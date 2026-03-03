<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class WorldSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $locations = [
            // ==========================================
            // 1. EGYPT (مصر)
            // ==========================================
            [
                'name' => ['en' => 'Egypt', 'ar' => 'مصر'],
                'phone_code' => '+20',
                'governorates' => [
                    ['name' => ['en' => 'Cairo', 'ar' => 'القاهرة'], 'price' => 50, 'cities' => [['en' => 'Cairo', 'ar' => 'القاهرة'], ['en' => 'Nasr City', 'ar' => 'مدينة نصر'], ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة']]],
                    ['name' => ['en' => 'Giza', 'ar' => 'الجيزة'], 'price' => 50, 'cities' => [['en' => 'Giza', 'ar' => 'الجيزة'], ['en' => '6th of October', 'ar' => 'السادس من أكتوبر'], ['en' => 'Sheikh Zayed', 'ar' => 'الشيخ زايد']]],
                    ['name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية'], 'price' => 60, 'cities' => [['en' => 'Alexandria', 'ar' => 'الإسكندرية'], ['en' => 'Borg El Arab', 'ar' => 'برج العرب']]],
                    ['name' => ['en' => 'Dakahlia', 'ar' => 'الدقهلية'], 'price' => 65, 'cities' => [['en' => 'Mansoura', 'ar' => 'المنصورة'], ['en' => 'Talkha', 'ar' => 'طلخا']]],
                ]
            ],
            // ==========================================
            // 2. SAUDI ARABIA (السعودية)
            // ==========================================
            [
                'name' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
                'phone_code' => '+966',
                'governorates' => [
                    ['name' => ['en' => 'Riyadh Region', 'ar' => 'منطقة الرياض'], 'price' => 300, 'cities' => [['en' => 'Riyadh', 'ar' => 'الرياض'], ['en' => 'Diriyah', 'ar' => 'الدرعية'], ['en' => 'Al Kharj', 'ar' => 'الخرج']]],
                    ['name' => ['en' => 'Makkah Region', 'ar' => 'منطقة مكة المكرمة'], 'price' => 350, 'cities' => [['en' => 'Makkah', 'ar' => 'مكة المكرمة'], ['en' => 'Jeddah', 'ar' => 'جدة'], ['en' => 'Taif', 'ar' => 'الطائف']]],
                    ['name' => ['en' => 'Eastern Province', 'ar' => 'المنطقة الشرقية'], 'price' => 350, 'cities' => [['en' => 'Dammam', 'ar' => 'الدمام'], ['en' => 'Khobar', 'ar' => 'الخبر'], ['en' => 'Jubail', 'ar' => 'الجبيل']]],
                    ['name' => ['en' => 'Al Madinah Region', 'ar' => 'منطقة المدينة المنورة'], 'price' => 350, 'cities' => [['en' => 'Madinah', 'ar' => 'المدينة المنورة'], ['en' => 'Yanbu', 'ar' => 'ينبع'], ['en' => 'Al Ula', 'ar' => 'العلا']]],
                    ['name' => ['en' => 'Asir Region', 'ar' => 'منطقة عسير'], 'price' => 400, 'cities' => [['en' => 'Abha', 'ar' => 'أبها'], ['en' => 'Khamis Mushait', 'ar' => 'خميس مشيط']]],
                ]
            ],
            // ==========================================
            // 3. UNITED ARAB EMIRATES (الإمارات)
            // ==========================================
            [
                'name' => ['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة'],
                'phone_code' => '+971',
                'governorates' => [
                    ['name' => ['en' => 'Abu Dhabi', 'ar' => 'أبو ظبي'], 'price' => 250, 'cities' => [['en' => 'Abu Dhabi', 'ar' => 'أبو ظبي'], ['en' => 'Al Ain', 'ar' => 'العين']]],
                    ['name' => ['en' => 'Dubai', 'ar' => 'دبي'], 'price' => 250, 'cities' => [['en' => 'Dubai', 'ar' => 'دبي'], ['en' => 'Jebel Ali', 'ar' => 'جبل علي']]],
                    ['name' => ['en' => 'Sharjah', 'ar' => 'الشارقة'], 'price' => 200, 'cities' => [['en' => 'Sharjah', 'ar' => 'الشارقة'], ['en' => 'Khor Fakkan', 'ar' => 'خورفكان']]],
                    ['name' => ['en' => 'Ajman', 'ar' => 'عجمان'], 'price' => 200, 'cities' => [['en' => 'Ajman', 'ar' => 'عجمان']]],
                ]
            ],
            // ==========================================
            // 4. KUWAIT (الكويت)
            // ==========================================
            [
                'name' => ['en' => 'Kuwait', 'ar' => 'الكويت'],
                'phone_code' => '+965',
                'governorates' => [
                    ['name' => ['en' => 'Al Asimah', 'ar' => 'العاصمة'], 'price' => 400, 'cities' => [['en' => 'Kuwait City', 'ar' => 'مدينة الكويت'], ['en' => 'Shuwaikh', 'ar' => 'الشويخ']]],
                    ['name' => ['en' => 'Hawalli', 'ar' => 'حولي'], 'price' => 400, 'cities' => [['en' => 'Hawalli', 'ar' => 'حولي'], ['en' => 'Salmiya', 'ar' => 'السالمية']]],
                    ['name' => ['en' => 'Al Farwaniyah', 'ar' => 'الفروانية'], 'price' => 450, 'cities' => [['en' => 'Farwaniyah', 'ar' => 'الفروانية'], ['en' => 'Khaitan', 'ar' => 'خيطان']]],
                    ['name' => ['en' => 'Al Ahmadi', 'ar' => 'الأحمدي'], 'price' => 500, 'cities' => [['en' => 'Ahmadi', 'ar' => 'الأحمدي'], ['en' => 'Fahaheel', 'ar' => 'الفحيحيل']]],
                ]
            ],
            // ==========================================
            // 5. QATAR (قطر)
            // ==========================================
            [
                'name' => ['en' => 'Qatar', 'ar' => 'قطر'],
                'phone_code' => '+974',
                'governorates' => [
                    ['name' => ['en' => 'Doha', 'ar' => 'الدوحة'], 'price' => 300, 'cities' => [['en' => 'Doha', 'ar' => 'الدوحة'], ['en' => 'The Pearl', 'ar' => 'اللؤلؤة']]],
                    ['name' => ['en' => 'Al Rayyan', 'ar' => 'الريان'], 'price' => 320, 'cities' => [['en' => 'Al Rayyan', 'ar' => 'الريان'], ['en' => 'Abu Hamour', 'ar' => 'أبو هامور']]],
                    ['name' => ['en' => 'Al Wakrah', 'ar' => 'الوكرة'], 'price' => 350, 'cities' => [['en' => 'Al Wakrah', 'ar' => 'الوكرة']]],
                ]
            ],
            // ==========================================
            // 6. BAHRAIN (البحرين)
            // ==========================================
            [
                'name' => ['en' => 'Bahrain', 'ar' => 'البحرين'],
                'phone_code' => '+973',
                'governorates' => [
                    ['name' => ['en' => 'Capital', 'ar' => 'العاصمة'], 'price' => 200, 'cities' => [['en' => 'Manama', 'ar' => 'المنامة'], ['en' => 'Juffair', 'ar' => 'الجفير']]],
                    ['name' => ['en' => 'Muharraq', 'ar' => 'المحرق'], 'price' => 250, 'cities' => [['en' => 'Muharraq', 'ar' => 'المحرق'], ['en' => 'Amwaj Islands', 'ar' => 'جزر أمواج']]],
                ]
            ],
            // ==========================================
            // 7. OMAN (عمان)
            // ==========================================
            [
                'name' => ['en' => 'Oman', 'ar' => 'سلطنة عمان'],
                'phone_code' => '+968',
                'governorates' => [
                    ['name' => ['en' => 'Muscat', 'ar' => 'مسقط'], 'price' => 350, 'cities' => [['en' => 'Muscat', 'ar' => 'مسقط'], ['en' => 'Seeb', 'ar' => 'السيب'], ['en' => 'Bawshar', 'ar' => 'بوشر']]],
                    ['name' => ['en' => 'Dhofar', 'ar' => 'ظفار'], 'price' => 500, 'cities' => [['en' => 'Salalah', 'ar' => 'صلالة']]],
                ]
            ],
            // ==========================================
            // 8. JORDAN (الأردن)
            // ==========================================
            [
                'name' => ['en' => 'Jordan', 'ar' => 'الأردن'],
                'phone_code' => '+962',
                'governorates' => [
                    ['name' => ['en' => 'Amman', 'ar' => 'عمان'], 'price' => 150, 'cities' => [['en' => 'Amman', 'ar' => 'عمان'], ['en' => 'Abdali', 'ar' => 'العبدلي']]],
                    ['name' => ['en' => 'Zarqa', 'ar' => 'الزرقاء'], 'price' => 170, 'cities' => [['en' => 'Zarqa', 'ar' => 'الزرقاء'], ['en' => 'Ruseifa', 'ar' => 'الرصيفة']]],
                    ['name' => ['en' => 'Irbid', 'ar' => 'إربد'], 'price' => 180, 'cities' => [['en' => 'Irbid', 'ar' => 'إربد'], ['en' => 'Ramtha', 'ar' => 'الرمثا']]],
                    ['name' => ['en' => 'Aqaba', 'ar' => 'العقبة'], 'price' => 200, 'cities' => [['en' => 'Aqaba', 'ar' => 'العقبة']]],
                ]
            ],
            // ==========================================
            // 9. PALESTINE (فلسطين)
            // ==========================================
            [
                'name' => ['en' => 'Palestine', 'ar' => 'فلسطين'],
                'phone_code' => '+970',
                'governorates' => [
                    ['name' => ['en' => 'Jerusalem', 'ar' => 'القدس'], 'price' => 100, 'cities' => [['en' => 'Jerusalem', 'ar' => 'القدس'], ['en' => 'Abu Dis', 'ar' => 'أبو ديس']]],
                    ['name' => ['en' => 'Ramallah and Al-Bireh', 'ar' => 'رام الله والبيرة'], 'price' => 150, 'cities' => [['en' => 'Ramallah', 'ar' => 'رام الله'], ['en' => 'Al-Bireh', 'ar' => 'البيرة']]],
                    ['name' => ['en' => 'Gaza', 'ar' => 'غزة'], 'price' => 150, 'cities' => [['en' => 'Gaza City', 'ar' => 'مدينة غزة'], ['en' => 'Jabalia', 'ar' => 'جباليا']]],
                    ['name' => ['en' => 'Hebron', 'ar' => 'الخليل'], 'price' => 180, 'cities' => [['en' => 'Hebron', 'ar' => 'الخليل'], ['en' => 'Halhul', 'ar' => 'حلحول']]],
                ]
            ],
            // ==========================================
            // 10. LEBANON (لبنان)
            // ==========================================
            [
                'name' => ['en' => 'Lebanon', 'ar' => 'لبنان'],
                'phone_code' => '+961',
                'governorates' => [
                    ['name' => ['en' => 'Beirut', 'ar' => 'بيروت'], 'price' => 150, 'cities' => [['en' => 'Beirut', 'ar' => 'بيروت'], ['en' => 'Hamra', 'ar' => 'الحمرا']]],
                    ['name' => ['en' => 'Mount Lebanon', 'ar' => 'جبل لبنان'], 'price' => 200, 'cities' => [['en' => 'Baabda', 'ar' => 'بعبدا'], ['en' => 'Jounieh', 'ar' => 'جونيه']]],
                    ['name' => ['en' => 'North Governorate', 'ar' => 'الشمال'], 'price' => 250, 'cities' => [['en' => 'Tripoli', 'ar' => 'طرابلس'], ['en' => 'Batroun', 'ar' => 'البترون']]],
                ]
            ],
            // ==========================================
            // 11. SYRIA (سوريا)
            // ==========================================
            [
                'name' => ['en' => 'Syria', 'ar' => 'سوريا'],
                'phone_code' => '+963',
                'governorates' => [
                    ['name' => ['en' => 'Damascus', 'ar' => 'دمشق'], 'price' => 100, 'cities' => [['en' => 'Damascus', 'ar' => 'دمشق']]],
                    ['name' => ['en' => 'Aleppo', 'ar' => 'حلب'], 'price' => 150, 'cities' => [['en' => 'Aleppo', 'ar' => 'حلب'], ['en' => 'Al-Bab', 'ar' => 'الباب']]],
                    ['name' => ['en' => 'Homs', 'ar' => 'حمص'], 'price' => 150, 'cities' => [['en' => 'Homs', 'ar' => 'حمص'], ['en' => 'Al-Rastan', 'ar' => 'الرستن']]],
                ]
            ],
            // ==========================================
            // 12. IRAQ (العراق)
            // ==========================================
            [
                'name' => ['en' => 'Iraq', 'ar' => 'العراق'],
                'phone_code' => '+964',
                'governorates' => [
                    ['name' => ['en' => 'Baghdad', 'ar' => 'بغداد'], 'price' => 200, 'cities' => [['en' => 'Baghdad', 'ar' => 'بغداد'], ['en' => 'Kadhimiya', 'ar' => 'الكاظمية']]],
                    ['name' => ['en' => 'Basra', 'ar' => 'البصرة'], 'price' => 250, 'cities' => [['en' => 'Basra', 'ar' => 'البصرة'], ['en' => 'Zubair', 'ar' => 'الزبير']]],
                    ['name' => ['en' => 'Erbil', 'ar' => 'أربيل'], 'price' => 250, 'cities' => [['en' => 'Erbil', 'ar' => 'أربيل'], ['en' => 'Soran', 'ar' => 'سوران']]],
                ]
            ],
            // ==========================================
            // 13. MOROCCO (المغرب)
            // ==========================================
            [
                'name' => ['en' => 'Morocco', 'ar' => 'المغرب'],
                'phone_code' => '+212',
                'governorates' => [
                    ['name' => ['en' => 'Casablanca-Settat', 'ar' => 'الدار البيضاء سطات'], 'price' => 300, 'cities' => [['en' => 'Casablanca', 'ar' => 'الدار البيضاء'], ['en' => 'Mohammedia', 'ar' => 'المحمدية']]],
                    ['name' => ['en' => 'Rabat-Salé-Kénitra', 'ar' => 'الرباط سلا القنيطرة'], 'price' => 300, 'cities' => [['en' => 'Rabat', 'ar' => 'الرباط'], ['en' => 'Salé', 'ar' => 'سلا']]],
                    ['name' => ['en' => 'Marrakech-Safi', 'ar' => 'مراكش آسفي'], 'price' => 350, 'cities' => [['en' => 'Marrakech', 'ar' => 'مراكش'], ['en' => 'Safi', 'ar' => 'آسفي']]],
                ]
            ],
            // ==========================================
            // 14. ALGERIA (الجزائر)
            // ==========================================
            [
                'name' => ['en' => 'Algeria', 'ar' => 'الجزائر'],
                'phone_code' => '+213',
                'governorates' => [
                    ['name' => ['en' => 'Algiers', 'ar' => 'الجزائر العاصمة'], 'price' => 250, 'cities' => [['en' => 'Algiers', 'ar' => 'الجزائر'], ['en' => 'Rouiba', 'ar' => 'الرويبة']]],
                    ['name' => ['en' => 'Oran', 'ar' => 'وهران'], 'price' => 300, 'cities' => [['en' => 'Oran', 'ar' => 'وهران'], ['en' => 'Arzew', 'ar' => 'أرزيو']]],
                    ['name' => ['en' => 'Constantine', 'ar' => 'قسنطينة'], 'price' => 300, 'cities' => [['en' => 'Constantine', 'ar' => 'قسنطينة'], ['en' => 'El Khroub', 'ar' => 'الخروب']]],
                ]
            ],
            // ==========================================
            // 15. TUNISIA (تونس)
            // ==========================================
            [
                'name' => ['en' => 'Tunisia', 'ar' => 'تونس'],
                'phone_code' => '+216',
                'governorates' => [
                    ['name' => ['en' => 'Tunis', 'ar' => 'تونس'], 'price' => 200, 'cities' => [['en' => 'Tunis', 'ar' => 'تونس'], ['en' => 'Carthage', 'ar' => 'قرطاج']]],
                    ['name' => ['en' => 'Sfax', 'ar' => 'صفاقس'], 'price' => 250, 'cities' => [['en' => 'Sfax', 'ar' => 'صفاقس'], ['en' => 'Sakiet Ezzit', 'ar' => 'ساقية الزيت']]],
                    ['name' => ['en' => 'Sousse', 'ar' => 'سوسة'], 'price' => 250, 'cities' => [['en' => 'Sousse', 'ar' => 'سوسة'], ['en' => 'Hammamet', 'ar' => 'الحمامات']]],
                ]
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($locations as $countryData) {
                // 1. Insert Country
                $countryId = DB::table('countries')->insertGetId([
                    'name' => json_encode($countryData['name'], JSON_UNESCAPED_UNICODE),
                    'phone_code' => $countryData['phone_code'],
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($countryData['governorates'] as $govData) {
                    // 2. Insert Governorate (بدون السعر)
                    $governorateId = DB::table('governorates')->insertGetId([
                        'country_id' => $countryId,
                        'name' => json_encode($govData['name'], JSON_UNESCAPED_UNICODE),
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // 3. Insert Shipping Price in 'shipping_governorates' table (تم التعديل هنا)
                    DB::table('shipping_governorates')->insert([
                        'governorate_id' => $governorateId,
                        'price' => $govData['price'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // 4. Prepare Cities for Bulk Insert
                    $citiesToInsert = [];
                    foreach ($govData['cities'] as $cityData) {
                        $citiesToInsert[] = [
                            'governorate_id' => $governorateId,
                            'name' => json_encode($cityData, JSON_UNESCAPED_UNICODE),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // 5. Insert cities in bulk
                    if (!empty($citiesToInsert)) {
                        DB::table('cities')->insert($citiesToInsert);
                    }
                }
            }

            DB::commit();
            $this->command->info('15 Major Arab Countries seeded successfully with their shipping prices!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}