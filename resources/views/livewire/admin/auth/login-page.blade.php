<div style="width:100%;max-width:440px;margin:0 auto;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(24px);border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 32px 80px rgba(0,0,0,0.5);">

        {{-- ── Header ── --}}
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 40%,#f97316 100%);padding:32px 24px 28px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-70px;left:-40px;width:220px;height:220px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>

            {{-- Logo icon --}}
            <div style="width:64px;height:64px;background:rgba(255,255,255,0.18);backdrop-filter:blur(10px);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 8px 32px rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.2);">
                <svg width="32" height="32" fill="white" viewBox="0 0 24 24">
                    <path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z"/>
                </svg>
            </div>

            <h1 style="color:white;font-size:1.5rem;font-weight:900;letter-spacing:-0.5px;margin:0 0 4px;text-shadow:0 2px 8px rgba(0,0,0,0.2);">غـازي</h1>
            <p style="color:rgba(255,255,255,0.75);font-size:0.82rem;margin:0;letter-spacing:0.5px;">تسجيل الدخول إلى حسابك</p>
        </div>

        {{-- ── Form ── --}}
        <div style="padding:24px;">

            {{-- Session flash (registered) --}}
            @if(session('registered'))
            <div style="display:flex;align-items:center;gap:10px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.25);color:#86efac;border-radius:12px;padding:12px 14px;font-size:0.82rem;margin-bottom:18px;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>{{ session('registered') }}</span>
            </div>
            @endif

            {{-- Error alert --}}
            @if($error)
            <div style="display:flex;align-items:center;gap:10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#fca5a5;border-radius:12px;padding:12px 14px;font-size:0.82rem;margin-bottom:18px;animation:shake 0.3s;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <span>{{ $error }}</span>
            </div>
            @endif

            <form wire:submit="login" style="display:flex;flex-direction:column;gap:18px;">

                {{-- Phone field --}}
                <div>
                    <label style="display:block;color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:600;margin-bottom:7px;">
                        📱 رقم الجوال
                    </label>
                    <input
                        type="tel"
                        wire:model="phone"
                        placeholder="أدخل رقم الجوال"
                        dir="ltr"
                        autocomplete="username"
                        style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:12px;padding:12px 14px;font-size:0.9rem;font-family:'Tajawal',sans-serif;outline:none;transition:all 0.25s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#f97316';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                    @error('phone')
                    <span style="color:#fca5a5;font-size:0.76rem;margin-top:5px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password field --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;flex-wrap:wrap;gap:4px;">
                        <label style="color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:600;">
                            🔒 كلمة المرور
                        </label>
                        <a href="{{ route('password.forgot') }}" wire:navigate
                           style="color:#f97316;font-size:0.76rem;text-decoration:none;">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                    <div style="position:relative;">
                        <input
                            type="password"
                            wire:model="password"
                            placeholder="أدخل كلمة المرور"
                            autocomplete="current-password"
                            id="pass-input"
                            style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:12px;padding:12px 44px 12px 14px;font-size:0.9rem;font-family:'Tajawal',sans-serif;outline:none;transition:all 0.25s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='#f97316';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
                        {{-- Toggle show/hide --}}
                        <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.4);padding:4px;" title="إظهار/إخفاء">
                            <svg id="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </button>
                    </div>
                    @error('password')
                    <span style="color:#fca5a5;font-size:0.76rem;margin-top:5px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember me --}}
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;">
                    <input type="checkbox" wire:model="remember"
                           style="width:18px;height:18px;accent-color:#f97316;cursor:pointer;flex-shrink:0;">
                    <span style="color:rgba(255,255,255,0.6);font-size:0.84rem;">تذكرني</span>
                </label>

                {{-- Submit button --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:12px;padding:13px;font-size:0.95rem;border:none;cursor:pointer;font-family:'Tajawal',sans-serif;box-shadow:0 8px 24px rgba(249,115,22,0.35);transition:all 0.25s;margin-top:2px;">
                    <span wire:loading.remove style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
                        تسجيل الدخول
                    </span>
                    <span wire:loading style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        <svg width="18" height="18" fill="white" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                        جاري التحقق...
                    </span>
                </button>

            </form>

            {{-- Register link --}}
            <p style="text-align:center;color:rgba(255,255,255,0.5);font-size:0.84rem;margin:18px 0 0;">
                ليس لديك حساب؟
                <a href="{{ route('customer.register') }}" wire:navigate
                   style="color:#fb923c;font-weight:700;text-decoration:none;">إنشاء حساب جديد</a>
            </p>

            {{-- Footer --}}
            <div style="text-align:center;margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.06);">
                <p style="color:rgba(255,255,255,0.25);font-size:0.72rem;margin:0;">
                    نظام غازي &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin  { to { transform: rotate(360deg); } }
@keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }
</style>

<script>
function togglePass() {
    const inp  = document.getElementById('pass-input');
    const icon = document.getElementById('eye-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
    } else {
        inp.type = 'password';
        icon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}
</script>
