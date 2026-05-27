<div style="width:100%;max-width:420px;margin:0 auto;padding:0 16px;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(20px);border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 64px rgba(0,0,0,0.4);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1e3a8a,#f97316);padding:36px 32px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="width:70px;height:70px;background:rgba(255,255,255,0.2);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="36" height="36" fill="white" viewBox="0 0 24 24">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
            </div>
            <h1 style="color:white;font-size:1.4rem;font-weight:800;">استعادة كلمة المرور</h1>
            <p style="color:rgba(255,255,255,0.7);font-size:0.82rem;margin-top:4px;">غـازي — لوحة الإدارة</p>
        </div>

        {{-- Body --}}
        <div style="padding:28px 32px;">

            @if($success)
                <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;border-radius:10px;padding:16px;font-size:0.85rem;text-align:center;margin-bottom:18px;">
                    ✅ {{ $success }}
                </div>
                <a href="{{ route('admin.login') }}" wire:navigate
                   style="display:block;text-align:center;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:11px;padding:13px;font-size:0.9rem;text-decoration:none;margin-top:8px;font-family:'Tajawal',sans-serif;">
                    العودة لتسجيل الدخول
                </a>
            @else
                @if($error)
                <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:12px 14px;font-size:0.82rem;margin-bottom:18px;">
                    ⚠️ {{ $error }}
                </div>
                @endif

                <p style="color:rgba(255,255,255,0.6);font-size:0.84rem;margin-bottom:20px;line-height:1.6;">
                    أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.
                </p>

                <form wire:submit="send" style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.7);font-size:0.82rem;font-weight:600;margin-bottom:7px;">البريد الإلكتروني</label>
                        <input type="email" wire:model="email" placeholder="أدخل بريدك الإلكتروني" dir="ltr"
                            style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:11px;padding:11px 14px;font-size:0.875rem;font-family:'Tajawal',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)'">
                        @error('email') <span style="color:#fca5a5;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:11px;padding:13px;font-size:0.9rem;border:none;cursor:pointer;font-family:'Tajawal',sans-serif;box-shadow:0 6px 20px rgba(249,115,22,0.4);">
                        <span wire:loading.remove>إرسال رابط الاستعادة</span>
                        <span wire:loading>⏳ جاري الإرسال...</span>
                    </button>
                </form>

                <div style="text-align:center;margin-top:20px;">
                    <a href="{{ route('admin.login') }}" wire:navigate
                       style="color:rgba(255,255,255,0.5);font-size:0.82rem;text-decoration:none;"
                       onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                        ← العودة لتسجيل الدخول
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
