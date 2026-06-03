<?php

namespace App\Livewire\Owner;

use App\Models\GasStation;
use App\Models\Refuel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.owner')]
#[Title('التعبئات - مالك المحطة')]
class RefuelsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $stationFilter = null;
    public int $perPage = 15;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingStationFilter(): void { $this->resetPage(); }

    public function filter(): void
    {
        $this->resetPage();
    }

    public function export(): void
    {
        session()->flash('success', 'سيتم إرسال ملف التصدير إلى بريدك الإلكتروني قريباً.');
    }

    public function render()
    {
        $stationIds = GasStation::where('owner_id', auth()->id())->pluck('id');

        $today = now()->startOfDay();

        $summaryToday = [
            'liters'  => (float) Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $today)->sum('liters'),
            'revenue' => (float) Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $today)->sum('final_price'),
            'count'   => Refuel::whereIn('station_id', $stationIds)->where('refuel_date', '>=', $today)->count(),
        ];

        $refuels = Refuel::with(['user:id,full_name,phone', 'station:id,station_name', 'employee:id,user_id'])
            ->whereIn('station_id', $stationIds)
            ->when($this->search, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
            ))
            ->when($this->dateFrom, fn($q) => $q->where('refuel_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('refuel_date', '<=', $this->dateTo . ' 23:59:59'))
            ->when($this->stationFilter, fn($q) => $q->where('station_id', $this->stationFilter))
            ->latest('refuel_date')
            ->paginate($this->perPage);

        $myStations = GasStation::where('owner_id', auth()->id())->get(['id', 'station_name']);

        return view('livewire.owner.refuels-page', compact('refuels', 'summaryToday', 'myStations'));
    }
}
