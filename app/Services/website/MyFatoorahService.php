<?php

namespace App\Services\website;

use App\Models\Dashboard\Order;
use App\Models\Dashboard\Transaction;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MyFatoorahService
{
    public function createInvoice(Order $order): array
    {
        $token = config('services.myfatoorah.token');
        if (! $token || (float) $order->total_price <= 0) {
            throw new RuntimeException('Payment configuration or order amount is invalid.');
        }

        $paymentExpiresAt = $order->payment_expires_at;
        if ($paymentExpiresAt && $paymentExpiresAt->lessThanOrEqualTo(now())) {
            throw new RuntimeException('The payment deadline has expired.');
        }

        // نرسل الإجمالي المحفوظ في الطلب، وليس قيمة قادمة من المتصفح.
        $payload = [
            'Order' => [
                'Amount' => (float) $order->total_price,
                'Currency' => config('services.myfatoorah.currency'),
            ],
            'NotificationOption' => 'LINK',
        ];

        if ($paymentExpiresAt) {
            // نرسل الموعد المحفوظ بتوقيت UTC، بدون بدء مهلة جديدة أو تغيير وقت الطلب.
            $payload['PaymentExpiry'] = $paymentExpiresAt->copy()->utc()->toIso8601ZuluString();
        }

        $callbackUrl = config('services.myfatoorah.callback_url');

        if ($callbackUrl) {
            $payload['IntegrationUrls'] = [
                'Redirection' => $callbackUrl,
            ];
        }

        // لا نكرر الطلب تلقائيًا حتى لا ننشئ فاتورتين عند انقطاع الاتصال.
        $response = Http::withToken($token)->acceptJson()
            ->connectTimeout(10)->timeout(30)
            ->post(rtrim(config('services.myfatoorah.base_url'), '/').'/v3/payments', $payload);

        $invoiceId = $response->json('Data.InvoiceId');
        $paymentUrl = $response->json('Data.PaymentURL');

        if (! $response->successful() || $response->json('IsSuccess') !== true
            || ! is_scalar($invoiceId) || (string) $invoiceId === ''
            || ! is_string($paymentUrl) || ! filter_var($paymentUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('MyFatoorah did not return a valid invoice.');
        }

        // رقم الفاتورة ليس رقم معاملة الدفع، ولا يعني أن الطلب أصبح مدفوعًا.
        Transaction::create([
            'order_id' => $order->id,
            'invoice_id' => (string) $invoiceId,
            'payment_method' => 'myfatoorah',
            // نحفظ الموعد الذي أرسلناه مع الفاتورة المقبولة، حتى لا نفترضه للفاتورات القديمة.
            'invoice_expires_at' => $paymentExpiresAt,
        ]);

        return ['invoice_id' => (string) $invoiceId, 'payment_url' => $paymentUrl];
    }

    // استعلام فقط: نستخدم رقم الفاتورة حتى لو لم يبدأ العميل أي محاولة دفع.
    public function getInvoiceStatus(string $invoiceId): array
    {
        $token = config('services.myfatoorah.token');
        if (! $token || ! ctype_digit($invoiceId)) {
            throw new RuntimeException('Payment configuration or invoice ID is invalid.');
        }

        $response = Http::withToken($token)->acceptJson()->connectTimeout(10)->timeout(30)
            ->post(rtrim(config('services.myfatoorah.base_url'), '/').'/v2/GetPaymentStatus', [
                'Key' => $invoiceId,
                'KeyType' => 'InvoiceId',
            ]);

        $data = $response->json('Data');
        if (! $response->successful() || $response->json('IsSuccess') !== true
            || ! is_array($data) || ! is_scalar($data['InvoiceId'] ?? null)
            || (string) $data['InvoiceId'] !== $invoiceId
            || ! is_string($data['InvoiceStatus'] ?? null) || $data['InvoiceStatus'] === ''
            || ! is_array($data['InvoiceTransactions'] ?? null)) {
            throw new RuntimeException('Failed to retrieve a valid invoice status.');
        }

        // نعيد بيانات البوابة كما هي؛ قرار اعتماد الدفع أو تحرير الكوبون يأتي لاحقًا.
        return $response->json();
    }

    // نقرأ رد الاستعلام فقط؛ لا نعتمد الطلب أو نحرر الكوبون داخل هذه المثود.
    public function getInvoicePaymentResult(array $invoiceStatus): array
    {
        $unknown = ['status' => 'unknown', 'payment_id' => null];
        $data = $invoiceStatus['Data'] ?? null;

        if (($invoiceStatus['IsSuccess'] ?? null) !== true || ! is_array($data)
            || ! in_array($data['InvoiceStatus'] ?? null, ['Pending', 'Paid', 'Canceled'], true)
            || ! is_array($data['InvoiceTransactions'] ?? null)
            || ! array_is_list($data['InvoiceTransactions'])) {
            return $unknown;
        }

        $hasSuccessfulAttempt = false;
        $successfulPaymentId = null;
        $hasProcessingAttempt = false;
        $hasUnknownAttempt = false;

        // نفحص كل المحاولات؛ فشل محاولة قد يتبعه نجاح محاولة أخرى.
        foreach ($data['InvoiceTransactions'] as $transaction) {
            if (! is_array($transaction)) {
                $hasUnknownAttempt = true;
                continue;
            }

            $status = $transaction['TransactionStatus'] ?? null;
            if ($status === 'Succss') {
                // هذا هو اسم حالة النجاح كما ترسله واجهة MyFatoorah v2.
                $hasSuccessfulAttempt = true;
                $paymentId = $transaction['PaymentId'] ?? null;
                if (is_string($paymentId) && ctype_digit($paymentId)) {
                    $successfulPaymentId ??= $paymentId;
                }
            } elseif (in_array($status, ['InProgress', 'Authorize'], true)) {
                $hasProcessingAttempt = true;
            } elseif (! in_array($status, ['Failed', 'Canceled'], true)) {
                $hasUnknownAttempt = true;
            }
        }

        if ($hasSuccessfulAttempt) {
            // نعيد رقم محاولة النجاح للتحقق من مبلغها وعملتها عبر الاستعلام الحالي v3.
            return $data['InvoiceStatus'] === 'Paid' && $successfulPaymentId !== null
                ? ['status' => 'paid', 'payment_id' => $successfulPaymentId]
                : $unknown;
        }

        if ($data['InvoiceStatus'] === 'Paid') {
            return $unknown;
        }

        if ($hasProcessingAttempt) {
            return $data['InvoiceStatus'] === 'Pending'
                ? ['status' => 'processing', 'payment_id' => null]
                : $unknown;
        }

        if ($hasUnknownAttempt) {
            return $unknown;
        }

        // غير مدفوع في هذا الرد؛ تحرير الحجز يحتاج فحص انتهاء مهلة الدفع أيضًا.
        return ['status' => 'unpaid', 'payment_id' => null];
    }

    public function getPaymentStatus(string $paymentId): array
    {
        $token = config('services.myfatoorah.token');
        if (! $token || ! ctype_digit($paymentId)) {
            throw new RuntimeException('Payment configuration or payment ID is invalid.');
        }

        $response = Http::withToken($token)->acceptJson()->connectTimeout(10)->timeout(30)
            ->get(rtrim(config('services.myfatoorah.base_url'), '/')."/v3/payments/{$paymentId}");

        if (! $response->successful() || $response->json('IsSuccess') !== true) {
            throw new RuntimeException('Failed to retrieve payment status.');
        }

        return $response->json();
    }

    public function isPaymentSuccessful(array $paymentStatus): array|false
    {
        if (isset($paymentStatus['IsSuccess']) && $paymentStatus['IsSuccess'] === true
               && isset($paymentStatus['Data']['Invoice']['Status']) && $paymentStatus['Data']['Invoice']['Status'] === 'PAID'
               && ($paymentStatus['Data']['Transaction']['Status'] ?? null) === 'SUCCESS'
               && isset($paymentStatus['Data']['Invoice']['Id']) && is_scalar($paymentStatus['Data']['Invoice']['Id'])
               && isset($paymentStatus['Data']['Transaction']['Id']) && is_scalar($paymentStatus['Data']['Transaction']['Id'])) {
                
            return [    
                'invoice_id' => (string) $paymentStatus['Data']['Invoice']['Id'],
                'transaction_id' => (string) $paymentStatus['Data']['Transaction']['Id'],
                'amount' => $paymentStatus['Data']['Amount']['ValueInDisplayCurrency'] ?? null,
                'currency' => $paymentStatus['Data']['Amount']['DisplayCurrency'] ?? null,
            ];
        }
        return false;
    }

    public function getFailedPayment(array $paymentStatus): array|false
    {
        if (($paymentStatus['IsSuccess'] ?? null) !== true
            || ($paymentStatus['Data']['Invoice']['Status'] ?? null) !== 'PENDING'
            || ! in_array($paymentStatus['Data']['Transaction']['Status'] ?? null, ['FAILED', 'CANCELED'], true)
            || ! isset($paymentStatus['Data']['Invoice']['Id'])
            || ! is_scalar($paymentStatus['Data']['Invoice']['Id'])
            || (string) $paymentStatus['Data']['Invoice']['Id'] === '') {
            return false;
        }

        return [
            'invoice_id' => (string) $paymentStatus['Data']['Invoice']['Id'],
            'amount' => $paymentStatus['Data']['Amount']['ValueInDisplayCurrency'] ?? null,
            'currency' => $paymentStatus['Data']['Amount']['DisplayCurrency'] ?? null,
        ];
    }
}
