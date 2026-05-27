<?php

namespace App\Livewire\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin-login')]
#[Title('إعادة تعيين كلمة المرور - غازي')]
class ResetPasswordPage extends Component
{
    #[Url]
    public string $token = '';

    #[Url]
    public string $email = '';

    public string $password = '';
    public string $password_confirmation = '';
    public string $error = '';
    public bool $done = false;

    public function mount(): void
    {
        if (! $this->token || ! $this->email) {
            abort(404);
        }
    }

    public function submit(): void
    {
        $this->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.min'       => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقتين',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (! $record || ! hash_equals($record->token, hash('sha256', $this->token))) {
            $this->error = 'الرابط غير صالح أو منتهي الصلاحية';
            return;
        }

        // Token expires after 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $this->email)->delete();
            $this->error = 'انتهت صلاحية الرابط، يرجى طلب رابط جديد';
            return;
        }

        $user = User::where('email', $this->email)->where('user_role', 'admin')->first();

        if (! $user) {
            $this->error = 'المستخدم غير موجود';
            return;
        }

        $user->password_hash = Hash::make($this->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $this->email)->delete();

        $this->done = true;
    }

    public function render()
    {
        return view('livewire.admin.auth.reset-password-page');
    }
}
