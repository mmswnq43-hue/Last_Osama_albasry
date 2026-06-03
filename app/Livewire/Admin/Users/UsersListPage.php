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
#[Title('إدارة العملاء - غازي')]
class UsersListPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?array $selectedUser = null;
    public string $showModal = '';
    public string $successMessage = '';

    public array $createForm = [
        'full_name'     => '',
        'phone'         => '',
        'email'         => '',
        'password'      => '',
        'vehicle_type'  => '',
        'engine_number' => '',
    ];
    public string $createError = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openUserDetails(int $userId): void
    {
        $user = User::findOrFail($userId);
        $subscription = Subscription::where('user_id', $userId)->where('status', 'active')->where('end_date', '>=', now())->first();

        $this->selectedUser = [
            'id'               => $user->id,
            'full_name'        => $user->full_name,
            'phone'            => $user->phone,
            'is_active'        => $user->is_active,
            'approval_status'  => $user->approval_status,
            'rejection_reason' => $user->rejection_reason,
            'vehicle_type'     => $user->vehicle_type,
            'engine_number'    => $user->engine_number,
            'created_at'       => $user->created_at?->format('Y-m-d H:i'),
            'subscription'     => $subscription ? [
                'plan_type' => $subscription->plan_type,
                'status'    => $subscription->status,
                'end_date'  => $subscription->end_date?->format('Y-m-d'),
            ] : null,
        ];
        $this->showModal = 'details';
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->forceFill(['is_active' => ! $user->is_active])->save();
        $this->successMessage = $user->is_active ? 'تم تفعيل العميل' : 'تم تعطيل العميل';
    }

    public function openCreate(): void
    {
        $this->createForm = ['full_name' => '', 'phone' => '', 'email' => '', 'password' => '', 'vehicle_type' => '', 'engine_number' => ''];
        $this->createError = '';
        $this->showModal = 'create';
    }

    public function createUser(): void
    {
        $this->createError = '';
        $this->validate([
            'createForm.full_name'     => 'required|string|max:100',
            'createForm.phone'         => 'required|string|max:20|unique:users,phone',
            'createForm.email'         => 'nullable|email|max:150|unique:users,email',
            'createForm.password'      => 'required|string|min:6',
            'createForm.vehicle_type'  => 'nullable|string|max:100',
            'createForm.engine_number' => 'nullable|string|max:100',
        ]);

        User::create([
            'full_name'       => $this->createForm['full_name'],
            'phone'           => $this->createForm['phone'],
            'email'           => $this->createForm['email'] ?: null,
            'user_role'       => 'customer',
            'password_hash'   => Hash::make($this->createForm['password']),
            'vehicle_type'    => $this->createForm['vehicle_type'] ?: null,
            'engine_number'   => $this->createForm['engine_number'] ?: null,
            'is_active'       => true,
            'approval_status' => 'approved',
            'created_at'      => now(),
        ]);

        $this->successMessage = 'تم إنشاء حساب العميل بنجاح';
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->selectedUser = null;
    }

    public function render()
    {
        $query = User::where('user_role', 'customer');

        if ($this->search) {
            $query->where(fn($q) => $q->where('full_name', 'like', "%{$this->search}%")->orWhere('phone', 'like', "%{$this->search}%"));
        }
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return view('livewire.admin.users.users-list-page', [
            'users' => $query->latest('created_at')->paginate(15),
        ]);
    }
}
