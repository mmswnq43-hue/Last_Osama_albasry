<?php

namespace App\Livewire\Admin\Users;

use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('طلبات الانضمام - غازي')]
class PendingApprovalsPage extends Component
{
    public array $users = [];
    public ?array $selectedUser = null;
    public string $rejectionReason = '';
    public string $showModal = ''; // 'approve' | 'reject' | ''
    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->loadUsers();
    }

    private function loadUsers(): void
    {
        $this->users = User::where('approval_status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function (User $user) {
                $subscription = Subscription::where('user_id', $user->id)->latest()->first();
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'user_role' => $user->user_role,
                    'vehicle_type' => $user->vehicle_type,
                    'engine_number' => $user->engine_number,
                    'created_at' => $user->created_at?->format('Y-m-d H:i'),
                    'subscription' => $subscription ? [
                        'id' => $subscription->id,
                        'plan_type' => $subscription->plan_type,
                        'price' => $subscription->price,
                        'status' => $subscription->status,
                        'receipt' => $subscription->payment_receipt_image,
                    ] : null,
                ];
            })
            ->toArray();
    }

    public function openApproveModal(int $userId): void
    {
        $this->selectedUser = collect($this->users)->firstWhere('id', $userId);
        $this->showModal = 'approve';
    }

    public function openRejectModal(int $userId): void
    {
        $this->selectedUser = collect($this->users)->firstWhere('id', $userId);
        $this->rejectionReason = '';
        $this->showModal = 'reject';
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->selectedUser = null;
        $this->rejectionReason = '';
    }

    public function approveUser(): void
    {
        if (! $this->selectedUser) return;

        $user = User::findOrFail($this->selectedUser['id']);
        $user->forceFill([
            'approval_status' => 'approved',
            'is_active' => true,
            'rejection_reason' => null,
        ])->save();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'تم قبول حسابك',
            'message' => 'مرحباً '.$user->full_name.'! تم قبول حسابك بنجاح.',
            'notification_type' => 'account_approved',
            'is_important' => true,
        ]);

        $this->successMessage = 'تم قبول المستخدم '.$user->full_name.' بنجاح';
        $this->closeModal();
        $this->loadUsers();
    }

    public function rejectUser(): void
    {
        $this->validate(['rejectionReason' => 'required|min:10'], [
            'rejectionReason.required' => 'يجب ذكر سبب الرفض',
            'rejectionReason.min' => 'السبب يجب أن يكون 10 أحرف على الأقل',
        ]);

        if (! $this->selectedUser) return;

        $user = User::findOrFail($this->selectedUser['id']);
        $user->forceFill([
            'approval_status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $this->rejectionReason,
        ])->save();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'تم رفض حسابك',
            'message' => 'نأسف، تم رفض حسابك. السبب: '.$this->rejectionReason,
            'notification_type' => 'account_rejected',
            'is_important' => true,
        ]);

        $this->successMessage = 'تم رفض المستخدم '.$user->full_name;
        $this->closeModal();
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.admin.users.pending-approvals-page');
    }
}
