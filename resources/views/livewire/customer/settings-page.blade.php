<div style="padding:20px 16px 10px;" x-data="{
    openSheet: null,
    theme: localStorage.getItem('theme') || 'dark',
    notifs: localStorage.getItem('notifs') !== 'off',
    lang: localStorage.getItem('lang') || 'ar',
    setTheme(val) {
        this.theme = val;
        localStorage.setItem('theme', val);
    },
    setNotifs(val) {
        this.notifs = val;
        localStorage.setItem('notifs', val ? 'on' : 'off');
    },
    setLang(val) {
        this.lang = val;
        localStorage.setItem('lang', val);
    },
    closeSheet() { this.openSheet = null; }
}">

{{-- ══════════════════════════════════════
     Header: avatar + name
════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px;">
    <div style="width:56px;height:56px;border-radius:18px;background:linear-gradient(135deg,rgba(249,115,22,0.25),rgba(234,88,12,0.15));border:2px solid rgba(249,115,22,0.4);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="26" height="26" fill="none" stroke="#f97316" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
    </div>
    <div>
        <p style="color:#f8fafc;font-size:1.05rem;font-weight:800;margin:0;">{{ $user->full_name }}</p>
        <p style="color:#94a3b8;font-size:0.82rem;margin:4px 0 0;" dir="ltr">{{ $user->phone }}</p>
    </div>
</div>

{{-- Flash messages --}}
@if($successMessage)
<div class="c-success" style="margin-bottom:18px;">✓ {{ $successMessage }}</div>
@endif
@if($errorMessage)
<div class="c-error" style="margin-bottom:18px;">{{ $errorMessage }}</div>
@endif

{{-- ══════════════════════════════════════
     Settings List
════════════════════════════════════════ --}}

{{-- ─── Group: الحساب ─── --}}
<p style="color:#64748b;font-size:0.72rem;font-weight:700;letter-spacing:.08em;margin:0 4px 10px;text-transform:uppercase;">الحساب</p>

<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:18px;overflow:hidden;margin-bottom:22px;">

    {{-- تعديل الملف الشخصي --}}
    <button @click="openSheet='profile'"
            style="width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:16px 18px;font-family:'Tajawal',sans-serif;border-bottom:1px solid rgba(255,255,255,0.06);transition:.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='none'">
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(249,115,22,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span style="color:#f8fafc;font-size:0.92rem;font-weight:700;">تعديل الملف الشخصي</span>
        </div>
        <svg width="16" height="16" fill="none" stroke="#475569" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    {{-- تغيير كلمة المرور --}}
    <button @click="openSheet='password'"
            style="width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:16px 18px;font-family:'Tajawal',sans-serif;transition:.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='none'">
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span style="color:#f8fafc;font-size:0.92rem;font-weight:700;">تغيير كلمة المرور</span>
        </div>
        <svg width="16" height="16" fill="none" stroke="#475569" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
</div>

{{-- ─── Group: التفضيلات ─── --}}
<p style="color:#64748b;font-size:0.72rem;font-weight:700;letter-spacing:.08em;margin:0 4px 10px;text-transform:uppercase;">التفضيلات</p>

<div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:18px;overflow:hidden;margin-bottom:22px;">

    {{-- المظهر --}}
    <button @click="openSheet='theme'"
            style="width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:16px 18px;font-family:'Tajawal',sans-serif;border-bottom:1px solid rgba(255,255,255,0.06);transition:.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='none'">
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(168,85,247,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#a855f7" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </div>
            <span style="color:#f8fafc;font-size:0.92rem;font-weight:700;">المظهر</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:0.76rem;font-weight:600;padding:3px 10px;border-radius:8px;"
                  :style="theme==='dark' ? 'background:rgba(168,85,247,0.2);color:#c4b5fd;' : 'background:rgba(250,204,21,0.15);color:#fde047;'"
                  x-text="theme==='dark' ? 'داكن' : 'فاتح'"></span>
            <svg width="16" height="16" fill="none" stroke="#475569" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>
    </button>

    {{-- الإشعارات --}}
    <button @click="openSheet='notifs'"
            style="width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:16px 18px;font-family:'Tajawal',sans-serif;border-bottom:1px solid rgba(255,255,255,0.06);transition:.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='none'">
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(234,179,8,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#fbbf24" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <span style="color:#f8fafc;font-size:0.92rem;font-weight:700;">الإشعارات</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:0.76rem;font-weight:600;padding:3px 10px;border-radius:8px;"
                  :style="notifs ? 'background:rgba(34,197,94,0.2);color:#86efac;' : 'background:rgba(100,116,139,0.2);color:#94a3b8;'"
                  x-text="notifs ? 'مُفعَّلة' : 'مُوقَفة'"></span>
            <svg width="16" height="16" fill="none" stroke="#475569" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>
    </button>

    {{-- اللغة --}}
    <button @click="openSheet='lang'"
            style="width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:16px 18px;font-family:'Tajawal',sans-serif;transition:.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='none'">
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(20,184,166,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="17" height="17" fill="none" stroke="#2dd4bf" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </div>
            <span style="color:#f8fafc;font-size:0.92rem;font-weight:700;">اللغة</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:0.76rem;font-weight:600;padding:3px 10px;border-radius:8px;background:rgba(45,212,191,0.15);color:#5eead4;"
                  x-text="lang==='ar' ? 'العربية' : 'English'"></span>
            <svg width="16" height="16" fill="none" stroke="#475569" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>
    </button>
</div>

{{-- ─── تسجيل الخروج ─── --}}
<button wire:click="logout" wire:loading.attr="disabled"
        style="width:100%;background:rgba(239,68,68,0.08);border:1.5px solid rgba(239,68,68,0.25);color:#fca5a5;border-radius:16px;padding:14px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;display:flex;align-items:center;justify-content:center;gap:10px;transition:.2s;margin-bottom:8px;"
        onmouseover="this.style.background='rgba(239,68,68,0.16)'" onmouseout="this.style.background='rgba(239,68,68,0.08)'">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
    </svg>
    تسجيل الخروج
</button>

{{-- ══════════════════════════════════════
     SHEETS (modals from bottom)
════════════════════════════════════════ --}}

{{-- Backdrop --}}
<div x-show="openSheet !== null"
     x-transition:enter="transition duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="closeSheet()"
     x-cloak
     style="position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);z-index:50;"></div>

{{-- ── Sheet: تعديل الملف الشخصي ── --}}
<div x-show="openSheet==='profile'"
     x-transition:enter="transition duration-300"
     x-transition:enter-start="transform translate-y-full"
     x-transition:enter-end="transform translate-y-0"
     x-transition:leave="transition duration-200"
     x-transition:leave-start="transform translate-y-0"
     x-transition:leave-end="transform translate-y-full"
     x-cloak
     style="position:fixed;bottom:0;left:0;right:0;z-index:51;background:#0f172a;border-radius:24px 24px 0 0;border-top:1px solid rgba(255,255,255,0.1);padding:0 0 env(safe-area-inset-bottom,16px);max-height:88vh;overflow-y:auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.07);position:sticky;top:0;background:#0f172a;z-index:1;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#f97316;"></div>
            <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">تعديل الملف الشخصي</h3>
        </div>
        <button @click="closeSheet()" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:1rem;line-height:1;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <div style="padding:20px;">
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="c-label">الاسم الكامل *</label>
                <input wire:model="full_name" type="text" class="c-input" placeholder="اسمك الكامل" autocomplete="name">
                @error('full_name') <p class="field-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="c-label">رقم الجوال</label>
                <div class="c-input" style="color:#64748b;cursor:not-allowed;opacity:.7;" dir="ltr">{{ $user->phone }}</div>
            </div>
            <div>
                <label class="c-label">البريد الإلكتروني</label>
                <input wire:model="email" type="email" class="c-input" placeholder="email@example.com" dir="ltr" autocomplete="email">
                @error('email') <p class="field-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="c-label">نوع المركبة</label>
                <input wire:model="vehicle_type" type="text" class="c-input" placeholder="مثال: تويوتا كامري" autocomplete="off">
            </div>
            <div>
                <label class="c-label">رقم المحرك</label>
                <input wire:model="engine_number" type="text" class="c-input" placeholder="رقم المحرك" dir="ltr" autocomplete="off">
            </div>
            <button wire:click="saveProfile" wire:loading.attr="disabled" @click="$wire.saveProfile().then(()=>{ if(!$wire.errorMessage) closeSheet(); })" class="c-btn" style="margin-top:4px;">
                <span wire:loading.remove wire:target="saveProfile">حفظ التعديلات</span>
                <span wire:loading wire:target="saveProfile">جاري الحفظ...</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Sheet: تغيير كلمة المرور ── --}}
<div x-show="openSheet==='password'"
     x-transition:enter="transition duration-300"
     x-transition:enter-start="transform translate-y-full"
     x-transition:enter-end="transform translate-y-0"
     x-transition:leave="transition duration-200"
     x-transition:leave-start="transform translate-y-0"
     x-transition:leave-end="transform translate-y-full"
     x-cloak
     style="position:fixed;bottom:0;left:0;right:0;z-index:51;background:#0f172a;border-radius:24px 24px 0 0;border-top:1px solid rgba(255,255,255,0.1);padding:0 0 env(safe-area-inset-bottom,16px);max-height:80vh;overflow-y:auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.07);position:sticky;top:0;background:#0f172a;z-index:1;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#60a5fa;"></div>
            <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">تغيير كلمة المرور</h3>
        </div>
        <button @click="closeSheet()" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <div style="padding:20px;">
        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <label class="c-label">كلمة المرور الحالية</label>
                <input wire:model="current_password" type="password" class="c-input" placeholder="••••••••" autocomplete="current-password">
                @error('current_password') <p class="field-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="c-label">كلمة المرور الجديدة</label>
                <input wire:model="new_password" type="password" class="c-input" placeholder="••••••••" autocomplete="new-password">
                @error('new_password') <p class="field-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="c-label">تأكيد كلمة المرور الجديدة</label>
                <input wire:model="new_password_confirmation" type="password" class="c-input" placeholder="••••••••" autocomplete="new-password">
            </div>
            <button wire:click="changePassword" wire:loading.attr="disabled" @click="$wire.changePassword().then(()=>{ if(!$wire.errorMessage) closeSheet(); })" class="c-btn" style="background:linear-gradient(135deg,#3b82f6,#2563eb);box-shadow:0 10px 24px rgba(59,130,246,0.3);margin-top:4px;">
                <span wire:loading.remove wire:target="changePassword">تغيير كلمة المرور</span>
                <span wire:loading wire:target="changePassword">جاري التحديث...</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Sheet: المظهر ── --}}
<div x-show="openSheet==='theme'"
     x-transition:enter="transition duration-300"
     x-transition:enter-start="transform translate-y-full"
     x-transition:enter-end="transform translate-y-0"
     x-transition:leave="transition duration-200"
     x-transition:leave-start="transform translate-y-0"
     x-transition:leave-end="transform translate-y-full"
     x-cloak
     style="position:fixed;bottom:0;left:0;right:0;z-index:51;background:#0f172a;border-radius:24px 24px 0 0;border-top:1px solid rgba(255,255,255,0.1);padding:0 0 env(safe-area-inset-bottom,16px);">

    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.07);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#a855f7;"></div>
            <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">اختر المظهر</h3>
        </div>
        <button @click="closeSheet()" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <div style="padding:20px;display:flex;gap:12px;">

        {{-- داكن --}}
        <button @click="setTheme('dark'); closeSheet()"
                :style="theme==='dark' ? 'border-color:#a855f7;background:rgba(168,85,247,0.15);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="flex:1;border:2px solid;border-radius:18px;padding:20px 14px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;position:relative;">
            <div style="text-align:center;">
                <div style="font-size:2rem;margin-bottom:8px;">🌙</div>
                <p style="color:#f8fafc;font-size:0.88rem;font-weight:700;margin:0 0 4px;">داكن</p>
                <p style="color:#64748b;font-size:0.72rem;margin:0;">Dark Mode</p>
            </div>
            <div x-show="theme==='dark'"
                 style="position:absolute;top:10px;left:10px;width:20px;height:20px;border-radius:50%;background:#a855f7;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:800;">✓</div>
        </button>

        {{-- فاتح --}}
        <button @click="setTheme('light'); closeSheet()"
                :style="theme==='light' ? 'border-color:#fbbf24;background:rgba(251,191,36,0.12);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="flex:1;border:2px solid;border-radius:18px;padding:20px 14px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;position:relative;">
            <div style="text-align:center;">
                <div style="font-size:2rem;margin-bottom:8px;">☀️</div>
                <p style="color:#f8fafc;font-size:0.88rem;font-weight:700;margin:0 0 4px;">فاتح</p>
                <p style="color:#64748b;font-size:0.72rem;margin:0;">Light Mode</p>
            </div>
            <div x-show="theme==='light'"
                 style="position:absolute;top:10px;left:10px;width:20px;height:20px;border-radius:50%;background:#fbbf24;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:800;">✓</div>
        </button>
    </div>
</div>

{{-- ── Sheet: الإشعارات ── --}}
<div x-show="openSheet==='notifs'"
     x-transition:enter="transition duration-300"
     x-transition:enter-start="transform translate-y-full"
     x-transition:enter-end="transform translate-y-0"
     x-transition:leave="transition duration-200"
     x-transition:leave-start="transform translate-y-0"
     x-transition:leave-end="transform translate-y-full"
     x-cloak
     style="position:fixed;bottom:0;left:0;right:0;z-index:51;background:#0f172a;border-radius:24px 24px 0 0;border-top:1px solid rgba(255,255,255,0.1);padding:0 0 env(safe-area-inset-bottom,16px);">

    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.07);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#fbbf24;"></div>
            <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">إعدادات الإشعارات</h3>
        </div>
        <button @click="closeSheet()" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <div style="padding:20px;display:flex;flex-direction:column;gap:12px;">

        {{-- تفعيل --}}
        <button @click="setNotifs(true); closeSheet()"
                :style="notifs ? 'border-color:#22c55e;background:rgba(34,197,94,0.15);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="width:100%;border:2px solid;border-radius:16px;padding:16px 18px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;display:flex;align-items:center;justify-content:space-between;position:relative;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:12px;background:rgba(34,197,94,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#22c55e" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div style="text-align:right;">
                    <p style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin:0;">تفعيل الإشعارات</p>
                    <p style="color:#64748b;font-size:0.76rem;margin:3px 0 0;">استقبال جميع إشعارات النظام</p>
                </div>
            </div>
            <div x-show="notifs" style="width:22px;height:22px;border-radius:50%;background:#22c55e;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:800;flex-shrink:0;">✓</div>
        </button>

        {{-- إيقاف --}}
        <button @click="setNotifs(false); closeSheet()"
                :style="!notifs ? 'border-color:#ef4444;background:rgba(239,68,68,0.12);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="width:100%;border:2px solid;border-radius:16px;padding:16px 18px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:12px;background:rgba(100,116,139,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9M3 3l18 18"/>
                    </svg>
                </div>
                <div style="text-align:right;">
                    <p style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin:0;">إيقاف الإشعارات</p>
                    <p style="color:#64748b;font-size:0.76rem;margin:3px 0 0;">عدم استقبال أي إشعارات</p>
                </div>
            </div>
            <div x-show="!notifs" style="width:22px;height:22px;border-radius:50%;background:#ef4444;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:800;flex-shrink:0;">✓</div>
        </button>
    </div>
</div>

{{-- ── Sheet: اللغة ── --}}
<div x-show="openSheet==='lang'"
     x-transition:enter="transition duration-300"
     x-transition:enter-start="transform translate-y-full"
     x-transition:enter-end="transform translate-y-0"
     x-transition:leave="transition duration-200"
     x-transition:leave-start="transform translate-y-0"
     x-transition:leave-end="transform translate-y-full"
     x-cloak
     style="position:fixed;bottom:0;left:0;right:0;z-index:51;background:#0f172a;border-radius:24px 24px 0 0;border-top:1px solid rgba(255,255,255,0.1);padding:0 0 env(safe-area-inset-bottom,16px);">

    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.07);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#2dd4bf;"></div>
            <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">اختر اللغة</h3>
        </div>
        <button @click="closeSheet()" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <div style="padding:20px;display:flex;gap:12px;">

        {{-- عربي --}}
        <button @click="setLang('ar'); closeSheet()"
                :style="lang==='ar' ? 'border-color:#2dd4bf;background:rgba(45,212,191,0.12);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="flex:1;border:2px solid;border-radius:18px;padding:20px 14px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;position:relative;">
            <div style="text-align:center;">
                <div style="font-size:2rem;margin-bottom:8px;">🇾🇪</div>
                <p style="color:#f8fafc;font-size:0.92rem;font-weight:700;margin:0 0 3px;">العربية</p>
                <p style="color:#64748b;font-size:0.72rem;margin:0;">Arabic</p>
            </div>
            <div x-show="lang==='ar'"
                 style="position:absolute;top:10px;left:10px;width:20px;height:20px;border-radius:50%;background:#2dd4bf;display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:0.7rem;font-weight:800;">✓</div>
        </button>

        {{-- إنجليزي --}}
        <button @click="setLang('en'); closeSheet()"
                :style="lang==='en' ? 'border-color:#60a5fa;background:rgba(96,165,250,0.12);' : 'border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);'"
                style="flex:1;border:2px solid;border-radius:18px;padding:20px 14px;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;position:relative;">
            <div style="text-align:center;">
                <div style="font-size:2rem;margin-bottom:8px;">🇬🇧</div>
                <p style="color:#f8fafc;font-size:0.92rem;font-weight:700;margin:0 0 3px;">English</p>
                <p style="color:#64748b;font-size:0.72rem;margin:0;">الإنجليزية</p>
            </div>
            <div x-show="lang==='en'"
                 style="position:absolute;top:10px;left:10px;width:20px;height:20px;border-radius:50%;background:#60a5fa;display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:0.7rem;font-weight:800;">✓</div>
        </button>
    </div>
</div>

</div>
