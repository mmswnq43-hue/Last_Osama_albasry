<?php

namespace App\Livewire\Owner;

use App\Models\Employee;
use App\Models\GasStation;
use App\Models\Refuel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.owner')]
#[Title('لوحة التحكم - مالك المحطة')]
class DashboardPage extends Component
{
    public function render()
    {
        $ownerId = auth()->id();
        $stations = GasStation::where('owner_id', $ownerId)->get();
        $stationIds = $stations->pluck('id');

        $todayStart = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $stats = [
            'total_stations'    => $stations->count(),
            'open_stations'     => $stations->where('is_open', true)->count(),
            'refuels_today'     => Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $todayStart)->count(),
            'revenue_today'     => (float) Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $todayStart)->sum('final_price'),
            'refuels_month'     => Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $monthStart)->count(),
            'revenue_month'     => (float) Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $monthStart)->sum('final_price'),
            'employees_count'   => Employee::whereIn('station_id', $stationIds)->where('is_active', true)->count(),
        ];

        $recentRefuels = Refuel::with(['user:id,full_name,phone', 'station:id,station_name'])
            ->whereIn('station_id', $stationIds)
            ->latest('refuel_date')
            ->limit(8)
            ->get();

        return view('livewire.owner.dashboard-page', compact('stats', 'recentRefuels'));
    }
}
