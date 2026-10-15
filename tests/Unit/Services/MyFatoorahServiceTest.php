<?php

namespace Tests\Unit\Services;

use App\Models\Dashboard\Order;
use App\Services\website\MyFatoorahService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MyFatoorahServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.myfatoorah.token' => 'test-token']);
        Http::preventStrayRequests();
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('invoice_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method');
            $table->dateTime('invoice_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_sends_saved_total_and_stores_invoice_for_the_order(): void
    {
        $this->travelTo(Carbon::parse('2026-10-11 10:02:00', 'UTC'));
        Http::fake(['*/v3/payments' => Http::response([
            'IsSuccess' => true,
            'Data' => ['InvoiceId' => '12345', 'PaymentURL' => 'https://demo.myfatoorah.com/pay/12345'],
        ], 201)]);
        $order = new Order([
            'total_price' => 275.50, 'status' => 'pending',
            'payment_expires_at' => '2026-10-11 10:30:00',
        ]);
        $order->id = 42;

        $result = app(MyFatoorahService::class)->createInvoice($order);

        $this->assertSame('12345', $result['invoice_id']);
        $this->assertDatabaseHas('transactions', [
            'order_id' => 42, 'invoice_id' => '12345',
            'transaction_id' => null, 'payment_method' => 'myfatoorah',
            'invoice_expires_at' => '2026-10-11 10:30:00',
        ]);
        $this->assertSame('pending', $order->status);
        Http::assertSent(fn ($request) => $request['Order']['Amount'] === 275.50
            && $request['NotificationOption'] === 'LINK'
            && $request['PaymentExpiry'] === '2026-10-11T10:30:00Z');
        $this->assertSame('2026-10-11 10:30:00', $order->payment_expires_at->toDateTimeString());
        Http::assertSentCount(1);
    }

    public function test_it_converts_the_saved_deadline_to_utc_without_changing_it(): void
    {
        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('Africa/Cairo');

        try {
            $this->travelTo(Carbon::parse('2026-10-11 13:02:00', 'Africa/Cairo'));
            Http::fake(['*/v3/payments' => Http::response([
                'IsSuccess' => true,
                'Data' => ['InvoiceId' => '12345', 'PaymentURL' => 'https://demo.myfatoorah.com/pay/12345'],
            ], 201)]);
            $order = new Order(['total_price' => 100, 'payment_expires_at' => '2026-10-11 13:30:00']);
            $order->id = 42;

            app(MyFatoorahService::class)->createInvoice($order);

            Http::assertSent(fn ($request) => $request['PaymentExpiry'] === '2026-10-11T10:30:00Z');
            $this->assertSame('2026-10-11 13:30:00', $order->payment_expires_at->toDateTimeString());
            $this->assertSame('Africa/Cairo', $order->payment_expires_at->timezoneName);
        } finally {
            date_default_timezone_set($previousTimezone);
        }
    }

    #[DataProvider('expiredPaymentDeadlines')]
    public function test_an_expired_deadline_does_not_create_an_invoice(string $deadline): void
    {
        $this->travelTo(Carbon::parse('2026-10-11 10:30:00', 'UTC'));
        Http::fake();
        $order = new Order(['total_price' => 100, 'payment_expires_at' => $deadline]);
        $order->id = 42;

        try {
            app(MyFatoorahService::class)->createInvoice($order);
            $this->fail('An expired payment deadline must prevent invoice creation.');
        } catch (RuntimeException $e) {
            $this->assertSame('The payment deadline has expired.', $e->getMessage());
            Http::assertNothingSent();
            $this->assertSame(0, DB::table('transactions')->count());
        }
    }

    public static function expiredPaymentDeadlines(): array
    {
        return [
            'before current time' => ['2026-10-11 10:29:59'],
            'equal to current time' => ['2026-10-11 10:30:00'],
        ];
    }

    public function test_rejected_or_incomplete_responses_do_not_create_transactions(): void
    {
        $order = new Order(['total_price' => 100]);
        $order->id = 42;
        foreach ([
            ['IsSuccess' => false],
            ['IsSuccess' => true, 'Data' => ['PaymentURL' => 'https://demo.myfatoorah.com/pay']],
        ] as $response) {
            Http::fake(['*/v3/payments' => Http::response($response)]);
            try {
                app(MyFatoorahService::class)->createInvoice($order);
                $this->fail('Invalid invoice must be rejected.');
            } catch (RuntimeException $e) {
                $this->assertSame(0, DB::table('transactions')->count());
            }
        }
    }

    public function test_it_accepts_an_http_payment_link(): void
    {
        Http::fake(['*/v3/payments' => Http::response([
            'IsSuccess' => true,
            'Data' => ['InvoiceId' => '12345', 'PaymentURL' => 'http://demo.myfatoorah.com/pay/12345'],
        ], 201)]);
        $order = new Order(['total_price' => 100]);
        $order->id = 42;

        $result = app(MyFatoorahService::class)->createInvoice($order);

        $this->assertSame('http://demo.myfatoorah.com/pay/12345', $result['payment_url']);
        $this->assertDatabaseHas('transactions', ['order_id' => 42, 'invoice_id' => '12345']);
        Http::assertSent(fn ($request) => ! isset($request['PaymentExpiry']));
    }

    public function test_connection_failure_does_not_retry_or_store_an_invoice(): void
    {
        $attempts = 0;
        Http::fake(function () use (&$attempts) {
            $attempts++;
            throw new ConnectionException('Connection failed.');
        });
        $order = new Order(['total_price' => 100]);
        $order->id = 42;
        try {
            app(MyFatoorahService::class)->createInvoice($order);
            $this->fail('Connection failure must propagate.');
        } catch (ConnectionException $e) {
            $this->assertSame(1, $attempts);
            $this->assertSame(0, DB::table('transactions')->count());
        }
    }

    #[DataProvider('invoiceStatuses')]
    public function test_it_queries_the_invoice_by_id_and_returns_its_data(string $status, array $transactions): void
    {
        config(['services.myfatoorah.base_url' => 'https://apitest.myfatoorah.com/']);
        $expected = [
            'IsSuccess' => true,
            'Data' => [
                'InvoiceId' => 12345, 'InvoiceStatus' => $status,
                'ExpiryDate' => 'October 11, 2026', 'ExpiryTime' => '10:30:00.000',
                'InvoiceTransactions' => $transactions,
            ],
        ];
        Http::fake(['*/v2/GetPaymentStatus' => Http::response($expected)]);

        $result = app(MyFatoorahService::class)->getInvoiceStatus('12345');

        $this->assertSame($expected, $result);
        Http::assertSent(fn ($request) => $request->url() === 'https://apitest.myfatoorah.com/v2/GetPaymentStatus'
            && $request->method() === 'POST'
            && $request['Key'] === '12345'
            && $request['KeyType'] === 'InvoiceId'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('Accept', 'application/json'));
        Http::assertSentCount(1);
        $this->assertDatabaseCount('transactions', 0);
    }

    public static function invoiceStatuses(): array
    {
        return [
            'no attempts' => ['Pending', []],
            'paid after an earlier failure' => ['Paid', [
                ['PaymentId' => '1001', 'TransactionStatus' => 'Failed'],
                ['PaymentId' => '1002', 'TransactionStatus' => 'Succss'],
            ]],
            'in progress' => ['Pending', [['PaymentId' => '1003', 'TransactionStatus' => 'InProgress']]],
            'unknown status remains unchanged' => ['Unknown', []],
        ];
    }

    #[DataProvider('invalidInvoiceResponses')]
    public function test_an_invalid_invoice_response_is_not_returned_as_valid_data(array $body, int $httpStatus): void
    {
        Http::fake(['*/v2/GetPaymentStatus' => Http::response($body, $httpStatus)]);

        try {
            app(MyFatoorahService::class)->getInvoiceStatus('12345');
            $this->fail('An invalid invoice response must not be accepted.');
        } catch (RuntimeException $e) {
            $this->assertSame('Failed to retrieve a valid invoice status.', $e->getMessage());
            $this->assertDatabaseCount('transactions', 0);
        }
    }

    public static function invalidInvoiceResponses(): array
    {
        $data = ['InvoiceId' => 12345, 'InvoiceStatus' => 'Pending', 'InvoiceTransactions' => []];

        return [
            'HTTP failure' => [['IsSuccess' => true, 'Data' => $data], 500],
            'gateway rejection' => [['IsSuccess' => false, 'Message' => 'No invoices match this InvoiceId'], 200],
            'missing data' => [['IsSuccess' => true], 200],
            'wrong invoice' => [['IsSuccess' => true, 'Data' => [...$data, 'InvoiceId' => 99999]], 200],
            'invalid invoice ID type' => [['IsSuccess' => true, 'Data' => [...$data, 'InvoiceId' => []]], 200],
            'missing status' => [['IsSuccess' => true, 'Data' => [...$data, 'InvoiceStatus' => null]], 200],
            'missing transactions' => [['IsSuccess' => true, 'Data' => [...$data, 'InvoiceTransactions' => null]], 200],
        ];
    }

    #[DataProvider('invalidInvoiceIds')]
    public function test_an_invalid_invoice_id_does_not_send_a_request(string $invoiceId): void
    {
        Http::fake();

        try {
            app(MyFatoorahService::class)->getInvoiceStatus($invoiceId);
            $this->fail('An invalid invoice ID must not be sent to the gateway.');
        } catch (RuntimeException $e) {
            $this->assertSame('Payment configuration or invoice ID is invalid.', $e->getMessage());
            Http::assertNothingSent();
            $this->assertDatabaseCount('transactions', 0);
        }
    }

    public static function invalidInvoiceIds(): array
    {
        return [[''], ['abc'], ['123/other']];
    }

    public function test_missing_token_does_not_send_an_invoice_status_request(): void
    {
        config(['services.myfatoorah.token' => null]);
        Http::fake();

        try {
            app(MyFatoorahService::class)->getInvoiceStatus('12345');
            $this->fail('Missing configuration must prevent invoice lookup.');
        } catch (RuntimeException $e) {
            Http::assertNothingSent();
            $this->assertDatabaseCount('transactions', 0);
        }
    }

    public function test_invoice_lookup_connection_failure_propagates_without_changing_transactions(): void
    {
        $attempts = 0;
        Http::fake(function () use (&$attempts) {
            $attempts++;
            throw new ConnectionException('Connection failed.');
        });

        try {
            app(MyFatoorahService::class)->getInvoiceStatus('12345');
            $this->fail('An unknown payment result must remain a connection error.');
        } catch (ConnectionException $e) {
            $this->assertSame(1, $attempts);
            $this->assertDatabaseCount('transactions', 0);
        }
    }

    #[DataProvider('invoicePaymentResults')]
    public function test_it_reads_all_attempts_without_applying_payment_effects(
        string $invoiceStatus, array $transactions, string $expectedStatus, ?string $paymentId
    ): void {
        Http::fake();
        $response = [
            'IsSuccess' => true,
            'Data' => ['InvoiceStatus' => $invoiceStatus, 'InvoiceTransactions' => $transactions],
        ];

        $result = app(MyFatoorahService::class)->getInvoicePaymentResult($response);

        $this->assertSame(['status' => $expectedStatus, 'payment_id' => $paymentId], $result);
        Http::assertNothingSent();
        $this->assertDatabaseCount('transactions', 0);
    }

    public static function invoicePaymentResults(): array
    {
        $success = ['TransactionStatus' => 'Succss', 'PaymentId' => '001002'];
        $failed = ['TransactionStatus' => 'Failed', 'PaymentId' => '1001'];
        $canceled = ['TransactionStatus' => 'Canceled'];
        $processing = ['TransactionStatus' => 'InProgress'];
        $authorized = ['TransactionStatus' => 'Authorize'];
        $unknown = ['TransactionStatus' => 'Unexpected'];

        return [
            'no payment attempts' => ['Pending', [], 'unpaid', null],
            'failed attempt' => ['Pending', [$failed], 'unpaid', null],
            'canceled attempt' => ['Pending', [$canceled], 'unpaid', null],
            'all attempts failed or canceled' => ['Pending', [$failed, $canceled], 'unpaid', null],
            'canceled invoice without attempts' => ['Canceled', [], 'unpaid', null],
            'canceled invoice after failure' => ['Canceled', [$failed], 'unpaid', null],
            'payment still running' => ['Pending', [$processing], 'processing', null],
            'authorized is not captured payment' => ['Pending', [$authorized], 'processing', null],
            'failure followed by processing' => ['Pending', [$failed, $processing], 'processing', null],
            'processing followed by failure' => ['Pending', [$processing, $failed], 'processing', null],
            'successful payment' => ['Paid', [$success], 'paid', '001002'],
            'failed then successful' => ['Paid', [$failed, $success], 'paid', '001002'],
            'successful then failed' => ['Paid', [$success, $failed], 'paid', '001002'],
            'successful with older processing attempt' => ['Paid', [$processing, $success], 'paid', '001002'],
            'successful with unknown other attempt' => ['Paid', [$unknown, $success], 'paid', '001002'],
            'success without payment ID' => ['Paid', [['TransactionStatus' => 'Succss']], 'unknown', null],
            'success with invalid payment ID' => ['Paid', [['TransactionStatus' => 'Succss', 'PaymentId' => '123/path']], 'unknown', null],
            'success with invalid payment ID type' => ['Paid', [['TransactionStatus' => 'Succss', 'PaymentId' => []]], 'unknown', null],
            'another success has a usable payment ID' => ['Paid', [['TransactionStatus' => 'Succss'], $success], 'paid', '001002'],
            'paid without attempts' => ['Paid', [], 'unknown', null],
            'paid but only failed attempts' => ['Paid', [$failed], 'unknown', null],
            'pending conflicts with success' => ['Pending', [$success], 'unknown', null],
            'canceled conflicts with success' => ['Canceled', [$success], 'unknown', null],
            'canceled conflicts with processing' => ['Canceled', [$processing], 'unknown', null],
            'unrecognized invoice status' => ['Unexpected', [], 'unknown', null],
            'unrecognized attempt status' => ['Pending', [$unknown], 'unknown', null],
            'failed attempt beside unknown attempt' => ['Pending', [$failed, $unknown], 'unknown', null],
            'missing attempt status' => ['Pending', [[]], 'unknown', null],
            'invalid attempt type' => ['Pending', [null], 'unknown', null],
            'invalid list shape' => ['Pending', ['attempt' => $failed], 'unknown', null],
        ];
    }

    #[DataProvider('unreadableInvoicePaymentResults')]
    public function test_incomplete_or_rejected_results_remain_unknown(array $response): void
    {
        $this->assertSame(
            ['status' => 'unknown', 'payment_id' => null],
            app(MyFatoorahService::class)->getInvoicePaymentResult($response)
        );
    }

    public static function unreadableInvoicePaymentResults(): array
    {
        $data = ['InvoiceStatus' => 'Pending', 'InvoiceTransactions' => []];

        return [
            'gateway rejection' => [['IsSuccess' => false, 'Data' => $data]],
            'missing API success' => [['Data' => $data]],
            'missing data' => [['IsSuccess' => true]],
            'invalid data type' => [['IsSuccess' => true, 'Data' => 'invalid']],
            'missing invoice status' => [['IsSuccess' => true, 'Data' => ['InvoiceTransactions' => []]]],
            'missing transactions' => [['IsSuccess' => true, 'Data' => ['InvoiceStatus' => 'Pending']]],
            'invalid transactions type' => [['IsSuccess' => true, 'Data' => [...$data, 'InvoiceTransactions' => null]]],
        ];
    }

    public function test_missing_token_does_not_send_a_request(): void
    {
        config(['services.myfatoorah.token' => null]);
        Http::fake();
        try {
            app(MyFatoorahService::class)->createInvoice(new Order(['total_price' => 100]));
            $this->fail('Missing configuration must be rejected.');
        } catch (RuntimeException $e) {
            Http::assertNothingSent();
            $this->assertSame(0, DB::table('transactions')->count());
        }
    }
}
