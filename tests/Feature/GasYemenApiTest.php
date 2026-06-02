<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\GasStation;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GasYemenApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_subscription_and_customer_can_generate_qr_codes(): void
    {
        $admin = User::factory()->role('admin')->create();
        $customer = User::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 20,
            'discount_percent' => 0,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'pending_payment',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 1,
            'remaining_maintenance' => 1,
            'monthly_liters' => 0,
            'last_reset_date' => now(),
        ]);

        Sanctum::actingAs($admin);
        $this->putJson('/api/subscriptions/'.$subscription->id.'/approve')
            ->assertOk()
            ->assertJsonPath('status', 'active');

        Sanctum::actingAs($customer);
        $this->getJson('/api/user/qr-codes')
            ->assertOk()
            ->assertJsonPath('user_id', $customer->id)
            ->assertJsonStructure([
                'qr_codes' => ['refuel_qr'],
                'subscription_info' => ['remaining_car_washes', 'remaining_maintenance'],
            ]);
    }

    public function test_worker_refuel_process_generates_priority_card_after_500_liters(): void
    {
        $owner = User::factory()->role('station_owner')->create();
        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Main Station',
            'commercial_register' => 'CR-001',
            'location' => 'Sanaa',
            'station_code' => 'ST-001',
            'is_active' => true,
        ]);

        $customer = User::factory()->create([
            'qr_code' => 'GHAZI:PRIORITY123',
        ]);

        $subscription = Subscription::create([
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
            'monthly_liters' => 490,
            'last_reset_date' => now(),
        ]);

        $workerUser = User::factory()->role('station_worker')->create();
        Employee::create([
            'user_id' => $workerUser->id,
            'station_id' => $station->id,
            'employee_code' => 'EMP-TEST01',
            'position' => 'Pump Worker',
            'hire_date' => now(),
            'salary' => 100,
            'is_active' => true,
        ]);

        Sanctum::actingAs($workerUser);
        $this->postJson('/api/worker/services/refuels/process', [
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'liters' => 20,
            'price_per_liter' => 10,
            'qr_code' => 'GHAZI:PRIORITY123',
        ])->assertCreated();

        $subscription->refresh();

        $this->assertSame(510.0, (float) $subscription->monthly_liters);
        $this->assertDatabaseHas('electronic_cards', [
            'user_id' => $customer->id,
            'is_used' => 0,
        ]);
    }

    public function test_owner_can_add_employee_and_view_business_overview(): void
    {
        $owner = User::factory()->role('station_owner')->create();
        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Owner Station',
            'commercial_register' => 'CR-OWNER',
            'location' => 'Aden',
            'station_code' => 'ST-OWNER',
            'is_active' => true,
        ]);

        $employeeUser = User::factory()->create();

        Sanctum::actingAs($owner);
        $this->postJson('/api/owner/employees', [
            'user_id' => $employeeUser->id,
            'station_id' => $station->id,
            'position' => 'Cashier',
            'salary' => 250,
            'hire_date' => now()->toDateString(),
        ])->assertCreated();

        $employeeUser->refresh();
        $this->assertSame('station_worker', $employeeUser->user_role);

        $this->getJson('/api/owner/my-business')
            ->assertOk()
            ->assertJsonPath('owner_type', 'station_owner')
            ->assertJsonPath('stats.total_stations', 1);
    }
}
