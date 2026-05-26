<?php

namespace App\Livewire\Admin;

use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Refuel;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('لوحة التحكم - غازي')]
class DashboardPage extends Component
{
    public array $stats = [];
    public array $recentRefuels = [];
    public int $pendingUsersCount = 0;
    public int $pendingSubscriptionsCount = 0;

    public function mount(): void
    {
        $monthStart = now()->startOfMonth();
        $today = now()->startOfDay();

        $this->pendingUsersCount = User::where('approval_status', 'pending')->count();
        $this->pendingSubscriptionsCount = Subscription::where('status', 'pending_payment')->count();

        $this->stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'pending_users' => $this->pendingUsersCount,
            'total_stations' => GasStation::count(),
            'active_stations' => GasStation::where('is_active', true)->count(),
            'car_wash_centers' => CarWashCenter::count(),
            'maintenance_centers' => MaintenanceCenter::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->where('end_date', '>=', now())->count(),
            'pending_subscriptions' => $this->pendingSubscriptionsCount,
            'monthly_refuels' => Refuel::where('refuel_date', '>=', $monthStart)->count(),
            'today_refuels' => Refuel::where('refuel_date', '>=', $today)->count(),
            'monthly_revenue' => (float) Refuel::where('refuel_date', '>=', $monthStart)->sum('final_price')
                + (float) Subscription::where('created_at', '>=', $monthStart)->sum('price'),
            'today_revenue' => (float) Refuel::where('refuel_date', '>=', $today)->sum('final_price')
                + (float) Subscription::where('created_at', '>=', $today)->sum('price'),
            'monthly_car_washes' => CarWash::where('wash_date', '>=', $monthStart)->count(),
            'monthly_maintenance' => MaintenanceService::where('service_date', '>=', $monthStart)->count(),
        ];

        $this->recentRefuels = Refuel::with(['user:id,full_name,phone', 'station:id,station_name'])
            ->latest('refuel_date')
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'user_name' => $r->user?->full_name ?? '-',
                'station_name' => $r->station?->station_name ?? '-',
                'liters' => $r->liters,
                'final_price' => $r->final_price,
                'date' => $r->refuel_date,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-page');
    }
}
