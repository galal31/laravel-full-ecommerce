<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dashboard\Faq; // تأكد من مسار الموديل الخاص بك

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = [
            [
                'question' => [
                    'ar' => 'كيف يمكنني تتبع طلبي؟',
                    'en' => 'How can I track my order?'
                ],
                'answer' => [
                    'ar' => 'يمكنك تتبع طلبك من خلال الدخول إلى حسابك والذهاب إلى قسم "طلباتي"، ثم النقر على "تتبع الطلب" بجانب الطلب المراد تتبعه.',
                    'en' => 'You can track your order by logging into your account, navigating to the "My Orders" section, and clicking on "Track Order" next to your recent purchase.'
                ]
            ],
            [
                'question' => [
                    'ar' => 'ما هي سياسة الاسترجاع والاستبدال؟',
                    'en' => 'What is the return and exchange policy?'
                ],
                'answer' => [
                    'ar' => 'نوفر سياسة استرجاع واستبدال مرنة خلال 14 يوماً من تاريخ استلام الطلب، بشرط أن يكون المنتج في حالته الأصلية وغير مستخدم.',
                    'en' => 'We offer a flexible return and exchange policy within 14 days of receiving the order, provided the product is in its original condition and unused.'
                ]
            ],
            [
                'question' => [
                    'ar' => 'هل تتوفر خدمة الدفع عند الاستلام؟',
                    'en' => 'Is Cash on Delivery (COD) available?'
                ],
                'answer' => [
                    'ar' => 'نعم، نوفر خدمة الدفع عند الاستلام في معظم المناطق داخل الدولة. سيتم عرض الخيار لك أثناء إتمام عملية الشراء إذا كانت منطقتك مدعومة.',
                    'en' => 'Yes, Cash on Delivery (COD) is available in most regions. The option will be displayed during checkout if your area is supported.'
                ]
            ],
            [
                'question' => [
                    'ar' => 'كم تستغرق عملية التوصيل؟',
                    'en' => 'How long does delivery take?'
                ],
                'answer' => [
                    'ar' => 'عادةً ما تستغرق عملية التوصيل من 2 إلى 5 أيام عمل، وقد تختلف المدة قليلاً حسب موقعك الجغرافي.',
                    'en' => 'Delivery usually takes 2 to 5 business days. The duration may vary slightly depending on your exact location.'
                ]
            ],
            [
                'question' => [
                    'ar' => 'كيف يمكنني التواصل مع خدمة العملاء؟',
                    'en' => 'How can I contact customer service?'
                ],
                'answer' => [
                    'ar' => 'يمكنك التواصل معنا عبر صفحة "اتصل بنا"، أو من خلال البريد الإلكتروني support@example.com، أو عبر أرقام الهواتف الموضحة في أسفل الموقع.',
                    'en' => 'You can contact us via the "Contact Us" page, by email at support@example.com, or through the phone numbers listed at the footer of the website.'
                ]
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}