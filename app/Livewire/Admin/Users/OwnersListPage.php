<?php

namespace App\Livewire\Admin\Users;

use App\Models\GasStation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('إدارة الملاك - غازي')]
class OwnersListPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?array $selectedOwner = null;
    public string $showModal = '';
    public string $successMessage = '';
    public int $createStep = 1;

    public array $createForm = [
        // Owner fields
        'full_name'           => '',
        'phone'               => '',
        'email'               => '',
        'national_id'         => '',
        'address'             => '',
        'password'            => '',
        // Station fields
        'station_name'        => '',
        'city'                => '',
        'district'            => '',
        'license_number'      => '',
        'license_issue_date'  => '',
        'license_expiry_date' => '',
        'pumps_count'         => 1,
        'fuel_types'          => [],
        'latitude'            => '',
        'longitude'           => '',
        'station_phone'       => '',
    ];

    public array $editForm = [
        'full_name'   => '',
        'phone'       => '',
        'email'       => '',
        'national_id' => '',
        'address'     => '',
    ];
    public int $editOwnerId = 0;
    public string $deleteConfirmId = '';

    public string $createError = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->createForm = [
            'full_name'           => '',
            'phone'               => '',
            'email'               => '',
            'national_id'         => '',
            'address'             => '',
            'password'            => '',
            'station_name'        => '',
            'city'                => '',
            'district'            => '',
            'license_number'      => '',
            'license_issue_date'  => '',
            'license_expiry_date' => '',
            'pumps_count'         => 1,
            'fuel_types'          => [],
            'latitude'            => '',
            'longitude'           => '',
            'station_phone'       => '',
        ];
        $this->createError = '';
        $this->createStep = 1;
        $this->showModal = 'create';
    }

    public function nextStep(): void
    {
        $this->createError = '';
        $this->validate([
            'createForm.full_name'   => 'required|string|max:100',
            'createForm.phone'       => 'required|string|max:20|unique:users,phone',
            'createForm.email'       => 'nullable|email|max:150|unique:users,email',
            'createForm.national_id' => 'nullable|string|max:50',
            'createForm.address'     => 'nullable|string',
            'createForm.password'    => 'required|string|min:6',
        ]);
        $this->createStep = 2;
    }

    public function createOwner(): void
    {
        $this->createError = '';
        $this->validate([
            'createForm.station_name'        => 'required|string|max:100',
            'createForm.city'                => 'required|string|max:100',
            'createForm.district'            => 'nullable|string|max:100',
            'createForm.license_number'      => 'nullable|string|max:100',
            'createForm.license_issue_date'  => 'nullable|date',
            'createForm.license_expiry_date' => 'nullable|date|after_or_equal:createForm.license_issue_date',
            'createForm.pumps_count'         => 'required|integer|min:1|max:100',
            'createForm.fuel_types'          => 'nullable|array',
            'createForm.latitude'            => 'nullable|numeric|between:-90,90',
            'createForm.longitude'           => 'nullable|numeric|between:-180,180',
            'createForm.station_phone'       => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'full_name'       => $this->createForm['full_name'],
            'phone'           => $this->createForm['phone'],
            'email'           => $this->createForm['email'] ?: null,
            'national_id'     => $this->createForm['national_id'] ?: null,
            'address'         => $this->createForm['address'] ?: null,
            'user_role'       => 'station_owner',
            'password_hash'   => Hash::make($this->createForm['password']),
            'is_active'       => true,
            'approval_status' => 'approved',
            'created_at'      => now(),
        ]);

        $stationCode = 'ST' . str_pad($user->id, 6, '0', STR_PAD_LEFT);

        GasStation::create([
            'owner_id'            => $user->id,
            'station_name'        => $this->createForm['station_name'],
            'commercial_register' => $this->createForm['license_number'] ?: null,
            'location'            => trim($this->createForm['city'] . ($this->createForm['district'] ? '، ' . $this->createForm['district'] : '')),
            'city'                => $this->createForm['city'],
            'district'            => $this->createForm['district'] ?: null,
            'license_number'      => $this->createForm['license_number'] ?: null,
            'license_issue_date'  => $this->createForm['license_issue_date'] ?: null,
            'license_expiry_date' => $this->createForm['license_expiry_date'] ?: null,
            'pumps_count'         => $this->createForm['pumps_count'],
            'fuel_types'          => $this->createForm['fuel_types'] ?: null,
            'latitude'            => $this->createForm['latitude'] !== '' ? $this->createForm['latitude'] : null,
            'longitude'           => $this->createForm['longitude'] !== '' ? $this->createForm['longitude'] : null,
            'phone'               => $this->createForm['station_phone'] ?: null,
            'station_code'        => $stationCode,
            'is_active'           => true,
            'is_open'             => false,
            'rating'              => 0,
            'rating_count'        => 0,
            'created_at'          => now(),
        ]);

        $this->successMessage = 'تم إنشاء حساب المالك والمحطة بنجاح';
        $this->closeModal();
    }

    public function openEdit(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editOwnerId = $userId;
        $this->editForm = [
            'full_name'   => $user->full_name ?? '',
            'phone'       => $user->phone ?? '',
            'email'       => $user->email ?? '',
            'national_id' => $user->national_id ?? '',
            'address'     => $user->address ?? '',
        ];
        $this->showModal = 'edit';
    }

    public function updateOwner(): void
    {
        $this->validate([
            'editForm.full_name'   => 'required|string|max:100',
            'editForm.phone'       => 'required|string|max:20|unique:users,phone,' . $this->editOwnerId,
            'editForm.email'       => 'nullable|email|max:150|unique:users,email,' . $this->editOwnerId,
            'editForm.national_id' => 'nullable|string|max:50',
            'editForm.address'     => 'nullable|string',
        ]);

        User::findOrFail($this->editOwnerId)->forceFill([
            'full_name'   => $this->editForm['full_name'],
            'phone'       => $this->editForm['phone'],
            'email'       => $this->editForm['email'] ?: null,
            'national_id' => $this->editForm['national_id'] ?: null,
            'address'     => $this->editForm['address'] ?: null,
        ])->save();

        $this->successMessage = 'تم تحديث بيانات المالك بنجاح';
        $this->closeModal();
    }

    public function confirmDelete(int $userId): void
    {
        $this->deleteConfirmId = (string) $userId;
        $this->showModal = 'delete';
    }

    public function deleteOwner(): void
    {
        if (! $this->deleteConfirmId) return;
        User::findOrFail((int) $this->deleteConfirmId)->delete();
        $this->successMessage = 'تم حذف المالك بنجاح';
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->selectedOwner = null;
        $this->createStep = 1;
        $this->editOwnerId = 0;
        $this->deleteConfirmId = '';
    }

    public function openOwnerDetails(int $userId): void
    {
        $user = User::withCount('stations')->findOrFail($userId);

        $this->selectedOwner = [
            'id'              => $user->id,
            'full_name'       => $user->full_name,
            'phone'           => $user->phone,
            'email'           => $user->email,
            'is_active'       => $user->is_active,
            'approval_status' => $user->approval_status,
            'created_at'      => $user->created_at?->format('Y-m-d H:i'),
            'stations_count'  => $user->stations_count ?? 0,
        ];
        $this->showModal = 'details';
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->forceFill(['is_active' => ! $user->is_active])->save();
        $this->successMessage = $user->is_active ? 'تم تفعيل المالك' : 'تم تعطيل المالك';
    }

    public function render()
    {
        $query = User::where('user_role', 'station_owner')
            ->withCount('stations');

        if ($this->search) {
            $query->where(fn($q) => $q->where('full_name', 'like', "%{$this->search}%")->orWhere('phone', 'like', "%{$this->search}%"));
        }
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return view('livewire.admin.users.owners-list-page', [
            'owners' => $query->latest('created_at')->paginate(15),
        ]);
    }
}
