<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\GasStation;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('محطات الوقود - غازي')]
class StationsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $showModal = '';
    public string $successMessage = '';

    // Form fields
    public ?int $editingId = null;
    public string $station_name = '';
    public string $location = '';
    public string $commercial_register = '';
    public string $station_code = '';
    public string $latitude = '';
    public string $longitude = '';
    public int $owner_id = 0;
    public bool $is_active = true;

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = 'form';
    }

    public function openEdit(int $id): void
    {
        $station = GasStation::findOrFail($id);
        $this->editingId = $id;
        $this->station_name = $station->station_name;
        $this->location = $station->location;
        $this->commercial_register = $station->commercial_register;
        $this->station_code = $station->station_code;
        $this->latitude = (string) $station->latitude;
        $this->longitude = (string) $station->longitude;
        $this->owner_id = $station->owner_id;
        $this->is_active = $station->is_active;
        $this->showModal = 'form';
    }

    public function save(): void
    {
        $rules = [
            'station_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        if (! $this->editingId) {
            $rules['commercial_register'] = 'required|string|max:50|unique:gas_stations,commercial_register';
            $rules['station_code'] = 'required|string|max:20|unique:gas_stations,station_code';
            $rules['owner_id'] = 'required|integer|exists:users,id';
        }

        $this->validate($rules, [
            'station_name.required' => 'اسم المحطة مطلوب',
            'location.required' => 'الموقع مطلوب',
            'commercial_register.required' => 'السجل التجاري مطلوب',
            'commercial_register.unique' => 'السجل التجاري مستخدم مسبقاً',
            'station_code.required' => 'رمز المحطة مطلوب',
            'station_code.unique' => 'رمز المحطة مستخدم مسبقاً',
            'owner_id.required' => 'صاحب المحطة مطلوب',
        ]);

        if ($this->editingId) {
            $station = GasStation::findOrFail($this->editingId);
            $station->forceFill([
                'station_name' => $this->station_name,
                'location' => $this->location,
                'latitude' => $this->latitude ?: null,
                'longitude' => $this->longitude ?: null,
                'is_active' => $this->is_active,
            ])->save();
            $this->successMessage = 'تم تحديث المحطة بنجاح';
        } else {
            GasStation::create([
                'station_name' => $this->station_name,
                'location' => $this->location,
                'commercial_register' => $this->commercial_register,
                'station_code' => $this->station_code,
                'latitude' => $this->latitude ?: null,
                'longitude' => $this->longitude ?: null,
                'owner_id' => $this->owner_id,
                'is_active' => $this->is_active,
            ]);
            $this->successMessage = 'تمت إضافة المحطة بنجاح';
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $station = GasStation::findOrFail($id);
        $station->forceFill(['is_active' => ! $station->is_active])->save();
        $this->successMessage = $station->is_active ? 'تم تفعيل المحطة' : 'تم إيقاف المحطة';
    }

    public function delete(int $id): void
    {
        GasStation::findOrFail($id)->delete();
        $this->successMessage = 'تم حذف المحطة بنجاح';
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->station_name = '';
        $this->location = '';
        $this->commercial_register = '';
        $this->station_code = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->owner_id = 0;
        $this->is_active = true;
    }

    public function render()
    {
        $query = GasStation::with('owner:id,full_name');

        if ($this->search) {
            $query->where(fn($q) => $q->where('station_name', 'like', "%{$this->search}%")->orWhere('location', 'like', "%{$this->search}%"));
        }
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return view('livewire.admin.businesses.stations-page', [
            'stations' => $query->latest()->paginate(12),
            'owners' => User::whereIn('user_role', ['station_owner', 'admin'])->get(['id', 'full_name']),
        ]);
    }
}
