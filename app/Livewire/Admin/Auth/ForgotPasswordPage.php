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
    public string $phone   = '';
    public string $error   = '';
    public string $success = '';

    public function send(): void
    {
        $this->error   = '';
        $this->success = '';

        $this->validate([
            'phone' => 'required|string|min:9|max:15',
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.min'      => 'رقم الجوال يجب أن يكون 9 أرقام على الأقل',
        ]);

        $user = User::where('phone', $this->phone)
                    ->where('user_role', 'admin')
                    ->first();

        // Always show success (security — don't reveal if phone exists)
        if (! $user || ! $user->email) {
            $this->success = 'إذا كان الرقم مسجلاً وله بريد إلكتروني، ستصلك رسالة خلال دقائق.';
            return;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::send([], [], function ($msg) use ($user, $resetUrl) {
                $msg->to($user->email)
                    ->subject('إعادة تعيين كلمة المرور — غازي')
                    ->html($this->buildEmail($resetUrl, $user->full_name));
            });
        } catch (\Exception $e) {
            // Mail not configured — still show success silently
            logger()->error('Password reset mail failed: ' . $e->getMessage());
        }

        $this->success = 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني المسجل.';
    }

    private function buildEmail(string $url, string $name): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head><meta charset="UTF-8">
        <style>
          body { font-family: Tahoma, Arial, sans-serif; background: #0f172a; margin: 0; padding: 20px; }
          .wrap { max-width: 500px; margin: 0 auto; background: #1e293b; border-radius: 16px; overflow: hidden; }
          .hdr  { background: linear-gradient(135deg, #1e3a8a, #f97316); padding: 32px; text-align: center; color: white; }
          .hdr h1 { margin: 0 0 6px; font-size: 1.8rem; }
          .body { padding: 32px; color: #e2e8f0; line-height: 1.7; }
          .btn  { display: inline-block; background: linear-gradient(135deg,#f97316,#1d4ed8);
                  color: white; padding: 14px 30px; border-radius: 10px;
                  text-decoration: none; font-weight: bold; margin: 20px 0; }
          .note { color: #94a3b8; font-size: 0.82rem; margin-top: 20px; }
        </style>
        </head>
        <body>
        <div class="wrap">
          <div class="hdr">
            <h1>⛽ غـازي</h1>
            <p>إعادة تعيين كلمة المرور</p>
          </div>
          <div class="body">
            <p>مرحباً <b>{$name}</b>،</p>
            <p>تلقينا طلباً لإعادة تعيين كلمة مرور حسابك في لوحة إدارة غازي.</p>
            <p>اضغط على الزر أدناه لإنشاء كلمة مرور جديدة:</p>
            <div style="text-align:center">
              <a class="btn" href="{$url}">إعادة تعيين كلمة المرور</a>
            </div>
            <p class="note">⏱ هذا الرابط صالح لمدة <b>60 دقيقة</b> فقط.</p>
            <p class="note">إن لم تطلب إعادة التعيين، تجاهل هذه الرسالة.</p>
          </div>
        </div>
        </body></html>
        HTML;
    }

    public function render()
    {
        return view('livewire.admin.auth.forgot-password-page');
    }
}
