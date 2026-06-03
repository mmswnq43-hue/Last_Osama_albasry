<?php

namespace App\Livewire\Owner;

use App\Models\GasStation;
use App\Models\Refuel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.owner')]
#[Title('التقارير - مالك المحطة')]
class ReportsPage extends Component
{
    public string $period = 'monthly';

    public function render()
    {
        $ownerId = auth()->id();
        $stationIds = GasStation::where('owner_id', $ownerId)->pluck('id');

        // Last 6 months chart data
        $chartData = collect(range(5, 0))->map(function ($i) use ($stationIds) {
            $start = now()->subMonths($i)->startOfMonth();
            $end   = now()->subMonths($i)->endOfMonth();
            return [
                'label'   => $start->locale('ar')->translatedFormat('M Y'),
                'revenue' => (float) Refuel::whereIn('station_id', $stationIds)
                    ->whereBetween('refuel_date', [$start, $end])->sum('final_price'),
                'refuels' => Refuel::whereIn('station_id', $stationIds)
                    ->whereBetween('refuel_date', [$start, $end])->count(),
                'liters'  => (float) Refuel::whereIn('station_id', $stationIds)
                    ->whereBetween('refuel_date', [$start, $end])->sum('liters'),
            ];
        })->values()->toArray();

        // Per-station stats
        $stations = GasStation::where('owner_id', $ownerId)->get();
        $stationStats = $stations->map(function ($station) {
            $totalRefuels  = Refuel::where('station_id', $station->id)->count();
            $totalLiters   = (float) Refuel::where('station_id', $station->id)->sum('liters');
            $totalRevenue  = (float) Refuel::where('station_id', $station->id)->sum('final_price');
            $daysActive    = $station->created_at ? max(1, now()->diffInDays($station->created_at)) : 1;
            return [
                'id'            => $station->id,
                'name'          => $station->station_name,
                'total_refuels' => $totalRefuels,
                'total_liters'  => $totalLiters,
                'total_revenue' => $totalRevenue,
                'avg_per_day'   => round($totalRevenue / $daysActive, 2),
                'is_active'     => $station->is_active,
                'is_open'       => $station->is_open,
            ];
        })->toArray();

        return view('livewire.owner.reports-page', compact('chartData', 'stationStats'));
    }
}
