<?php

namespace App\Livewire\Admin\Subscriptions;

use App\Models\Notification;
use App\Models\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('الاشتراكات - غازي')]
class SubscriptionsListPage extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $planFilter = '';
    public string $successMessage = '';
    public ?array $selectedSub = null;
    public string $showModal = '';

    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPlanFilter(): void { $this->resetPage(); }

    public function approve(int $id): void
    {
        $sub = Subscription::findOrFail($id);
        $sub->forceFill(['status' => 'active'])->save();

        Notification::create([
            'user_id' => $sub->user_id,
            'title' => 'تم تفعيل اشتراكك',
            'message' => 'مبروك! تم قبول اشتراكك وتفعيله بنجاح.',
            'notification_type' => 'subscription_activated',
            'is_important' => true,
        ]);

        $this->successMessage = 'تم تفعيل الاشتراك بنجاح';
    }

    public function reject(int $id): void
    {
        $sub = Subscription::findOrFail($id);
        $sub->forceFill(['status' => 'rejected'])->save();

        Notification::create([
            'user_id' => $sub->user_id,
            'title' => 'تم رفض اشتراكك',
            'message' => 'نأسف، تم رفض طلب الاشتراك. يرجى التواصل مع الإدارة.',
            'notification_type' => 'subscription_rejected',
            'is_important' => true,
        ]);

        $this->successMessage = 'تم رفض الاشتراك';
    }

    public function viewReceipt(int $id): void
    {
        $sub = Subscription::with('user:id,full_name,phone')->findOrFail($id);
        $this->selectedSub = [
            'id' => $sub->id,
            'plan_type' => $sub->plan_type,
            'price' => $sub->price,
            'status' => $sub->status,
            'start_date' => $sub->start_date?->format('Y-m-d'),
            'end_date' => $sub->end_date?->format('Y-m-d'),
            'payment_receipt_image' => $sub->payment_receipt_image,
            'user_name' => $sub->user?->full_name,
            'user_phone' => $sub->user?->phone,
            'created_at' => $sub->created_at?->format('Y-m-d H:i'),
        ];
        $this->showModal = 'receipt';
    }

    public function closeModal(): void
    {
        $this->showModal = '';
        $this->selectedSub = null;
    }

    public function render()
    {
        $query = Subscription::with('user:id,full_name,phone')->latest('created_at');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        if ($this->planFilter) {
            $query->where('plan_type', $this->planFilter);
        }

        return view('livewire.admin.subscriptions.subscriptions-list-page', [
            'subscriptions' => $query->paginate(15),
        ]);
    }
}
