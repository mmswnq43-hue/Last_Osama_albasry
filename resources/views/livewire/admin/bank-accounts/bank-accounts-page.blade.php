<div>
    {{-- Success toast --}}
    @if($successMessage)
    <div wire:key="toast" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
         style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:60;background:#16a34a;color:white;padding:12px 24px;border-radius:12px;box-shadow:0 8px 24px rgba(22,163,74,0.4);font-weight:600;font-size:0.88rem;display:flex;align-items:center;gap:8px;">
        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        {{ $successMessage }}
    </div>
    @endif

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:0;">الحسابات البنكية</h2>
            <p style="color:#64748b;font-size:0.85rem;margin:4px 0 0;">الحسابات التي ستظهر للعملاء عند الاشتراك لإتمام الدفع</p>
        </div>
        <button wire:click="openCreate" class="btn-primary" style="display:flex;align-items:center;gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            إضافة حساب بنكي
        </button>
    </div>

    {{-- Cards grid --}}
    @if($accounts->isEmpty())
        <div class="glass-card" style="padding:60px 20px;text-align:center;">
            <div style="width:72px;height:72px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11M8 14v3m4-3v3m4-3v3"/></svg>
            </div>
            <p style="color:#475569;font-weight:600;margin:0 0 4px;">لا توجد حسابات بنكية</p>
            <p style="color:#94a3b8;font-size:0.85rem;margin:0;">أضف أول حساب بنكي ليتمكن العملاء من الدفع</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,320px),1fr));gap:16px;">
            @foreach($accounts as $acc)
            <div wire:key="acc-{{ $acc->id }}" class="glass-card" style="padding:0;overflow:hidden;{{ $acc->is_active ? '' : 'opacity:0.6;' }}">
                {{-- Card header --}}
                <div style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);padding:18px 20px;position:relative;overflow:hidden;">
                    <div style="position:absolute;top:-30px;left:-20px;width:100px;height:100px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;position:relative;">
                        <div>
                            <p style="color:rgba(255,255,255,0.7);font-size:0.7rem;margin:0 0 2px;">البنك</p>
                            <p style="color:white;font-weight:800;font-size:1.05rem;margin:0;">{{ $acc->bank_name }}</p>
                        </div>
                        <svg width="32" height="32" fill="rgba(255,255,255,0.85)" viewBox="0 0 24 24"><path d="M11.5 1L2 6v2h19V6m-5 4v7h3v-7M2 22h19v-3H2m8-9v7h3v-7m-9 0v7h3v-7z"/></svg>
                    </div>
                </div>
                {{-- Card body --}}
                <div style="padding:16px 20px;">
                    <div style="margin-bottom:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;margin:0 0 2px;">صاحب الحساب</p>
                        <p style="color:#0f172a;font-weight:600;font-size:0.9rem;margin:0;">{{ $acc->account_name }}</p>
                    </div>
                    <div style="margin-bottom:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;margin:0 0 2px;">رقم الحساب</p>
                        <p style="color:#0f172a;font-weight:700;font-size:0.95rem;margin:0;letter-spacing:0.5px;direction:ltr;text-align:right;">{{ $acc->account_number }}</p>
                    </div>
                    @if($acc->iban)
                    <div style="margin-bottom:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;margin:0 0 2px;">الآيبان (IBAN)</p>
                        <p style="color:#475569;font-weight:600;font-size:0.82rem;margin:0;direction:ltr;text-align:right;">{{ $acc->iban }}</p>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                        <span class="badge badge-blue">{{ $acc->currency }}</span>
                        @if($acc->is_active)
                            <span class="badge badge-green">نشط</span>
                        @else
                            <span class="badge badge-slate">معطّل</span>
                        @endif
                    </div>
                    @if($acc->notes)
                    <p style="color:#64748b;font-size:0.78rem;background:#f8faff;padding:8px 10px;border-radius:8px;margin:0 0 14px;">{{ $acc->notes }}</p>
                    @endif

                    {{-- Actions --}}
                    <div style="display:flex;gap:8px;border-top:1px solid #f1f5f9;padding-top:12px;">
                        <button wire:click="openEdit({{ $acc->id }})" style="flex:1;background:#eff6ff;color:#1d4ed8;border:none;border-radius:8px;padding:8px;font-size:0.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            تعديل
                        </button>
                        <button wire:click="toggleActive({{ $acc->id }})" style="flex:1;background:#fffbeb;color:#a16207;border:none;border-radius:8px;padding:8px;font-size:0.8rem;font-weight:600;cursor:pointer;">
                            {{ $acc->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                        <button wire:click="confirmDelete({{ $acc->id }})" style="background:#fef2f2;color:#dc2626;border:none;border-radius:8px;padding:8px 12px;font-size:0.8rem;font-weight:600;cursor:pointer;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ===== Create/Edit Modal ===== --}}
    @if($showModal)
    <div class="modal-overlay" wire:key="modal">
        <div class="modal-box" style="max-width:480px;">
            <div style="background:linear-gradient(135deg,#1e3a8a,#f97316);padding:20px 24px;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="color:white;font-weight:800;font-size:1.1rem;margin:0;">{{ $editingId ? 'تعديل الحساب البنكي' : 'إضافة حساب بنكي' }}</h3>
                <button wire:click="closeModal" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;width:30px;height:30px;color:white;cursor:pointer;font-size:1.1rem;">✕</button>
            </div>
            <form wire:submit="save" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">اسم البنك *</label>
                    <input type="text" wire:model="bank_name" class="input-field" style="width:100%;" placeholder="مثال: بنك التضامن">
                    @error('bank_name') <span style="color:#dc2626;font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">اسم صاحب الحساب *</label>
                    <input type="text" wire:model="account_name" class="input-field" style="width:100%;" placeholder="الاسم كما هو في البنك">
                    @error('account_name') <span style="color:#dc2626;font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">رقم الحساب *</label>
                    <input type="text" wire:model="account_number" class="input-field" style="width:100%;direction:ltr;text-align:right;" placeholder="0000000000">
                    @error('account_number') <span style="color:#dc2626;font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div style="display:flex;gap:12px;">
                    <div style="flex:1;">
                        <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">الآيبان (اختياري)</label>
                        <input type="text" wire:model="iban" class="input-field" style="width:100%;direction:ltr;text-align:right;" placeholder="IBAN">
                    </div>
                    <div style="width:110px;">
                        <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">العملة</label>
                        <select wire:model="currency" class="input-field" style="width:100%;">
                            <option value="YER">ريال يمني</option>
                            <option value="SAR">ريال سعودي</option>
                            <option value="USD">دولار</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#334155;margin-bottom:6px;">ملاحظات (اختياري)</label>
                    <input type="text" wire:model="notes" class="input-field" style="width:100%;" placeholder="أي تعليمات إضافية للعميل">
                </div>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" wire:model="is_active" style="width:18px;height:18px;accent-color:#f97316;cursor:pointer;">
                    <span style="font-size:0.85rem;color:#334155;">الحساب نشط (يظهر للعملاء)</span>
                </label>

                <div style="display:flex;gap:10px;margin-top:6px;">
                    <button type="submit" class="btn-primary" style="flex:1;">{{ $editingId ? 'حفظ التعديلات' : 'إضافة الحساب' }}</button>
                    <button type="button" wire:click="closeModal" style="padding:10px 20px;border:1.5px solid #e2e8f0;background:white;border-radius:10px;font-weight:600;font-size:0.875rem;color:#475569;cursor:pointer;">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ===== Delete Confirm Modal ===== --}}
    @if($confirmDeleteId)
    <div class="modal-overlay" wire:key="delete-modal">
        <div class="modal-box" style="max-width:380px;padding:28px;text-align:center;">
            <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="30" height="30" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0 0 8px;">تأكيد الحذف</h3>
            <p style="color:#64748b;font-size:0.85rem;margin:0 0 22px;">هل أنت متأكد من حذف هذا الحساب البنكي؟ لا يمكن التراجع.</p>
            <div style="display:flex;gap:10px;">
                <button wire:click="deleteAccount" style="flex:1;background:#dc2626;color:white;border:none;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;cursor:pointer;">نعم، احذف</button>
                <button wire:click="$set('confirmDeleteId', null)" style="flex:1;border:1.5px solid #e2e8f0;background:white;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;color:#475569;cursor:pointer;">إلغاء</button>
            </div>
        </div>
    </div>
    @endif
</div>
