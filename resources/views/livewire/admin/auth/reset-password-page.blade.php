<div style="width:100%;max-width:420px;margin:0 auto;padding:0 16px;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(20px);border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 64px rgba(0,0,0,0.4);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1e3a8a,#f97316);padding:36px 32px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="width:70px;height:70px;background:rgba(255,255,255,0.2);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="36" height="36" fill="white" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z"/>
                </svg>
            </div>
            <h1 style="color:white;font-size:1.4rem;font-weight:800;">تعيين كلمة مرور جديدة</h1>
            <p style="color:rgba(255,255,255,0.7);font-size:0.82rem;margin-top:4px;">غـازي — لوحة الإدارة</p>
        </div>

        {{-- Body --}}
        <div style="padding:28px 32px;">

            @if($done)
                <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;border-radius:10px;padding:16px;font-size:0.85rem;text-align:center;margin-bottom:18px;">
                    ✅ تم تغيير كلمة المرور بنجاح!
                </div>
                <a href="{{ route('admin.login') }}" wire:navigate
                   style="display:block;text-align:center;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:11px;padding:13px;font-size:0.9rem;text-decoration:none;font-family:'Tajawal',sans-serif;">
                    تسجيل الدخول الآن
                </a>
            @else
                @if($error)
                <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:12px 14px;font-size:0.82rem;margin-bottom:18px;">
                    ⚠️ {{ $error }}
                    @if(str_contains($error, 'انتهت') || str_contains($error, 'غير صالح'))
                        <br><a href="{{ route('admin.password.forgot') }}" wire:navigate style="color:#f97316;">طلب رابط جديد</a>
                    @endif
                </div>
                @endif

                <form wire:submit="submit" style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.7);font-size:0.82rem;font-weight:600;margin-bottom:7px;">كلمة المرور الجديدة</label>
                        <input type="password" wire:model="password" placeholder="6 أحرف على الأقل"
                            style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:11px;padding:11px 14px;font-size:0.875rem;font-family:'Tajawal',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)'">
                        @error('password') <span style="color:#fca5a5;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.7);font-size:0.82rem;font-weight:600;margin-bottom:7px;">تأكيد كلمة المرور</label>
                        <input type="password" wire:model="password_confirmation" placeholder="أعد كتابة كلمة المرور"
                            style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:11px;padding:11px 14px;font-size:0.875rem;font-family:'Tajawal',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)'">
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:11px;padding:13px;font-size:0.9rem;border:none;cursor:pointer;font-family:'Tajawal',sans-serif;box-shadow:0 6px 20px rgba(249,115,22,0.4);">
                        <span wire:loading.remove>حفظ كلمة المرور</span>
                        <span wire:loading>⏳ جاري الحفظ...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
