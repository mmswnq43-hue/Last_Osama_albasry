<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'admin' => '777777777',
            'customer' => '770000001',
            'station_owner' => '770000002',
            'car_wash_owner' => '770000003',
            'maintenance_owner' => '770000004',
            'station_worker' => '770000005',
            'car_wash_worker' => '770000006',
            'maintenance_worker' => '770000007',
        ];

        foreach ($roles as $role => $phone) {
            User::factory()->create([
                'full_name' => ucwords(str_replace('_', ' ', $role)) . ' User',
                'phone' => $phone,
                'user_role' => $role,
            ]);
        }

        $settings = [
            ['key' => 'priority_card_liters_threshold', 'value' => '500', 'label' => 'حد اللترات لتفعيل بطاقة الأولوية', 'type' => 'integer'],
            ['key' => 'priority_card_validity_days',    'value' => '30',  'label' => 'مدة صلاحية بطاقة الأولوية (بالأيام)', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
