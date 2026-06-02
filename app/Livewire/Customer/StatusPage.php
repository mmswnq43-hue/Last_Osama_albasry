<?php

namespace App\Livewire\Customer;

use App\Models\BankAccount;
use App\Models\Subscription;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('اشتراكاتي - غازي')]
class StatusPage extends Component
{
    // ── Plan wizard steps: 0=list, 1=select plan, 2=choose activation type ──
    public int    $step         = 0;
    public string $selectedPlan = '';
    public string $scheduleType = ''; // 'immediate' | 'replace' | 'after'

    // ── Cancel confirmation ──────────────────────────────
    public ?int $cancelConfirmId = null;

    // ─────────────────────────────────────────────────────
    // Plan modal
    // ─────────────────────────────────────────────────────
    public function openPlanModal(): void
    {
        $this->step         = 1;
        $this->selectedPlan = '';
        $this->scheduleType = '';
        $this->resetValidation();
    }

    public function closePlanModal(): void
    {
        $this->step = 0;
    }

    public function selectPlan(string $plan): void
    {
        $this->selectedPlan = $plan;
    }

    /** Step 1 → next: if active subscription exists show type choice, else go pay */
    public function nextFromPlan(): void
    {
        if (! $this->selectedPlan) {
            $this->addError('plan', 'يرجى اختيار باقة أولاً');
            return;
        }

        $activeSub = $this->getActiveSub();

        if ($activeSub) {
            $this->step = 2;
        } else {
            $this->saveAndRedirect('immediate');
        }
    }

    /** Step 2 → user chose activation type */
    public function chooseType(string $type): void
    {
        $this->scheduleType = $type;
        $this->saveAndRedirect($type);
    }

    private function saveAndRedirect(string $type): void
    {
        $activeSub = $this->getActiveSub();

        session([
            'sub_request' => [
                'plan'          => $this->selectedPlan,
                'type'          => $type,
                'active_sub_id' => $activeSub?->id,
                'active_end'    => $activeSub?->end_date?->toDateTimeString(),
            ],
        ]);

        $this->step = 0;
        $this->redirectRoute('customer.pay', navigate: true);
    }

    // ─────────────────────────────────────────────────────
    // Cancel subscription
    // ─────────────────────────────────────────────────────
    public function confirmCancel(int $id): void
    {
        $this->cancelConfirmId = $id;
    }

    public function cancelConfirmClose(): void
    {
        $this->cancelConfirmId = null;
    }

    public function cancelSubscription(): void
    {
        if (! $this->cancelConfirmId) return;

        $sub = Subscription::where('user_id', Auth::id())
            ->whereIn('status', ['active', 'scheduled', 'pending'])
            ->find($this->cancelConfirmId);

        if ($sub) {
            $sub->update(['status' => 'cancelled']);
        }

        $this->cancelConfirmId = null;
    }

    // ─────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────
    private function getActiveSub(): ?Subscription
    {
        // يبحث عن أي اشتراك قائم بأولوية: active → scheduled → pending
        // لعرض خيارات الاستبدال حتى لو الاشتراك لم يوافق عليه الأدمن بعد
        return Subscription::where('user_id', Auth::id())
            ->whereIn('status', ['active', 'scheduled', 'pending'])
            ->orderByRaw("FIELD(status, 'active', 'scheduled', 'pending')")
            ->latest('id')
            ->first();
    }

    public function render()
    {
        $user          = Auth::user();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->latest('id')
            ->get();

        $activeSub = $subscriptions->whereIn('status', ['active', 'scheduled'])->first();

        return view('livewire.customer.status-page', [
            'user'          => $user,
            'subscriptions' => $subscriptions,
            'activeSub'     => $activeSub,
            'plans'         => app(GasYemenSubscriptionService::class)->plans(),
            'bankAccounts'  => BankAccount::where('is_active', true)->get(),
        ]);
    }
}
