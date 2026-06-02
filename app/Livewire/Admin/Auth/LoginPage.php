<?php

namespace App\Livewire\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin-login')]
#[Title('تسجيل الدخول - غازي')]
class LoginPage extends Component
{
    public string $phone    = '';
    public string $password = '';
    public bool   $remember = false;
    public string $error    = '';

    public function login(): void
    {
        $this->error = '';

        $this->validate([
            'phone'    => 'required|string|min:9|max:15',
            'password' => 'required|string|min:6',
        ], [
            'phone.required'    => 'رقم الجوال مطلوب',
            'phone.min'         => 'رقم الجوال يجب أن يكون 9 أرقام على الأقل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        // Rate limiting — max 5 attempts per minute
        $key = 'login:' . Str::slug($this->phone) . ':' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->error = "تم تجاوز عدد المحاولات، حاول بعد {$seconds} ثانية";
            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if (! $user || ! Hash::check($this->password, $user->password_hash)) {
            RateLimiter::hit($key, 60);
            $this->error = 'رقم الجوال أو كلمة المرور غير صحيحة';
            return;
        }

        // ── Role-based checks ──────────────────────────────
        if ($user->user_role === 'admin') {
            if (! $user->is_active) {
                $this->error = 'الحساب غير نشط، تواصل مع الدعم الفني';
                return;
            }

            RateLimiter::clear($key);
            Auth::login($user, $this->remember);
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        }

        // ── Customer checks ─────────────────────────────
        if ($user->approval_status === 'pending') {
            $this->error = 'حسابك قيد المراجعة بانتظار موافقة الإدارة.';
            return;
        }

        if ($user->approval_status === 'rejected') {
            $reason = $user->rejection_reason ? ' السبب: '.$user->rejection_reason : '';
            $this->error = 'تم رفض طلب حسابك.'.$reason;
            return;
        }

        if (! $user->is_active) {
            $this->error = 'حسابك غير مفعّل. يرجى التواصل مع الإدارة.';
            return;
        }

        RateLimiter::clear($key);
        Auth::login($user, $this->remember);
        $this->redirect(route('customer.status'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.login-page');
    }
}
