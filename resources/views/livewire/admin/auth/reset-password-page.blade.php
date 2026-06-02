<div style="width:100%;max-width:440px;margin:0 auto;padding:0 16px;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(24px);border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 32px 80px rgba(0,0,0,0.5);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 40%,#f97316 100%);padding:36px 32px 28px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>

            <div style="width:72px;height:72px;background:rgba(255,255,255,0.18);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:1px solid rgba(255,255,255,0.2);">
                <svg width="34" height="34" fill="white" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z"/>
                </svg>
            </div>
            <h1 style="color:white;font-size:1.45rem;font-weight:800;margin:0 0 6px;">تعيين كلمة مرور جديدة</h1>
            <p style="color:rgba(255,255,255,0.7);font-size:0.82rem;margin:0;">نظام غازي للإدارة</p>
        </div>

        <div style="padding:30px 32px;">

            @if($done)
                {{-- Success --}}
                <div style="text-align:center;padding:10px 0;">
                    <div style="width:64px;height:64px;background:rgba(34,197,94,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg width="32" height="32" fill="#4ade80" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <h3 style="color:#4ade80;margin:0 0 10px;font-size:1.1rem;">تم تغيير كلمة المرور!</h3>
                    <p style="color:rgba(255,255,255,0.55);font-size:0.85rem;line-height:1.6;margin:0 0 24px;">
                        يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.
                    </p>
                    <a href="{{ route('login') }}" wire:navigate
                       style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:12px;padding:13px 28px;font-size:0.9rem;text-decoration:none;font-family:'Tajawal',sans-serif;">
                        <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
                        تسجيل الدخول الآن
                    </a>
                </div>

            @else
                @if($error)
                <div style="display:flex;align-items:flex-start;gap:10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#fca5a5;border-radius:12px;padding:13px 16px;font-size:0.84rem;margin-bottom:20px;line-height:1.5;">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <div>
                        {{ $error }}
                        @if(str_contains($error, 'انتهت') || str_contains($error, 'غير صالح'))
                            <br><a href="{{ route('admin.password.forgot') }}" wire:navigate style="color:#f97316;font-size:0.78rem;">← طلب رابط جديد</a>
                        @endif
                    </div>
                </div>
                @endif

                <form wire:submit="submit" style="display:flex;flex-direction:column;gap:18px;">

                    {{-- New password --}}
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:600;margin-bottom:8px;">
                            🔒 كلمة المرور الجديدة
                        </label>
                        <div style="position:relative;">
                            <input type="password" wire:model="password" placeholder="6 أحرف على الأقل"
                                id="new-pass"
                                style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:12px;padding:12px 44px 12px 44px;font-size:0.9rem;font-family:'Tajawal',sans-serif;outline:none;transition:all 0.25s;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                            <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.35);">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            </div>
                            <button type="button" onclick="toggleNewPass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.4);padding:2px;">
                                <svg id="eye2" width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                        </div>
                        @error('password')
                        <span style="color:#fca5a5;font-size:0.76rem;margin-top:5px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label style="display:block;color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:600;margin-bottom:8px;">
                            🔒 تأكيد كلمة المرور
                        </label>
                        <div style="position:relative;">
                            <input type="password" wire:model="password_confirmation" placeholder="أعد إدخال كلمة المرور"
                                style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:12px;padding:12px 16px 12px 44px;font-size:0.9rem;font-family:'Tajawal',sans-serif;outline:none;transition:all 0.25s;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                            <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.35);">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:13px;padding:13px;font-size:0.92rem;border:none;cursor:pointer;font-family:'Tajawal',sans-serif;box-shadow:0 6px 20px rgba(249,115,22,0.3);transition:all 0.25s;margin-top:4px;">
                        <span wire:loading.remove style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            حفظ كلمة المرور الجديدة
                        </span>
                        <span wire:loading style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="18" height="18" fill="white" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                            جاري الحفظ...
                        </span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
<script>
function toggleNewPass() {
    const inp = document.getElementById('new-pass');
    const ico = document.getElementById('eye2');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
    } else {
        inp.type = 'password';
        ico.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}
</script>
