<?php

namespace Database\Seeders;

use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\ElectronicCard;
use App\Models\Employee;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Notification;
use App\Models\Refuel;
use App\Models\SecurityLog;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // 1. OWNERS
        // ========================
        $stationOwner = User::where('phone', '770000002')->first();
        $carWashOwner = User::where('phone', '770000003')->first();
        $maintenanceOwner = User::where('phone', '770000004')->first();
        $stationWorker = User::where('phone', '770000005')->first();

        // ========================
        // 2. GAS STATIONS
        // ========================
        $stations = [
            ['station_name' => 'محطة الأمانة', 'location' => 'صنعاء، شارع الستين',       'latitude' => 15.3694, 'longitude' => 44.1910, 'station_code' => 'ST-001', 'commercial_register' => 'CR-ST-001'],
            ['station_name' => 'محطة النصر',   'location' => 'صنعاء، حي الروضة',         'latitude' => 15.3800, 'longitude' => 44.2100, 'station_code' => 'ST-002', 'commercial_register' => 'CR-ST-002'],
            ['station_name' => 'محطة الفجر',   'location' => 'عدن، المنصورة',            'latitude' => 12.7855, 'longitude' => 45.0187, 'station_code' => 'ST-003', 'commercial_register' => 'CR-ST-003'],
            ['station_name' => 'محطة الشرق',   'location' => 'تعز، شارع جمال',           'latitude' => 13.5772, 'longitude' => 44.0209, 'station_code' => 'ST-004', 'commercial_register' => 'CR-ST-004'],
            ['station_name' => 'محطة السلام',  'location' => 'صنعاء، شارع الزبيري',      'latitude' => 15.3550, 'longitude' => 44.2060, 'station_code' => 'ST-005', 'commercial_register' => 'CR-ST-005'],
        ];

        $createdStations = [];
        foreach ($stations as $st) {
            $createdStations[] = GasStation::firstOrCreate(
                ['station_code' => $st['station_code']],
                array_merge($st, ['owner_id' => $stationOwner->id, 'is_active' => true])
            );
        }

        // ========================
        // 3. CAR WASH CENTERS
        // ========================
        $carWashCenters = [
            ['center_name' => 'مغسلة الماسة',  'location' => 'صنعاء، حي السبعين',    'latitude' => 15.3700, 'longitude' => 44.1950, 'center_code' => 'CW-001', 'commercial_register' => 'CR-CW-001'],
            ['center_name' => 'مغسلة النجوم',  'location' => 'عدن، كريتر',          'latitude' => 12.7800, 'longitude' => 45.0250, 'center_code' => 'CW-002', 'commercial_register' => 'CR-CW-002'],
            ['center_name' => 'مغسلة الخليج',  'location' => 'صنعاء، الحصبة',       'latitude' => 15.3900, 'longitude' => 44.1800, 'center_code' => 'CW-003', 'commercial_register' => 'CR-CW-003'],
        ];

        $createdCarWashes = [];
        foreach ($carWashCenters as $cw) {
            $createdCarWashes[] = CarWashCenter::firstOrCreate(
                ['center_code' => $cw['center_code']],
                array_merge($cw, ['owner_id' => $carWashOwner->id, 'is_active' => true])
            );
        }

        // ========================
        // 4. MAINTENANCE CENTERS
        // ========================
        $maintenanceCenters = [
            ['center_name' => 'مركز الرائد للصيانة',  'location' => 'صنعاء، شارع تعز',  'latitude' => 15.3620, 'longitude' => 44.2000, 'center_code' => 'MC-001', 'commercial_register' => 'CR-MC-001', 'specialization' => 'صيانة عامة'],
            ['center_name' => 'مركز الخبراء',         'location' => 'عدن، دار سعد',     'latitude' => 12.8000, 'longitude' => 45.0100, 'center_code' => 'MC-002', 'commercial_register' => 'CR-MC-002', 'specialization' => 'كهرباء وميكانيك'],
        ];

        $createdMaintenance = [];
        foreach ($maintenanceCenters as $mc) {
            $createdMaintenance[] = MaintenanceCenter::firstOrCreate(
                ['center_code' => $mc['center_code']],
                array_merge($mc, ['owner_id' => $maintenanceOwner->id, 'is_active' => true])
            );
        }

        // ========================
        // 5. EMPLOYEES
        // ========================
        if (!Employee::where('user_id', $stationWorker->id)->exists()) {
            Employee::create([
                'user_id'       => $stationWorker->id,
                'station_id'    => $createdStations[0]->id,
                'employee_code' => 'EMP-001',
                'position'      => 'عامل محطة',
                'hire_date'     => now()->subMonths(6),
                'salary'        => 80000,
                'is_active'     => true,
            ]);
        }

        // ========================
        // 6. CUSTOMERS (Arabic names)
        // ========================
        $customers = [
            ['name' => 'أحمد محمد السلامي',   'phone' => '771100001', 'vehicle' => 'car',        'engine' => 'ENG001YM01'],
            ['name' => 'محمد علي الحميدي',    'phone' => '771100002', 'vehicle' => 'car',        'engine' => 'ENG002YM02'],
            ['name' => 'عبدالله حسن الأنسي',  'phone' => '771100003', 'vehicle' => 'truck',      'engine' => 'ENG003YM03'],
            ['name' => 'خالد عمر الشامي',     'phone' => '771100004', 'vehicle' => 'car',        'engine' => 'ENG004YM04'],
            ['name' => 'فيصل يوسف المقطري',   'phone' => '771100005', 'vehicle' => 'car',        'engine' => 'ENG005YM05'],
            ['name' => 'نور الدين القحطاني',  'phone' => '771100006', 'vehicle' => 'motorcycle', 'engine' => 'ENG006YM06'],
            ['name' => 'سامي عبدالرحمن',      'phone' => '771100007', 'vehicle' => 'car',        'engine' => 'ENG007YM07'],
            ['name' => 'ياسر الجرادي',        'phone' => '771100008', 'vehicle' => 'car',        'engine' => 'ENG008YM08'],
            ['name' => 'إبراهيم الصوفي',      'phone' => '771100009', 'vehicle' => 'truck',      'engine' => 'ENG009YM09'],
            ['name' => 'عمر العزي',           'phone' => '771100010', 'vehicle' => 'car',        'engine' => 'ENG010YM10'],
            ['name' => 'منير البكري',         'phone' => '771100011', 'vehicle' => 'car',        'engine' => 'ENG011YM11'],
            ['name' => 'طارق الوهابي',        'phone' => '771100012', 'vehicle' => 'car',        'engine' => 'ENG012YM12'],
        ];

        $createdCustomers = [];
        foreach ($customers as $c) {
            $user = User::firstOrCreate(
                ['phone' => $c['phone']],
                [
                    'full_name'       => $c['name'],
                    'password_hash'   => Hash::make('password'),
                    'vehicle_type'    => $c['vehicle'],
                    'engine_number'   => $c['engine'],
                    'user_role'       => 'customer',
                    'qr_code'         => 'QR-' . strtoupper(Str::random(10)),
                    'is_active'       => true,
                    'phone_verified'  => true,
                    'approval_status' => 'approved',
                ]
            );
            $createdCustomers[] = $user;
        }

        // 3 pending customers
        $pendingCustomers = [
            ['name' => 'راشد المحمدي',   'phone' => '771200001'],
            ['name' => 'بدر الزيدي',    'phone' => '771200002'],
            ['name' => 'حمزة الأمري',   'phone' => '771200003'],
        ];
        foreach ($pendingCustomers as $p) {
            User::firstOrCreate(
                ['phone' => $p['phone']],
                [
                    'full_name'       => $p['name'],
                    'password_hash'   => Hash::make('password'),
                    'vehicle_type'    => 'car',
                    'engine_number'   => 'ENG' . rand(100, 999),
                    'user_role'       => 'customer',
                    'qr_code'         => 'QR-' . strtoupper(Str::random(10)),
                    'is_active'       => false,
                    'phone_verified'  => true,
                    'approval_status' => 'pending',
                ]
            );
        }

        // ========================
        // 7. SUBSCRIPTIONS + REFUELS
        // ========================
        $plans = [
            'monthly'  => ['price' => 20,  'months' => 1,  'car_washes' => 0, 'maintenance' => 0],
            '3months'  => ['price' => 45,  'months' => 3,  'car_washes' => 1, 'maintenance' => 0],
            '6months'  => ['price' => 100, 'months' => 6,  'car_washes' => 3, 'maintenance' => 1],
            'yearly'   => ['price' => 195, 'months' => 12, 'car_washes' => 6, 'maintenance' => 1],
        ];
        $planKeys = array_keys($plans);

        foreach ($createdCustomers as $i => $customer) {
            if (Subscription::where('user_id', $customer->id)->exists()) {
                continue;
            }

            $planKey = $planKeys[$i % 4];
            $plan    = $plans[$planKey];
            $start   = now()->subDays(rand(5, 60));
            $liters  = rand(50, 600);

            $sub = Subscription::create([
                'user_id'               => $customer->id,
                'plan_type'             => $planKey,
                'price'                 => $plan['price'],
                'discount_percent'      => 0,
                'start_date'            => $start,
                'end_date'              => $start->copy()->addMonths($plan['months']),
                'status'                => 'active',
                'remaining_cylinders'   => 0,
                'remaining_car_washes'  => $plan['car_washes'],
                'remaining_maintenance' => $plan['maintenance'],
                'notes'                 => 'بيانات تجريبية',
                'monthly_liters'        => $liters,
                'last_reset_date'       => now()->startOfMonth(),
            ]);

            // Refuels history (3-8 per customer)
            $refuelCount = rand(3, 8);
            for ($r = 0; $r < $refuelCount; $r++) {
                $l = rand(20, 80);
                $ppl = rand(200, 350);
                $total = $l * $ppl;
                Refuel::create([
                    'user_id'              => $customer->id,
                    'station_id'           => $createdStations[array_rand($createdStations)]->id,
                    'subscription_id'      => $sub->id,
                    'employee_id'          => null,
                    'liters'               => $l,
                    'price_per_liter'      => $ppl,
                    'total_before_discount'=> $total,
                    'discount_amount'      => 0,
                    'final_price'          => $total,
                    'qr_code_used'         => $customer->qr_code,
                    'refuel_date'          => now()->subDays(rand(1, 30))->subHours(rand(0, 12)),
                ]);
            }

            // Electronic card for customers who crossed 500L
            if ($liters >= 500 && !ElectronicCard::where('user_id', $customer->id)->exists()) {
                ElectronicCard::create([
                    'user_id'                    => $customer->id,
                    'card_number'                => 'EC' . strtoupper(Str::random(10)),
                    'monthly_liters_at_generation'=> $liters,
                    'expires_at'                 => now()->addDays(30),
                    'generated_at'               => now()->subDays(rand(1, 5)),
                    'is_used'                    => false,
                ]);
            }

            // Car wash records
            if ($plan['car_washes'] > 0 && rand(0, 1)) {
                CarWash::create([
                    'user_id'        => $customer->id,
                    'center_id'      => $createdCarWashes[array_rand($createdCarWashes)]->id,
                    'subscription_id'=> $sub->id,
                    'employee_id'    => null,
                    'wash_type'      => ['غسيل خارجي', 'غسيل داخلي وخارجي', 'تلميع'][rand(0, 2)],
                    'qr_code_used'   => $customer->qr_code,
                    'wash_date'      => now()->subDays(rand(1, 20)),
                ]);
            }

            // Maintenance records
            if ($plan['maintenance'] > 0 && rand(0, 1)) {
                MaintenanceService::create([
                    'user_id'        => $customer->id,
                    'center_id'      => $createdMaintenance[array_rand($createdMaintenance)]->id,
                    'subscription_id'=> $sub->id,
                    'employee_id'    => null,
                    'service_type'   => ['تغيير زيت', 'فحص دوري', 'إصلاح كهربائي', 'فحص فرامل'][rand(0, 3)],
                    'description'    => 'خدمة صيانة دورية',
                    'cost'           => rand(5000, 30000),
                    'qr_code_used'   => $customer->qr_code,
                    'service_date'   => now()->subDays(rand(1, 25)),
                ]);
            }
        }

        // ========================
        // 8. SUPPORT TICKETS
        // ========================
        $ticketData = [
            ['title' => 'مشكلة في الاشتراك', 'desc' => 'لم يتم تفعيل اشتراكي بعد تحويل المبلغ منذ يومين، أرجو المساعدة.', 'priority' => 'high',   'status' => 'open'],
            ['title' => 'خطأ في QR Code',    'desc' => 'رمز QR الخاص بي لا يعمل عند المحطة وأتعذر عليّ التعبئة.',        'priority' => 'urgent', 'status' => 'in_progress'],
            ['title' => 'استفسار عن الباقات','desc' => 'ما الفرق بين باقة 3 أشهر وباقة 6 أشهر من حيث المزايا؟',          'priority' => 'normal', 'status' => 'resolved'],
            ['title' => 'طلب استرداد مبلغ',  'desc' => 'قمت بالدفع مرتين عن طريق الخطأ وأريد استرداد المبلغ الزائد.',    'priority' => 'high',   'status' => 'open'],
            ['title' => 'المحطة مغلقة',      'desc' => 'محطة الأمانة ظهرت نشطة في التطبيق لكنها كانت مغلقة فعلياً.',    'priority' => 'normal', 'status' => 'open'],
            ['title' => 'مشكلة تسجيل دخول', 'desc' => 'لا أستطيع تسجيل الدخول رغم إدخال البيانات الصحيحة.',             'priority' => 'urgent', 'status' => 'in_progress'],
            ['title' => 'طلب تغيير بيانات',  'desc' => 'أريد تحديث رقم محرك سيارتي بعد تغيير المحرك.',                  'priority' => 'low',    'status' => 'closed'],
        ];

        foreach ($ticketData as $i => $t) {
            $user = $createdCustomers[$i % count($createdCustomers)];
            if (!SupportTicket::where('title', $t['title'])->where('user_id', $user->id)->exists()) {
                SupportTicket::create([
                    'user_id'        => $user->id,
                    'title'          => $t['title'],
                    'description'    => $t['desc'],
                    'priority'       => $t['priority'],
                    'status'         => $t['status'],
                    'admin_response' => $t['status'] === 'resolved' ? 'تم الرد وحل المشكلة بنجاح، شكراً لتواصلك معنا.' : null,
                    'resolved_at'    => $t['status'] === 'resolved' ? now()->subDays(1) : null,
                    'created_at'     => now()->subDays(rand(1, 15)),
                ]);
            }
        }

        // ========================
        // 9. SECURITY LOGS
        // ========================
        $logTypes = ['refuel_qr_validation', 'failed_login', 'qr_session', 'account_locked'];

        foreach ($createdCustomers as $customer) {
            $count = rand(2, 5);
            for ($l = 0; $l < $count; $l++) {
                $success = rand(0, 4) > 0; // 80% success
                SecurityLog::create([
                    'user_id'      => $customer->id,
                    'log_type'     => $logTypes[array_rand($logTypes)],
                    'ip_address'   => rand(10, 200) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254),
                    'user_lat'     => 15.3694 + (rand(-100, 100) / 10000),
                    'user_lon'     => 44.1910 + (rand(-100, 100) / 10000),
                    'is_successful'=> $success,
                    'error_message'=> $success ? null : ['QR code mismatch', 'كلمة مرور خاطئة', 'انتهت صلاحية الجلسة'][rand(0, 2)],
                    'created_at'   => now()->subDays(rand(0, 14))->subHours(rand(0, 12)),
                ]);
            }
        }

        // ========================
        // 10. NOTIFICATIONS
        // ========================
        $adminUser = User::where('phone', '777777777')->first();

        $notifData = [
            ['title' => 'مرحباً بك في غازي!',          'msg' => 'تم تفعيل حسابك بنجاح. يمكنك الآن الاستمتاع بجميع مزايا التطبيق.', 'type' => 'welcome'],
            ['title' => 'تم تفعيل اشتراكك',            'msg' => 'مبروك! تم تفعيل اشتراكك الشهري بنجاح.',                            'type' => 'subscription_activated'],
            ['title' => 'كرت أولوية جديد!',             'msg' => 'تهانينا! لقد تجاوزت 500 لتر هذا الشهر وحصلت على كرت أولوية.',     'type' => 'priority_card_generated'],
            ['title' => 'تذكير بانتهاء الاشتراك',       'msg' => 'اشتراكك سينتهي خلال 7 أيام. قم بالتجديد للاستمرار في الخدمة.',   'type' => 'subscription_expiry'],
            ['title' => 'إشعار النظام',                 'msg' => 'سيكون النظام في وضع الصيانة غداً من 2 صباحاً حتى 4 صباحاً.',     'type' => 'system'],
        ];

        foreach ($notifData as $n) {
            foreach (array_slice($createdCustomers, 0, 5) as $customer) {
                if (!Notification::where('user_id', $customer->id)->where('title', $n['title'])->exists()) {
                    Notification::create([
                        'user_id'           => $customer->id,
                        'sender_id'         => $adminUser?->id,
                        'title'             => $n['title'],
                        'message'           => $n['msg'],
                        'notification_type' => $n['type'],
                        'is_read'           => rand(0, 1),
                        'is_important'      => in_array($n['type'], ['priority_card_generated', 'subscription_expiry']),
                        'created_at'        => now()->subDays(rand(0, 10)),
                    ]);
                }
            }
        }

        $this->command->info('✅ تم إدراج البيانات التجريبية بنجاح!');
        $this->command->info('   - ' . count($createdStations)    . ' محطات وقود');
        $this->command->info('   - ' . count($createdCarWashes)   . ' مغاسل سيارات');
        $this->command->info('   - ' . count($createdMaintenance) . ' مراكز صيانة');
        $this->command->info('   - ' . count($createdCustomers)   . ' عملاء مفعّلين');
        $this->command->info('   - 3 عملاء بانتظار الموافقة');
        $this->command->info('   - اشتراكات + تعبئات + تذاكر + سجلات أمان + إشعارات');
    }
}
