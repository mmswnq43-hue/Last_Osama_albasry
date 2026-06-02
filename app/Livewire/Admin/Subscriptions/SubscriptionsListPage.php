<?php

namespace App\Livewire\Admin\Subscriptions;

use App\Models\Notification;
use App\Models\Subscription;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('الاشتراكات - غازي')]
class SubscriptionsListPage extends Component
{
    use WithPagination;

    public string  $statusFilter  = '';
    public string  $planFilter    = '';
    public string  $successMessage = '';
    public ?array  $selectedSub   = null;
    public string  $showModal     = '';
    public ?int    $rejectConfirmId = null;

    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPlanFilter(): void   { $this->resetPage(); }

    // ─────────────────────────────────────────────────────
    // Approve subscription
    // ─────────────────────────────────────────────────────
    public function approve(int $id): void
    {
        $sub   = Subscription::with('user')->findOrFail($id);
        $plans = app(GasYemenSubscriptionService::class)->plans();
        $note  = $sub->notes ?? '';
        $dur   = $plans[$sub->plan_type]['duration_months'] ?? 1;

        if (str_contains($note, 'renewal:replace')) {
            // إلغاء الاشتراك النشط الحالي للمستخدم
            Subscription::where('user_id', $sub->user_id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            $sub->update([
                'status'     => 'active',
                'start_date' => now(),
                'end_date'   => now()->addMonths($dur),
            ]);
            $msg = 'تم تفعيل الاشتراك الجديد واستبدال القديم';

        } elseif (str_contains($note, 'renewal:after')) {
            // الاشتراك يبدأ بعد انتهاء الحالي
            $sub->update([
                'status'   => 'scheduled',
                'end_date' => $sub->start_date->addMonths($dur),
            ]);
            $msg = 'تم جدولة الاشتراك ليبدأ بعد انتهاء الحالي';

        } else {
            // اشتراك جديد عادي (تسجيل أولي)
            $sub->update([
                'status'     => 'active',
                'start_date' => now(),
                'end_date'   => now()->addMonths($dur),
            ]);
            $msg = 'تم تفعيل الاشتراك بنجاح';
        }

        if ($sub->user) {
            Notification::create([
                'user_id'           => $sub->user_id,
                'title'             => 'تم قبول اشتراكك',
                'message'           => 'تم قبول طلب الاشتراك في باقة '.$sub->plan_type.' بنجاح.',
                'notification_type' => 'subscription_activated',
                'is_important'      => true,
            ]);
        }

        $this->successMessage = $msg;
        $this->showModal      = '';
        $this->selectedSub    = null;
    }

    // ─────────────────────────────────────────────────────
    // Reject subscription
    // ─────────────────────────────────────────────────────
    public function reject(int $id): void
    {
        $sub = Subscription::with('user')->findOrFail($id);
        $sub->update(['status' => 'cancelled']);

        if ($sub->user) {
            Notification::create([
                'user_id'           => $sub->user_id,
                'title'             => 'تم رفض طلب الاشتراك',
                'message'           => 'نأسف، تم رفض طلب الاشتراك في باقة '.$sub->plan_type.'. يرجى التواصل مع الإدارة.',
                'notification_type' => 'subscription_rejected',
                'is_important'      => true,
            ]);
        }

        $this->successMessage   = 'تم رفض الاشتراك';
        $this->rejectConfirmId  = null;
    }

    // ─────────────────────────────────────────────────────
    // View receipt modal
    // ─────────────────────────────────────────────────────
    public function viewReceipt(int $id): void
    {
        $sub = Subscription::with('user:id,full_name,phone')->findOrFail($id);

        $receiptUrl = null;
        if ($sub->payment_receipt_image) {
            $disk = config('filesystems.default', 'public');
            try {
                $receiptUrl = $disk === 's3'
                    ? Storage::disk('s3')->temporaryUrl($sub->payment_receipt_image, now()->addMinutes(30))
                    : Storage::disk($disk)->url($sub->payment_receipt_image);
            } catch (\Throwable) {
                $receiptUrl = null;
            }
        }

        $this->selectedSub = [
            'id'          => $sub->id,
            'plan_type'   => $sub->plan_type,
            'price'       => $sub->price,
            'status'      => $sub->status,
            'notes'       => $sub->notes,
            'start_date'  => $sub->start_date?->format('Y-m-d'),
            'end_date'    => $sub->end_date?->format('Y-m-d'),
            'receipt_url' => $receiptUrl,
            'user_name'   => $sub->user?->full_name,
            'user_phone'  => $sub->user?->phone,
            'created_at'  => $sub->created_at?->format('Y-m-d H:i'),
        ];
        $this->showModal = 'receipt';
    }

    public function confirmReject(int $id): void { $this->rejectConfirmId = $id; }

    public function closeModal(): void
    {
        $this->showModal       = '';
        $this->selectedSub     = null;
        $this->rejectConfirmId = null;
    }

    public function render()
    {
        $query = Subscription::with('user:id,full_name,phone')->latest('id');

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
