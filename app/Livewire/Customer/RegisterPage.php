<?php

namespace App\Livewire\Customer;

use App\Models\BankAccount;
use App\Services\GasYemenSubscriptionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('إنشاء حساب - غازي')]
class RegisterPage extends Component
{
    public int $step = 1;

    // ── Step 1: personal data ──────────────────────────────
    public string $full_name             = '';
    public string $phone                 = '';
    public string $email                 = '';
    public string $vehicle_type          = '';
    public string $engine_number         = '';
    public string $password              = '';
    public string $password_confirmation = '';

    // ── Step 2: subscription ───────────────────────────────
    public string $selectedPlan = '';

    public string $error = '';

    protected $messages = [
        'full_name.required'    => 'الاسم الكامل مطلوب',
        'phone.required'        => 'رقم الجوال مطلوب',
        'phone.unique'          => 'رقم الجوال مسجّل مسبقاً',
        'password.required'     => 'كلمة المرور مطلوبة',
        'password.min'          => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        'password.confirmed'    => 'تأكيد كلمة المرور غير مطابق',
        'selectedPlan.required' => 'يرجى اختيار باقة اشتراك',
    ];

    protected function rulesStep1(): array
    {
        return [
            'full_name'    => 'required|string|max:150',
            'phone'        => 'required|string|min:9|max:15|unique:users,phone',
            'email'        => 'nullable|email|max:150|unique:users,email',
            'vehicle_type' => 'nullable|string|max:60',
            'engine_number'=> 'nullable|string|max:60',
            'password'     => 'required|string|min:6|confirmed',
        ];
    }

    public function nextFromData(): void
    {
        $this->validate($this->rulesStep1());
        $this->error = '';
        $this->step  = 2;
    }

    public function selectPlan(string $planType): void
    {
        $this->selectedPlan = $planType;
    }

    public function nextFromPlan(): void
    {
        $this->validate(['selectedPlan' => 'required|string'], [
            'selectedPlan.required' => 'يرجى اختيار باقة اشتراك',
        ]);

        // حفظ بيانات الخطوتين 1 و 2 في الجلسة
        // لتستخدمها صفحة رفع السند (controller عادي)
        session([
            'reg_data' => [
                'full_name'    => $this->full_name,
                'phone'        => $this->phone,
                'email'        => $this->email ?: null,
                'vehicle_type' => $this->vehicle_type ?: null,
                'engine_number'=> $this->engine_number ?: null,
                'password'     => $this->password,
                'plan'         => $this->selectedPlan,
            ],
        ]);

        $this->step = 3;
    }

    public function back(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function render()
    {
        return view('livewire.customer.register-page', [
            'planList'     => app(GasYemenSubscriptionService::class)->plans(),
            'bankAccounts' => BankAccount::where('is_active', true)->get(),
        ]);
    }
}
