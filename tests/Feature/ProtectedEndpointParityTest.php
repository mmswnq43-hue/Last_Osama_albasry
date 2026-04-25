<?php

namespace Tests\Feature;

use App\Models\ElectronicCard;
use App\Models\Employee;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\Notification;
use App\Models\Refuel;
use App\Models\SecurityLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProtectedEndpointParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_status_and_two_factor_endpoints_match_fastapi_expectations(): void
    {
        $user = User::factory()->create([
            'phone' => '700111111',
            'two_factor_enabled' => false,
            'phone_verified' => false,
            'account_locked' => false,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/status')
            ->assertOk()
            ->assertJsonPath('phone', '700111111')
            ->assertJsonPath('two_factor_enabled', false);

        $this->postJson('/api/auth/enable-2fa', [
            'phone' => '700111111',
            'verification_code' => '123456',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'تم تفعيل التحقق الثنائي بنجاح');

        $user->refresh();
        $this->assertTrue($user->two_factor_enabled);
        $this->assertTrue($user->phone_verified);

        $this->postJson('/api/auth/disable-2fa')
            ->assertOk()
            ->assertJsonPath('message', 'تم تعطيل التحقق الثنائي بنجاح');

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'log_type' => 'two_factor_enabled',
            'is_successful' => 1,
        ]);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'log_type' => 'two_factor_disabled',
            'is_successful' => 1,
        ]);
    }

    public function test_user_profile_subscription_and_fuel_status_endpoints_have_fastapi_equivalents(): void
    {
        $user = User::factory()->create([
            'full_name' => 'Ali Customer',
            'vehicle_type' => 'Sedan',
            'engine_number' => 'ENG-555',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_type' => '3months',
            'price' => 45,
            'discount_percent' => 0,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(3),
            'status' => 'active',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 1,
            'remaining_maintenance' => 0,
            'monthly_liters' => 125,
            'last_reset_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/user/profile')
            ->assertOk()
            ->assertJsonPath('full_name', 'Ali Customer');

        $this->putJson('/api/user/profile', [
            'full_name' => 'Ali Updated',
            'vehicle_type' => 'SUV',
        ])
            ->assertOk()
            ->assertJsonPath('full_name', 'Ali Updated')
            ->assertJsonPath('vehicle_type', 'SUV');

        $this->getJson('/api/user/subscription')
            ->assertOk()
            ->assertJsonPath('plan_type', '3months')
            ->assertJsonPath('status', 'active');

        $this->getJson('/api/subscriptions/fuel-status')
            ->assertOk()
            ->assertJsonPath('plan_type', '3months')
            ->assertJsonPath('monthly_fuel_used', 125);

        $this->postJson('/api/subscriptions/renew', [
            'plan_type' => 'monthly',
        ])
            ->assertCreated()
            ->assertJsonPath('plan_type', 'monthly')
            ->assertJsonPath('status', 'active');
    }

    public function test_owner_endpoints_cover_station_and_daily_summary_flows(): void
    {
        $owner = User::factory()->role('station_owner')->create();
        $customer = User::factory()->create();

        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Owner Fuel Hub',
            'commercial_register' => 'CR-OWN-01',
            'location' => 'Taiz',
            'latitude' => 13.5795,
            'longitude' => 44.0209,
            'station_code' => 'ST-OWN-01',
            'is_active' => true,
        ]);

        Refuel::create([
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'subscription_id' => null,
            'employee_id' => null,
            'liters' => 40,
            'price_per_liter' => 10,
            'total_before_discount' => 400,
            'discount_amount' => 0,
            'final_price' => 400,
            'qr_code_used' => 'QR-OWN-01',
            'refuel_date' => now(),
        ]);

        Sanctum::actingAs($owner);

        $this->getJson('/api/owner/my-stations')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.station_name', 'Owner Fuel Hub');

        $this->getJson('/api/owner/my-business')
            ->assertOk()
            ->assertJsonPath('owner_type', 'station_owner')
            ->assertJsonPath('stats.total_stations', 1)
            ->assertJsonPath('stats.total_refuels', 1);

        $this->getJson('/api/owner/stations/'.$station->id.'/today-refuels')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('total_liters', 40)
            ->assertJsonPath('total_revenue', 400);

        $this->getJson('/api/owner/reports/revenue?period=monthly')
            ->assertOk()
            ->assertJsonPath('total_revenue', 400)
            ->assertJsonPath('total_liters', 40);
    }

    public function test_worker_and_services_alias_endpoints_match_expected_protected_behavior(): void
    {
        $owner = User::factory()->role('station_owner')->create();
        $worker = User::factory()->role('station_worker')->create();
        $customer = User::factory()->create([
            'qr_code' => 'GHAZI:WORKER-OK',
            'vehicle_type' => 'Pickup',
            'engine_number' => 'ENG-WORK-1',
        ]);

        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Worker Station',
            'commercial_register' => 'CR-WRK-01',
            'location' => 'Sanaa',
            'latitude' => 15.3694,
            'longitude' => 44.1910,
            'station_code' => 'ST-WRK-01',
            'is_active' => true,
        ]);

        $employee = Employee::create([
            'user_id' => $worker->id,
            'station_id' => $station->id,
            'employee_code' => 'EMP-WRK-01',
            'position' => 'Pump Worker',
            'hire_date' => now(),
            'salary' => 100,
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 20,
            'discount_percent' => 0,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 1,
            'remaining_maintenance' => 1,
            'monthly_liters' => 499,
            'last_reset_date' => now(),
        ]);

        Sanctum::actingAs($worker);

        $this->getJson('/api/worker/my-workplace')
            ->assertOk()
            ->assertJsonPath('employee_code', 'EMP-WRK-01')
            ->assertJsonPath('workplace_type', 'gas_station');

        $this->postJson('/api/services/refuels/validate-qr', [
            'qr_code' => 'GHAZI:WORKER-OK',
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'vehicle_type' => 'Pickup',
            'engine_number' => 'ENG-WORK-1',
            'user_lat' => 15.3694,
            'user_lon' => 44.1910,
        ])
            ->assertOk()
            ->assertJsonPath('is_valid', true)
            ->assertJsonPath('user_info.id', $customer->id);

        $this->postJson('/api/services/refuels/process', [
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'liters' => 5,
            'price_per_liter' => 10,
            'qr_code' => 'GHAZI:WORKER-OK',
        ])
            ->assertCreated()
            ->assertJsonPath('employee_id', $employee->id);

        $this->assertDatabaseHas('electronic_cards', [
            'user_id' => $customer->id,
            'is_used' => 0,
        ]);

        $this->getJson('/api/services/refuels/me')
            ->assertOk()
            ->assertJsonCount(0);

        $this->getJson('/api/worker/my-history/refuels')
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_admin_users_transactions_security_and_threats_endpoints_are_usable(): void
    {
        $admin = User::factory()->role('admin')->create();
        $customer = User::factory()->create(['is_active' => false]);

        Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 20,
            'discount_percent' => 0,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addMonth(),
            'status' => 'pending_payment',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 0,
            'remaining_maintenance' => 0,
            'monthly_liters' => 0,
            'last_reset_date' => now(),
        ]);

        for ($index = 0; $index < 5; $index++) {
            SecurityLog::create([
                'user_id' => $customer->id,
                'log_type' => 'failed_login',
                'ip_address' => '127.0.0.1',
                'is_successful' => false,
                'error_message' => 'Invalid credentials',
                'created_at' => now()->subMinutes(10),
            ]);
        }

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/users?is_active=0')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $customer->id);

        $this->getJson('/api/admin/transactions?transaction_type=subscription')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('transactions.0.type', 'subscription_payment');

        $this->getJson('/api/admin/security/logs?log_type=failed_login')
            ->assertOk()
            ->assertJsonPath('total', 5);

        $this->getJson('/api/admin/security/threats')
            ->assertOk()
            ->assertJsonPath('threats.0.type', 'multiple_failed_logins')
            ->assertJsonPath('threats.0.user_id', $customer->id);

        $this->postJson('/api/admin/security/lock-user', [
            'user_id' => $customer->id,
            'lock_type' => 'temporary',
            'reason' => 'Repeated failed logins',
            'duration' => 30,
            'notify_user' => true,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'تم قفل حساب المستخدم');

        $customer->refresh();
        $this->assertTrue($customer->account_locked);
        $this->assertFalse($customer->is_active);
    }

    public function test_electronic_card_endpoints_cover_status_validation_use_and_admin_views(): void
    {
        $admin = User::factory()->role('admin')->create();
        $owner = User::factory()->role('station_owner')->create();
        $customer = User::factory()->create();

        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Priority Station',
            'commercial_register' => 'CR-ELC-01',
            'location' => 'Ibb',
            'station_code' => 'ST-ELC-01',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 20,
            'discount_percent' => 0,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 0,
            'remaining_maintenance' => 0,
            'monthly_liters' => 520,
            'last_reset_date' => now(),
        ]);

        $card = ElectronicCard::create([
            'user_id' => $customer->id,
            'card_number' => 'PRIORITY-CARD-01',
            'generated_at' => now(),
            'monthly_liters_at_generation' => 520,
            'is_used' => false,
            'expires_at' => now()->addDay(),
        ]);

        Sanctum::actingAs($customer);

        $this->getJson('/api/electronic-cards/priority-status')
            ->assertOk()
            ->assertJsonPath('active_priority_cards', 1)
            ->assertJsonPath('cards.0.card_number', 'PRIORITY-CARD-01');

        $this->getJson('/api/electronic-cards/validate/PRIORITY-CARD-01')
            ->assertOk()
            ->assertJsonPath('is_valid', true);

        $this->postJson('/api/electronic-cards/PRIORITY-CARD-01/use-priority', [
            'station_id' => $station->id,
        ])
            ->assertOk()
            ->assertJsonPath('priority_access', true)
            ->assertJsonPath('station_name', 'Priority Station');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'notification_type' => 'priority_card_used',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/electronic-cards/admin/all-cards')
            ->assertOk()
            ->assertJsonCount(1);

        $this->getJson('/api/electronic-cards/admin/user-cards/'.$customer->id)
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.card_number', 'PRIORITY-CARD-01');
    }
}
