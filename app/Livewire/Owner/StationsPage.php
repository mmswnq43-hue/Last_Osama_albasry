<?php

namespace App\Livewire\Owner;

use App\Models\GasStation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.owner')]
#[Title('محطاتي - مالك المحطة')]
class StationsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $station_name = '';
    public string $location = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public string $station_code = '';
    public string $phone = '';
    public bool $is_open = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'station_name', 'location', 'latitude', 'longitude', 'station_code', 'phone', 'is_open']);
        $this->is_open = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $station = GasStation::where('id', $id)->where('owner_id', auth()->id())->firstOrFail();
        $this->editingId = $id;
        $this->station_name = $station->station_name;
        $this->location = $station->location;
        $this->latitude = $station->latitude;
        $this->longitude = $station->longitude;
        $this->station_code = $station->station_code;
        $this->phone = $station->phone ?? '';
        $this->is_open = (bool) $station->is_open;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'station_name' => 'required|string|max:100',
            'location'     => 'required|string|max:255',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'station_code' => 'required|string|max:20',
            'phone'        => 'nullable|string|max:20',
        ]);

        $data = [
            'station_name' => $this->station_name,
            'location'     => $this->location,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'station_code' => $this->station_code,
            'phone'        => $this->phone,
            'is_open'      => $this->is_open,
        ];

        if ($this->editingId) {
            GasStation::where('id', $this->editingId)->where('owner_id', auth()->id())->update($data);
            session()->flash('success', 'تم تحديث بيانات المحطة بنجاح.');
        } else {
            GasStation::create(array_merge($data, [
                'owner_id'  => auth()->id(),
                'is_active' => false, // admin activates
            ]));
            session()->flash('success', 'تم إضافة المحطة بنجاح. في انتظار تفعيل الإدارة.');
        }

        $this->showModal = false;
    }

    public function toggleOpen(int $id): void
    {
        $station = GasStation::where('id', $id)->where('owner_id', auth()->id())->firstOrFail();
        $station->update(['is_open' => !$station->is_open]);
        session()->flash('success', $station->is_open ? 'تم فتح المحطة.' : 'تم إغلاق المحطة.');
    }

    public function delete(int $id): void
    {
        GasStation::where('id', $id)->where('owner_id', auth()->id())->delete();
        session()->flash('success', 'تم حذف المحطة.');
    }

    public function render()
    {
        $stations = GasStation::where('owner_id', auth()->id())
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('station_name', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('station_code', 'like', '%' . $this->search . '%');
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.owner.stations-page', compact('stations'));
    }
}
