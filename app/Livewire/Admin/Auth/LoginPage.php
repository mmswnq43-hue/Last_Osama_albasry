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
    public string $email = '';
    public string $password = '';
    public string $error = '';

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('email', $this->email)->first();

        if (! $user || ! Hash::check($this->password, $user->password_hash)) {
            $this->error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
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
