<div style="max-width:580px;">
    @if($successMessage)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    <div style="background:white;border-radius:18px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;padding-bottom:18px;border-bottom:2px solid #f8faff;">
            <div style="width:46px;height:46px;background:linear-gradient(135deg,#f97316,#3b82f6);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(249,115,22,0.3);">
                <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            </div>
            <div>
                <h2 style="font-weight:800;color:#0f172a;font-size:1rem;">إعدادات بطاقة الأولوية</h2>
                <p style="color:#64748b;font-size:0.78rem;margin-top:2px;">قيم قابلة للتعديل تنعكس فوراً على النظام</p>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px;">
            {{-- Threshold --}}
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:700;color:#374151;margin-bottom:8px;">
                    حد اللترات لتفعيل بطاقة الأولوية
                    <span style="font-weight:400;color:#94a3b8;">(لتر)</span>
                </label>
                <div style="position:relative;">
                    <input wire:model="priorityCardLitersThreshold" type="number" min="1" max="10000" class="input-field" style="width:100%;box-sizing:border-box;padding-left:52px;">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:0.78rem;font-weight:700;color:#3b82f6;">L</span>
                </div>
                @error('priorityCardLitersThreshold') <span style="color:#dc2626;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                <p style="color:#94a3b8;font-size:0.75rem;margin-top:6px;">عند تجاوز هذا الرقم شهرياً يحصل المستخدم على بطاقة الأولوية تلقائياً.</p>
            </div>

            {{-- Validity Days --}}
            <div>
                <label style="display:block;font-size:0.85rem;font-weight:700;color:#374151;margin-bottom:8px;">
                    مدة صلاحية بطاقة الأولوية
                    <span style="font-weight:400;color:#94a3b8;">(يوم)</span>
                </label>
                <div style="position:relative;">
                    <input wire:model="priorityCardValidityDays" type="number" min="1" max="365" class="input-field" style="width:100%;box-sizing:border-box;padding-left:60px;">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:0.72rem;font-weight:700;color:#f97316;">يوم</span>
                </div>
                @error('priorityCardValidityDays') <span style="color:#dc2626;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                <p style="color:#94a3b8;font-size:0.75rem;margin-top:6px;">عدد الأيام التي تبقى فيها البطاقة صالحة بعد إصدارها.</p>
            </div>

            {{-- Preview Box --}}
            <div style="background:linear-gradient(135deg,#eff6ff,#fff7ed);border:1.5px solid #fed7aa;border-radius:14px;padding:16px;">
                <p style="font-size:0.78rem;font-weight:700;color:#78350f;margin-bottom:10px;">معاينة الإعدادات الحالية</p>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div style="flex:1;background:white;border-radius:10px;padding:12px;text-align:center;min-width:120px;">
                        <p style="font-size:1.6rem;font-weight:800;color:#1d4ed8;">{{ $priorityCardLitersThreshold }}</p>
                        <p style="font-size:0.72rem;color:#64748b;margin-top:2px;">لتر للبطاقة</p>
                    </div>
                    <div style="flex:1;background:white;border-radius:10px;padding:12px;text-align:center;min-width:120px;">
                        <p style="font-size:1.6rem;font-weight:800;color:#ea580c;">{{ $priorityCardValidityDays }}</p>
                        <p style="font-size:0.72rem;color:#64748b;margin-top:2px;">يوم صلاحية</p>
                    </div>
                </div>
            </div>

            <button wire:click="save" class="btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;" wire:loading.attr="disabled">
                <svg wire:loading.remove width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span wire:loading.remove style="font-size:0.9rem;">حفظ الإعدادات</span>
                <span wire:loading style="font-size:0.9rem;">⏳ جاري الحفظ...</span>
            </button>
        </div>
    </div>
</div>
