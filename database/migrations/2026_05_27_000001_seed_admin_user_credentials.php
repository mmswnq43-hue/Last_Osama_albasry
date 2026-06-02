<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Find admin by role
        $admin = DB::table('users')->where('user_role', 'admin')->first();

        if ($admin) {
            // Update existing admin with new credentials
            DB::table('users')->where('id', $admin->id)->update([
                'phone'           => '770794503',
                'email'           => 'm.mosonaq@gmail.com',
                'password_hash'   => Hash::make('770794503'),
                'full_name'       => 'Admin',
                'is_active'       => 1,
                'approval_status' => 'approved',
            ]);
        } else {
            // Create admin if not exists
            DB::table('users')->insert([
                'full_name'       => 'Admin',
                'phone'           => '770794503',
                'email'           => 'm.mosonaq@gmail.com',
                'password_hash'   => Hash::make('770794503'),
                'user_role'       => 'admin',
                'qr_code'         => 'QR-ADMIN-' . strtoupper(Str::random(8)),
                'is_active'       => 1,
                'phone_verified'  => 1,
                'approval_status' => 'approved',
            ]);
        }
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};
