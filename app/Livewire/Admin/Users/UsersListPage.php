<?php

namespace App\Livewire\Admin\Users;

use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('إدارة المستخدمين - غازي')]
class UsersListPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public ?array $selectedUser = null;
    public string $showModal = '';
    public string $newRole = '';
    public string $successMessage = '';

    public array $createForm = [
        'full_name' => '',
        'phone' => '',
        'email' => '',
        'user_role' => 'customer',
        'password' => '',
        'vehicle_type' => '',
    ];
    public string $createError = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedRoleFilter(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openUserDetails(int $userId): void
    {
        $user = User::findOrFail($userId);
        $subscription = Subscription::where('user_id', $userId)->where('status', 'active')->where('end_date', '>=', now())->first();

        $this->selectedUser = [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'phone' => $user->phone,
            'user_role' => $user->user_role,
            'is_active' => $user->is_active,
            'approval_status' => $user->approval_status,
            'rejection_reason' => $user->rejection_reason,
            'vehicle_type' => $user->vehicle_type,
            'engine_number' => $user->engine_number,
            'created_at' => $user->created_at?->format('Y-m-d H:i'),
            'subscription' => $subscription ? [
                'plan_type' => $subscription->plan_type,
                'status' => $subscription->status,
                'end_date' => $subscription->end_date?->format('Y-m-d'),
            ] : null,
        ];
        $this->newRole = $user->user_role;
        $this->showModal = 'details';
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->forceFill(['is_active' => ! $user->is_active])->save();
        $this->successMessage = $user->is_active ? 'تم تفعيل المستخدم' : 'تم تعطيل المستخدم';
    }

    public function updateRole(): void
    {
        if (! $this->selectedUser) return;
        $user = User::findOrFail($this->selectedUser['id']);
        $user->forceFill(['user_role' => $this->newRole])->save();
        $this->successMessage = 'تم تغيير الدور بنجاح';
        $this->closeModal();
    }

    public function openCreate(): void
    {
        $this->createForm = [
            'full_name' => '',
            'phone' => '',
            'email' => '',
            'user_role' => 'customer',
            'password' => '',
            'vehicle_type' => '',
        ];
        $this->createError = '';
        $this->showModal = 'create';
    }

    public function createUser(): void
    {
        $this->createError = '';
        $this->validate([
            'createForm.full_name' => 'required|string|max:100',
            'createForm.phone'     => 'required|string|max:20|unique:users,phone',
            'createForm.email'     => 'nullable|email|max:150|unique:users,email',
            'createForm.user_role' => 'required|in:customer,station_owner,station_worker,admin',
            'createForm.password'  => 'required|string|min:6',
            'createForm.vehicle_type' => 'nullable|string|max:100',
        ]);

        User::create([
            'full_name'       => $this->createForm['full_name'],
            'phone'           => $this->createForm['phone'],
            'email'           => $this->createForm['email'] ?: null,
            'user_role'       => $this->createForm['user_role'],
            'password_hash'   => Hash::make($this->createForm['password']),
            'vehicle_type'    => $this->createForm['vehicle_type'] ?: null,
            'is_active'       => true,
            'approval_status' => 'approved',
            'created_at'      => now(),
        ]);

        $this->successMessage = 'تم إنشاء المستخدم بنجاح';
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->selectedUser = null;
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(fn($q) => $q->where('full_name', 'like', "%{$this->search}%")->orWhere('phone', 'like', "%{$this->search}%"));
        }
        if ($this->roleFilter) {
            $query->where('user_role', $this->roleFilter);
        }
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return view('livewire.admin.users.users-list-page', [
            'users' => $query->latest('created_at')->paginate(15),
        ]);
    }
}
