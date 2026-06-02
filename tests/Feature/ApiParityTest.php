<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\GasStation;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_system_endpoints_respond_with_expected_payloads(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'healthy');

        $this->getJson('/api/info')
            ->assertOk()
            ->assertJsonPath('name', 'Ghazi Gas Station System');

        $this->getJson('/api/api-overview')
            ->assertOk()
            ->assertJsonStructure([
                'authentication',
                'user_management',
                'business_management',
                'services',
                'admin',
            ]);

        $this->getJson('/api/schema')
            ->assertOk()
            ->assertJsonPath('total_tables', 13);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonStructure(['refuel', 'car_wash', 'maintenance']);
    }

    public function test_admin_can_access_analytics_and_receive_aggregated_metrics(): void
    {
        $admin = User::factory()->role('admin')->create();
        $owner = User::factory()->role('station_owner')->create();
        $customer = User::factory()->create();

        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Analytics Station',
            'commercial_register' => 'CR-AN-01',
            'location' => 'Sanaa',
            'latitude' => 15.3694,
            'longitude' => 44.1910,
            'station_code' => 'STA-AN-01',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 20,
            'discount_percent' => 5,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'remaining_cylinders' => 0,
            'remaining_car_washes' => 1,
            'remaining_maintenance' => 1,
            'monthly_liters' => 50,
            'last_reset_date' => now(),
        ]);

        \App\Models\Refuel::create([
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'subscription_id' => 1,
            'employee_id' => null,
            'liters' => 50,
            'price_per_liter' => 10,
            'total_before_discount' => 500,
            'discount_amount' => 25,
            'final_price' => 475,
            'qr_code_used' => 'QR-AN-01',
            'refuel_date' => now(),
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/analytics/system-overview')
            ->assertOk()
            ->assertJsonPath('users.total', 3)
            ->assertJsonPath('businesses.stations.total', 1)
            ->assertJsonPath('services.refuels.total', 1);

        $this->getJson('/api/analytics/subscription-analytics')
            ->assertOk()
            ->assertJsonPath('summary.total_subscriptions', 1);
    }

    public function test_worker_refuel_validation_rejects_far_location_and_writes_security_log(): void
    {
        $owner = User::factory()->role('station_owner')->create();
        $workerUser = User::factory()->role('station_worker')->create();
        $customer = User::factory()->create([
            'qr_code' => 'GHAZI:VALID-QR-001',
            'vehicle_type' => 'Toyota',
            'engine_number' => 'ENG-12345',
        ]);

        $station = GasStation::create([
            'owner_id' => $owner->id,
            'station_name' => 'Distance Station',
            'commercial_register' => 'CR-DIS-01',
            'location' => 'Aden',
            'latitude' => 12.7855,
            'longitude' => 45.0187,
            'station_code' => 'STA-DIS-01',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $workerUser->id,
            'station_id' => $station->id,
            'employee_code' => 'EMP-DIS-01',
            'position' => 'Worker',
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
            'remaining_car_washes' => 0,
            'remaining_maintenance' => 0,
            'monthly_liters' => 10,
            'last_reset_date' => now(),
        ]);

        Sanctum::actingAs($workerUser);

        $this->postJson('/api/worker/services/refuels/validate-qr', [
            'qr_code' => 'GHAZI:VALID-QR-001',
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'vehicle_type' => 'Toyota',
            'engine_number' => 'ENG-12345',
            'user_lat' => 15.3694,
            'user_lon' => 44.1910,
        ])
            ->assertOk()
            ->assertJsonPath('is_valid', false);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $customer->id,
            'log_type' => 'refuel_qr_validation',
            'is_successful' => 0,
        ]);
    }

    public function test_ticket_workflow_and_broadcast_targeting_behave_as_expected(): void
    {
        $admin = User::factory()->role('admin')->create();
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();

        Sanctum::actingAs($customer);
        $this->postJson('/api/tickets', [
            'title' => 'QR Code issue',
            'description' => 'The QR code did not scan',
            'priority' => 'high',
        ])->assertCreated();

        $ticket = SupportTicket::query()->firstOrFail();

        $this->getJson('/api/tickets/me')
            ->assertOk()
            ->assertJsonCount(1);

        Sanctum::actingAs($admin);
        $this->putJson('/api/tickets/'.$ticket->id, [
            'status' => 'resolved',
            'admin_response' => 'Resolved by support team',
        ])
            ->assertOk()
            ->assertJsonPath('status', 'resolved');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'notification_type' => 'support_ticket_update',
        ]);

        $this->postJson('/api/admin/broadcast', [
            'message' => 'System maintenance tonight',
            'target_audience' => 'all_users',
            'channels' => ['in_app'],
            'priority' => 'high',
        ])
            ->assertOk()
            ->assertJsonPath('notifications_sent', 3);

        $this->assertSame(4, Notification::query()->count());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $otherCustomer->id,
            'message' => 'System maintenance tonight',
        ]);
    }
}
