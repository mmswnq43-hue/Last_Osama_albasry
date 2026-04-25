<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\Refuel;
use App\Models\User;
use App\Models\GasStation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_revenue_csv(): void
    {
        $admin = User::factory()->role('admin')->create();

        // create some sample data to include in the export
        $customer = User::factory()->create();
        Subscription::create([
            'user_id' => $customer->id,
            'plan_type' => 'monthly',
            'price' => 10,
            'discount_percent' => 0,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'status' => 'active',
            'monthly_liters' => 0,
            'last_reset_date' => now(),
        ]);

        $station = GasStation::create([
            'owner_id' => $admin->id,
            'station_name' => 'Test Station',
            'commercial_register' => 'CR123',
            'station_code' => 'ST001',
            'location' => 'Test Location',
            'is_active' => true,
            'created_at' => now(),
        ]);

        Refuel::create([
            'user_id' => $customer->id,
            'station_id' => $station->id,
            'employee_id' => null,
            'liters' => 20,
            'price_per_liter' => 1.5,
            'total_before_discount' => 30,
            'discount_amount' => 0,
            'final_price' => 30,
            'qr_code_used' => 'TESTQR',
            'refuel_date' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get('/api/admin/reports/export?type=revenue&period=monthly');

        $response->assertStatus(200);
        $this->assertStringContainsString('"type","amount"', $response->getContent());
        $this->assertStringContainsString('"subscriptions"', $response->getContent());
    }
}
