<div style="padding:20px 16px 10px;" x-data="{
    darkMode: localStorage.getItem('theme') !== 'light',
    notifs: localStorage.getItem('notifs') !== 'off',
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    },
    toggleNotifs() {
        this.notifs = !this.notifs;
        localStorage.setItem('notifs', this.notifs ? 'on' : 'off');
    }
}">

<h1 style="color:#f8fafc;font-size:1.25rem;font-weight:800;margin:0 0 22px;">الإعدادات</h1>

{{-- ═══ Flash Messages ═══ --}}
@if($successMessage)
<div class="c-success" style="margin-bottom:18px;">✓ {{ $successMessage }}</div>
@endif
@if($errorMessage)
<div class="c-error" style="margin-bottom:18px;">{{ $errorMessage }}</div>
@endif

{{-- ═══ Section 1: Profile ═══ --}}
<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:rgba(249,115,22,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h2 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">البيانات الشخصية</h2>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">
        <div>
            <label class="c-label">الاسم الكامل *</label>
            <input wire:model="full_name" type="text" class="c-input" placeholder="اسمك الكامل">
            @error('full_name') <p class="field-err">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="c-label">البريد الإلكتروني</label>
            <input wire:model="email" type="email" class="c-input" placeholder="email@example.com" dir="ltr">
            @error('email') <p class="field-err">{{ $message }}</p> @enderror
        </div>
        <div class="grid-2">
            <div>
                <label class="c-label">نوع المركبة</label>
                <input wire:model="vehicle_type" type="text" class="c-input" placeholder="مثال: سيدان">
                @error('vehicle_type') <p class="field-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="c-label">رقم المحرك</label>
                <input wire:model="engine_number" type="text" class="c-input" placeholder="رقم المحرك" dir="ltr">
                @error('engine_number') <p class="field-err">{{ $message }}</p> @enderror
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;padding-top:2px;">
            <button wire:click="saveProfile" wire:loading.attr="disabled" class="c-btn" style="flex:1;">
                <span wire:loading.remove wire:target="saveProfile">حفظ البيانات</span>
                <span wire:loading wire:target="saveProfile">جاري الحفظ...</span>
            </button>
            <div style="background:rgba(255,255,255,0.06);border-radius:12px;padding:12px 16px;font-size:0.82rem;color:#64748b;white-space:nowrap;">
                {{ $user->phone }}
            </div>
        </div>
    </div>
</div>

{{-- ═══ Section 2: Password ═══ --}}
<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:rgba(59,130,246,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">تغيير كلمة المرور</h2>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">
        <div>
            <label class="c-label">كلمة المرور الحالية</label>
            <input wire:model="current_password" type="password" class="c-input" placeholder="••••••••">
            @error('current_password') <p class="field-err">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="c-label">كلمة المرور الجديدة</label>
            <input wire:model="new_password" type="password" class="c-input" placeholder="••••••••">
            @error('new_password') <p class="field-err">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="c-label">تأكيد كلمة المرور الجديدة</label>
            <input wire:model="new_password_confirmation" type="password" class="c-input" placeholder="••••••••">
        </div>
        <button wire:click="changePassword" wire:loading.attr="disabled" class="c-btn" style="margin-top:2px;">
            <span wire:loading.remove wire:target="changePassword">تغيير كلمة المرور</span>
            <span wire:loading wire:target="changePassword">جاري التحديث...</span>
        </button>
    </div>
</div>

{{-- ═══ Section 3: Appearance ═══ --}}
<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <div style="width:36px;height:36px;background:rgba(168,85,247,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#a855f7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </div>
        <h2 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">المظهر</h2>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="color:#cbd5e1;font-size:0.88rem;font-weight:600;margin:0;">الوضع الداكن</p>
            <p style="color:#64748b;font-size:0.76rem;margin:3px 0 0;" x-text="darkMode ? 'مُفعَّل' : 'غير مُفعَّل'"></p>
        </div>
        <button @click="toggleTheme()"
                :style="darkMode ? 'background:rgba(249,115,22,0.2);border-color:rgba(249,115,22,0.5);' : 'background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.15);'"
                style="width:52px;height:28px;border-radius:999px;border:1.5px solid;cursor:pointer;position:relative;transition:.25s;flex-shrink:0;">
            <div :style="darkMode ? 'transform:translateX(-2px);background:#f97316;' : 'transform:translateX(22px);background:#64748b;'"
                 style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;transition:.25s;"></div>
        </button>
    </div>
</div>

{{-- ═══ Section 4: Notifications ═══ --}}
<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:20px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <div style="width:36px;height:36px;background:rgba(234,179,8,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#fbbf24" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <h2 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">الإشعارات</h2>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="color:#cbd5e1;font-size:0.88rem;font-weight:600;margin:0;">إشعارات التطبيق</p>
            <p style="color:#64748b;font-size:0.76rem;margin:3px 0 0;" x-text="notifs ? 'مُفعَّلة' : 'مُوقَفة'"></p>
        </div>
        <button @click="toggleNotifs()"
                :style="notifs ? 'background:rgba(34,197,94,0.2);border-color:rgba(34,197,94,0.5);' : 'background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.15);'"
                style="width:52px;height:28px;border-radius:999px;border:1.5px solid;cursor:pointer;position:relative;transition:.25s;flex-shrink:0;">
            <div :style="notifs ? 'transform:translateX(-2px);background:#22c55e;' : 'transform:translateX(22px);background:#64748b;'"
                 style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;transition:.25s;"></div>
        </button>
    </div>
</div>

{{-- ═══ Section 5: Language ═══ --}}
<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:20px;padding:16px 20px;margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(20,184,166,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="none" stroke="#2dd4bf" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </div>
            <div>
                <p style="color:#f8fafc;font-size:0.88rem;font-weight:700;margin:0;">اللغة</p>
                <p style="color:#64748b;font-size:0.76rem;margin:3px 0 0;">Language</p>
            </div>
        </div>
        <span style="background:rgba(255,255,255,0.06);color:#94a3b8;padding:6px 14px;border-radius:10px;font-size:0.82rem;font-weight:600;">
            العربية
        </span>
    </div>
</div>

{{-- ═══ Logout ═══ --}}
<div style="padding:6px 0 20px;">
    <button wire:click="logout" wire:loading.attr="disabled"
            style="width:100%;background:rgba(239,68,68,0.1);border:1.5px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:16px;padding:14px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;transition:.2s;"
            onmouseover="this.style.background='rgba(239,68,68,0.18)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        تسجيل الخروج
    </button>
</div>

</div>
