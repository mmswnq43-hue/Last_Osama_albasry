<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user — safe to run multiple times
        User::firstOrCreate(
            ['phone' => '777777777'],
            [
                'full_name'       => 'Admin User',
                'password_hash'   => Hash::make('password'),
                'user_role'       => 'admin',
                'qr_code'         => 'QR-ADMIN-' . strtoupper(Str::random(8)),
                'is_active'       => true,
                'phone_verified'  => true,
                'approval_status' => 'approved',
            ]
        );

        // System settings defaults
        $settings = [
            ['key' => 'priority_card_liters_threshold', 'value' => '500', 'label' => 'حد اللترات لتفعيل بطاقة الأولوية', 'type' => 'integer'],
            ['key' => 'priority_card_validity_days',    'value' => '30',  'label' => 'مدة صلاحية بطاقة الأولوية (بالأيام)', 'type' => 'integer'],
        ];

        foreach ($settings as $s) {
            SystemSetting::firstOrCreate(['key' => $s['key']], $s);
        }

        $this->command->info('✅ Production seed done — admin: 777777777 / password');
    }
}
