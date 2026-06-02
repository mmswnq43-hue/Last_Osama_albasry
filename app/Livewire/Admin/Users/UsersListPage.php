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
