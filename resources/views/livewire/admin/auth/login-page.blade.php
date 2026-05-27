<div style="width:100%;max-width:420px;margin:0 auto;padding:0 16px;">
    <div style="background:rgba(255,255,255,0.04);backdrop-filter:blur(20px);border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow:0 24px 64px rgba(0,0,0,0.4);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#1e3a8a,#f97316);padding:36px 32px;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-60px;left:-30px;width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
            <div style="width:70px;height:70px;background:rgba(255,255,255,0.2);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                <svg width="36" height="36" fill="white" viewBox="0 0 24 24">
                    <path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z"/>
                </svg>
            </div>
            <h1 style="color:white;font-size:1.6rem;font-weight:800;letter-spacing:-0.5px;">غـازي</h1>
            <p style="color:rgba(255,255,255,0.7);font-size:0.82rem;margin-top:4px;">لوحة إدارة النظام</p>
        </div>

        {{-- Form --}}
        <div style="padding:28px 32px;">
            @if($error)
            <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:12px 14px;font-size:0.82rem;margin-bottom:18px;">
                ⚠️ {{ $error }}
            </div>
            @endif

            <form wire:submit="login" style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;color:rgba(255,255,255,0.7);font-size:0.82rem;font-weight:600;margin-bottom:7px;">البريد الإلكتروني</label>
                    <input type="email" wire:model="email" placeholder="أدخل البريد الإلكتروني" dir="ltr"
                        style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:11px;padding:11px 14px;font-size:0.875rem;font-family:'Tajawal',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)'">
                    @error('email') <span style="color:#fca5a5;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;">
                        <label style="color:rgba(255,255,255,0.7);font-size:0.82rem;font-weight:600;">كلمة المرور</label>
                        <a href="{{ route('admin.password.forgot') }}" wire:navigate
                           style="color:#f97316;font-size:0.78rem;text-decoration:none;opacity:0.85;"
                           onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                    <input type="password" wire:model="password" placeholder="أدخل كلمة المرور"
                        style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.1);color:white;border-radius:11px;padding:11px 14px;font-size:0.875rem;font-family:'Tajawal',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.08)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.06)'">
                    @error('password') <span style="color:#fca5a5;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled"
                    style="background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;font-weight:700;border-radius:11px;padding:13px;font-size:0.9rem;border:none;cursor:pointer;margin-top:4px;font-family:'Tajawal',sans-serif;box-shadow:0 6px 20px rgba(249,115,22,0.4);transition:opacity 0.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <span wire:loading.remove>تسجيل الدخول</span>
                    <span wire:loading>⏳ جاري الدخول...</span>
                </button>
            </form>
        </div>
    </div>
</div>
