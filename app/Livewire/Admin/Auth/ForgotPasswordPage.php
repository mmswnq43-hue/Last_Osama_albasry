<?php

namespace App\Livewire\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin-login')]
#[Title('نسيت كلمة المرور - غازي')]
class ForgotPasswordPage extends Component
{
    public string $email = '';
    public string $error = '';
    public string $success = '';

    public function send(): void
    {
        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email'    => 'صيغة البريد الإلكتروني غير صحيحة',
        ]);

        $user = User::where('email', $this->email)
                    ->where('user_role', 'admin')
                    ->first();

        // Always show success to avoid email enumeration
        if (! $user) {
            $this->success = 'إذا كان البريد مسجلاً، ستصلك رسالة خلال دقائق.';
            return;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        $resetUrl = route('admin.password.reset', ['token' => $token, 'email' => $this->email]);

        Mail::send([], [], function ($message) use ($resetUrl) {
            $message->to($this->email)
                    ->subject('إعادة تعيين كلمة المرور — غازي')
                    ->html($this->buildEmailHtml($resetUrl));
        });

        $this->success = 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني.';
    }

    private function buildEmailHtml(string $resetUrl): string
    {
        return '<!DOCTYPE html><html dir="rtl" lang="ar">
<head><meta charset="UTF-8"><style>
body{font-family:Tahoma,Arial,sans-serif;background:#0f172a;margin:0;padding:20px;}
.card{max-width:480px;margin:0 auto;background:#1e293b;border-radius:16px;overflow:hidden;}
.header{background:linear-gradient(135deg,#1e3a8a,#f97316);padding:32px;text-align:center;color:white;}
.header h1{margin:0;font-size:1.6rem;}
.body{padding:32px;color:#e2e8f0;}
.btn{display:inline-block;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;padding:14px 28px;border-radius:10px;text-decoration:none;font-weight:bold;margin:20px 0;}
.footer{color:#64748b;font-size:0.8rem;margin-top:20px;}
</style></head>
<body><div class="card">
<div class="header"><h1>غـازي</h1><p>إعادة تعيين كلمة المرور</p></div>
<div class="body">
<p>تلقينا طلباً لإعادة تعيين كلمة مرور حساب الإدارة.</p>
<p>اضغط على الزر أدناه لإنشاء كلمة مرور جديدة:</p>
<div style="text-align:center"><a href="' . $resetUrl . '" class="btn">إعادة تعيين كلمة المرور</a></div>
<p class="footer">هذا الرابط صالح لمدة 60 دقيقة. إن لم تطلب إعادة التعيين تجاهل هذه الرسالة.</p>
</div></div></body></html>';
    }

    public function render()
    {
        return view('livewire.admin.auth.forgot-password-page');
    }
}
