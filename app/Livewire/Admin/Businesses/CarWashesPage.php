<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\CarWashCenter;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مغاسل السيارات - غازي')]
class CarWashesPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $showModal = '';
    public string $successMessage = '';
    public ?int $editingId = null;
    public string $center_name = '';
    public string $location = '';
    public string $commercial_register = '';
    public string $center_code = '';
    public string $latitude = '';
    public string $longitude = '';
    public int $owner_id = 0;
    public bool $is_active = true;

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void { $this->resetForm(); $this->showModal = 'form'; }

    public function openEdit(int $id): void
    {
        $center = CarWashCenter::findOrFail($id);
        $this->editingId = $id;
        $this->center_name = $center->center_name;
        $this->location = $center->location;
        $this->commercial_register = $center->commercial_register;
        $this->center_code = $center->center_code;
        $this->latitude = (string) $center->latitude;
        $this->longitude = (string) $center->longitude;
        $this->owner_id = $center->owner_id;
        $this->is_active = $center->is_active;
        $this->showModal = 'form';
    }

    public function save(): void
    {
        $rules = ['center_name' => 'required|string|max:100', 'location' => 'required|string|max:255', 'latitude' => 'nullable|numeric', 'longitude' => 'nullable|numeric'];
        if (! $this->editingId) {
            $rules['commercial_register'] = 'required|string|max:50|unique:car_wash_centers,commercial_register';
            $rules['center_code'] = 'required|string|max:20|unique:car_wash_centers,center_code';
            $rules['owner_id'] = 'required|integer|exists:users,id';
        }
        $this->validate($rules);

        if ($this->editingId) {
            CarWashCenter::findOrFail($this->editingId)->forceFill(['center_name' => $this->center_name, 'location' => $this->location, 'latitude' => $this->latitude ?: null, 'longitude' => $this->longitude ?: null, 'is_active' => $this->is_active])->save();
            $this->successMessage = 'تم تحديث المغسلة بنجاح';
        } else {
            CarWashCenter::create(['center_name' => $this->center_name, 'location' => $this->location, 'commercial_register' => $this->commercial_register, 'center_code' => $this->center_code, 'latitude' => $this->latitude ?: null, 'longitude' => $this->longitude ?: null, 'owner_id' => $this->owner_id, 'is_active' => $this->is_active]);
            $this->successMessage = 'تمت إضافة المغسلة بنجاح';
        }
        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $c = CarWashCenter::findOrFail($id);
        $c->forceFill(['is_active' => ! $c->is_active])->save();
        $this->successMessage = $c->is_active ? 'تم تفعيل المغسلة' : 'تم إيقاف المغسلة';
    }

    public function delete(int $id): void
    {
        CarWashCenter::findOrFail($id)->delete();
        $this->successMessage = 'تم الحذف بنجاح';
    }

    public function closeModal(): void { $this->showModal = ''; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->editingId = null; $this->center_name = ''; $this->location = ''; $this->commercial_register = '';
        $this->center_code = ''; $this->latitude = ''; $this->longitude = ''; $this->owner_id = 0; $this->is_active = true;
    }

    public function render()
    {
        $query = CarWashCenter::with('owner:id,full_name');
        if ($this->search) {
            $query->where(fn($q) => $q->where('center_name', 'like', "%{$this->search}%")->orWhere('location', 'like', "%{$this->search}%"));
        }
        return view('livewire.admin.businesses.car-washes-page', [
            'centers' => $query->latest()->paginate(12),
            'owners' => User::whereIn('user_role', ['car_wash_owner', 'admin'])->get(['id', 'full_name']),
        ]);
    }
}
