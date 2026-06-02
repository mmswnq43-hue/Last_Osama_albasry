<div style="width:100%;max-width:440px;margin:0 auto;padding:0 16px;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(24px);border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 32px 80px rgba(0,0,0,0.5);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 40%,#f97316 100%);padding:36px 32px 28px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>

            <div style="width:72px;height:72px;background:rgba(255,255,255,0.18);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:1px solid rgba(255,255,255,0.2);">
                <svg width="34" height="34" fill="white" viewBox="0 0 24 24">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
            </div>
            <h1 style="color:white;font-size:1.45rem;font-weight:800;margin:0 0 6px;">نسيت كلمة المرور</h1>
            <p style="color:rgba(255,255,255,0.7);font-size:0.82rem;margin:0;">نظام غازي للإدارة</p>
        </div>

        <div style="padding:30px 32px;">

            @if($success)
                {{-- Success state --}}
                <div style="text-align:center;padding:10px 0;">
                    <div style="width:64px;height:64px;background:rgba(34,197,94,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg width="32" height="32" fill="#4ade80" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <h3 style="color:#4ade80;margin:0 0 10px;font-size:1.1rem;">تم الإرسال!</h3>
                    <p style="color:rgba(255,255,255,0.6);font-size:0.85rem;line-height:1.6;margin:0 0 24px;">
                        {{ $success }}
                    </p>
                    <a href="{{ route('login') }}" wire:navigate
                       style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:12px;padding:12px 28px;font-size:0.9rem;text-decoration:none;font-family:'Tajawal',sans-serif;">
                        <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                        العودة لتسجيل الدخول
                    </a>
                </div>

            @else
                @if($error)
                <div style="display:flex;align-items:center;gap:10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#fca5a5;border-radius:12px;padding:13px 16px;font-size:0.84rem;margin-bottom:20px;">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $error }}
                </div>
                @endif

                <p style="color:rgba(255,255,255,0.55);font-size:0.85rem;line-height:1.7;margin:0 0 24px;">
                    أدخل رقم جوالك المسجل وسنرسل رابط إعادة تعيين كلمة المرور على بريدك الإلكتروني المرتبط بالحساب.
                </p>

                <form wire:submit="send" style="display:flex;flex-direction:column;gap:18px;">
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:600;margin-bottom:8px;">
                            📱 رقم الجوال
                        </label>
                        <div style="position:relative;">
                            <input type="tel" wire:model="phone" placeholder="أدخل رقم الجوال" dir="ltr"
                                style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:12px;padding:12px 16px 12px 44px;font-size:0.9rem;font-family:'Tajawal',sans-serif;outline:none;transition:all 0.25s;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                            <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.35);">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </div>
                        </div>
                        @error('phone')
                        <span style="color:#fca5a5;font-size:0.76rem;margin-top:5px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:13px;padding:13px;font-size:0.92rem;border:none;cursor:pointer;font-family:'Tajawal',sans-serif;box-shadow:0 6px 20px rgba(249,115,22,0.3);transition:all 0.25s;">
                        <span wire:loading.remove style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            إرسال رابط الاستعادة
                        </span>
                        <span wire:loading style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="18" height="18" fill="white" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                            جاري الإرسال...
                        </span>
                    </button>
                </form>

                <div style="text-align:center;margin-top:22px;">
                    <a href="{{ route('login') }}" wire:navigate
                       style="color:rgba(255,255,255,0.4);font-size:0.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;"
                       onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                        العودة لتسجيل الدخول
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
