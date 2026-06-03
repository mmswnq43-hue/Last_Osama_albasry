<div>
    @if($successMessage)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="admin-page-header">
        <div>
            <h2 style="font-size:1.05rem;font-weight:800;color:#0f172a;margin:0;">إدارة الملاك</h2>
            <p style="color:#94a3b8;font-size:0.78rem;margin:4px 0 0;">ملاك المحطات المسجلين في المنصة</p>
        </div>
        <button wire:click="openCreate"
                style="display:flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,0.3);font-family:'Tajawal',sans-serif;white-space:nowrap;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة مالك
        </button>
    </div>

    {{-- Filters --}}
    <div class="admin-filter-bar">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="🔍 بحث بالاسم أو رقم الهاتف..." class="input-field" style="flex:1;min-width:200px;">
        <select wire:model.live="statusFilter" class="input-field">
            <option value="">كل الحالات</option>
            <option value="active">مفعّل</option>
            <option value="inactive">موقوف</option>
        </select>
    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#faf5ff,#ede9fe);">
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;white-space:nowrap;">المالك</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">المحطات</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">حالة الحساب</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">تاريخ التسجيل</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($owners as $owner)
                    <tr class="table-row" style="border-top:1px solid #f8faff;transition:background 0.15s;">
                        <td style="padding:12px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                                    {{ mb_substr($owner->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p style="font-weight:600;color:#1e293b;">{{ $owner->full_name }}</p>
                                    <p style="color:#94a3b8;font-size:0.75rem;" dir="ltr">{{ $owner->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;">
                            <span style="display:inline-flex;align-items:center;gap:4px;background:#ede9fe;color:#6d28d9;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:700;">
                                🏪 {{ $owner->stations_count }} محطة
                            </span>
                        </td>
                        <td style="padding:12px 16px;">
                            @if($owner->is_active)
                                <span class="badge badge-green">● مفعّل</span>
                            @else
                                <span class="badge badge-red">● موقوف</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:0.75rem;">{{ $owner->created_at?->format('Y-m-d') }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;gap:8px;">
                                <button wire:click="openOwnerDetails({{ $owner->id }})"
                                    style="padding:5px 12px;background:#ede9fe;color:#6d28d9;border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    تفاصيل
                                </button>
                                <button wire:click="toggleStatus({{ $owner->id }})"
                                    style="padding:5px 12px;background:{{ $owner->is_active ? '#fff5f5' : '#f0fdf4' }};color:{{ $owner->is_active ? '#dc2626' : '#16a34a' }};border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    {{ $owner->is_active ? 'إيقاف' : 'تفعيل' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="padding:48px;text-align:center;color:#94a3b8;">لا يوجد ملاك مسجلون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">{{ $owners->links() }}</div>
    </div>

    {{-- Create Owner Modal --}}
    @if($showModal === 'create')
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:480px;">
            <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 style="font-weight:700;color:white;font-size:1rem;">إضافة مالك محطة</h3>
                </div>
                <button wire:click="closeModal" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;color:white;display:flex;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                @if($createError)
                <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:10px 14px;color:#dc2626;font-size:0.82rem;">{{ $createError }}</div>
                @endif
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">الاسم الكامل <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.full_name" type="text" class="input-field" placeholder="الاسم الثلاثي" autocomplete="off">
                        @error('createForm.full_name') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم الجوال <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.phone" type="tel" class="input-field" placeholder="7XXXXXXXX" dir="ltr" autocomplete="off">
                        @error('createForm.phone') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">البريد الإلكتروني</label>
                        <input wire:model="createForm.email" type="email" class="input-field" placeholder="email@example.com" dir="ltr" autocomplete="off">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">كلمة المرور <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.password" type="password" class="input-field" placeholder="6 أحرف على الأقل" autocomplete="new-password">
                        @error('createForm.password') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div style="background:#faf5ff;border-radius:10px;padding:10px 14px;font-size:0.78rem;color:#64748b;border:1px solid #ede9fe;">
                    💡 سيتم إنشاء الحساب بدور <strong style="color:#7c3aed;">مالك محطة</strong> بحالة مقبول ومفعّل مباشرةً.
                </div>
            </div>
            <div style="padding:14px 24px;background:#faf5ff;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="closeModal" style="padding:9px 20px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;">إلغاء</button>
                <button wire:click="createOwner" wire:loading.attr="disabled"
                        style="padding:9px 24px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    <span wire:loading.remove wire:target="createOwner">إنشاء الحساب</span>
                    <span wire:loading wire:target="createOwner">جاري الإنشاء...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Details Modal --}}
    @if($showModal === 'details' && $selectedOwner)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:480px;">
            <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1.1rem;">
                        {{ mb_substr($selectedOwner['full_name'], 0, 1) }}
                    </div>
                    <div>
                        <h3 style="font-weight:700;color:white;font-size:1rem;">{{ $selectedOwner['full_name'] }}</h3>
                        <p style="color:rgba(255,255,255,0.7);font-size:0.75rem;" dir="ltr">{{ $selectedOwner['phone'] }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;color:white;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:0.82rem;">
                    <div style="background:#faf5ff;border-radius:10px;padding:12px;text-align:center;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:6px;">عدد المحطات</p>
                        <p style="font-weight:800;color:#7c3aed;font-size:1.4rem;">{{ $selectedOwner['stations_count'] }}</p>
                        <p style="color:#94a3b8;font-size:0.7rem;">محطة</p>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:12px;text-align:center;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:6px;">حالة الحساب</p>
                        <p style="font-weight:700;font-size:0.9rem;color:{{ $selectedOwner['is_active'] ? '#16a34a' : '#dc2626' }};">
                            {{ $selectedOwner['is_active'] ? '✅ مفعّل' : '❌ موقوف' }}
                        </p>
                    </div>
                </div>
                @if($selectedOwner['email'])
                <div style="background:#f8faff;border-radius:10px;padding:12px;">
                    <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">البريد الإلكتروني</p>
                    <p style="font-weight:600;color:#1e293b;font-size:0.82rem;" dir="ltr">{{ $selectedOwner['email'] }}</p>
                </div>
                @endif
                <p style="font-size:0.75rem;color:#94a3b8;">تاريخ التسجيل: {{ $selectedOwner['created_at'] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
