<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin-login')]
#[Title('تسجيل الدخول - غازي')]
class LoginPage extends Component
{
    public string $phone = '';
    public string $password = '';
    public string $error = '';

    public function login(): void
    {
        $this->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('phone', $this->phone)->first();

        if (! $user || ! Hash::check($this->password, $user->password_hash)) {
            $this->error = 'رقم الهاتف أو كلمة المرور غير صحيحة';
            return;
        }

        if ($user->user_role !== 'admin') {
            $this->error = 'ليس لديك صلاحية الوصول للوحة الإدارة';
            return;
        }

        if (! $user->is_active) {
            $this->error = 'الحساب غير نشط';
            return;
        }

        Auth::login($user);
        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.login-page');
    }
}
