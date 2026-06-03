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
            <h2 style="font-size:1.05rem;font-weight:800;color:#0f172a;margin:0;">إدارة العملاء</h2>
            <p style="color:#94a3b8;font-size:0.78rem;margin:4px 0 0;">جميع العملاء المسجلين في المنصة</p>
        </div>
        <button wire:click="openCreate"
                style="display:flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#f97316,#ea580c);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;box-shadow:0 4px 12px rgba(249,115,22,0.3);font-family:'Tajawal',sans-serif;white-space:nowrap;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة عميل
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
                    <tr style="background:linear-gradient(135deg,#f8faff,#eff6ff);">
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;white-space:nowrap;">العميل</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">المركبة</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">رقم المحرك</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">حالة الحساب</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">تاريخ التسجيل</th>
                        <th style="text-align:right;padding:12px 16px;color:#475569;font-weight:700;font-size:0.75rem;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="table-row" style="border-top:1px solid #f8faff;transition:background 0.15s;">
                        <td style="padding:12px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;background:linear-gradient(135deg,#f97316,#3b82f6);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                                    {{ mb_substr($user->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p style="font-weight:600;color:#1e293b;">{{ $user->full_name }}</p>
                                    <p style="color:#94a3b8;font-size:0.75rem;" dir="ltr">{{ $user->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;color:#475569;">{{ $user->vehicle_type ?? '-' }}</td>
                        <td style="padding:12px 16px;color:#475569;font-size:0.75rem;" dir="ltr">{{ $user->engine_number ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            @if($user->is_active)
                                <span class="badge badge-green">● مفعّل</span>
                            @else
                                <span class="badge badge-red">● موقوف</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:0.75rem;">{{ $user->created_at?->format('Y-m-d') }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;gap:8px;">
                                <button wire:click="openUserDetails({{ $user->id }})"
                                    style="padding:5px 12px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    تفاصيل
                                </button>
                                <button wire:click="toggleStatus({{ $user->id }})"
                                    style="padding:5px 12px;background:{{ $user->is_active ? '#fff5f5' : '#f0fdf4' }};color:{{ $user->is_active ? '#dc2626' : '#16a34a' }};border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    {{ $user->is_active ? 'إيقاف' : 'تفعيل' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:48px;text-align:center;color:#94a3b8;">لا يوجد عملاء</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">{{ $users->links() }}</div>
    </div>

    {{-- Create Customer Modal --}}
    @if($showModal === 'create')
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:500px;">
            <div style="background:linear-gradient(135deg,#f97316,#ea580c);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <h3 style="font-weight:700;color:white;font-size:1rem;">إضافة عميل جديد</h3>
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
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">كلمة المرور <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.password" type="password" class="input-field" placeholder="6 أحرف على الأقل" autocomplete="new-password">
                        @error('createForm.password') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">نوع المركبة</label>
                        <input wire:model="createForm.vehicle_type" type="text" class="input-field" placeholder="مثال: تويوتا" autocomplete="off">
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم المحرك</label>
                        <input wire:model="createForm.engine_number" type="text" class="input-field" placeholder="رقم المحرك" dir="ltr" autocomplete="off">
                    </div>
                </div>
                <div style="background:#f8faff;border-radius:10px;padding:10px 14px;font-size:0.78rem;color:#64748b;border:1px solid #e2e8f0;">
                    💡 سيتم إنشاء الحساب بحالة <strong style="color:#16a34a;">مقبول ومفعّل</strong> مباشرةً.
                </div>
            </div>
            <div style="padding:14px 24px;background:#f8faff;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="closeModal" style="padding:9px 20px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;">إلغاء</button>
                <button wire:click="createUser" wire:loading.attr="disabled" class="btn-primary" style="padding:9px 24px;">
                    <span wire:loading.remove wire:target="createUser">إنشاء الحساب</span>
                    <span wire:loading wire:target="createUser">جاري الإنشاء...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Details Modal --}}
    @if($showModal === 'details' && $selectedUser)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:500px;max-height:90vh;overflow-y:auto;">
            <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1.1rem;">
                        {{ mb_substr($selectedUser['full_name'], 0, 1) }}
                    </div>
                    <div>
                        <h3 style="font-weight:700;color:white;font-size:1rem;">{{ $selectedUser['full_name'] }}</h3>
                        <p style="color:rgba(255,255,255,0.7);font-size:0.75rem;" dir="ltr">{{ $selectedUser['phone'] }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;color:white;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:16px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:0.82rem;">
                    <div style="background:#f8faff;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">حالة الحساب</p>
                        <p style="font-weight:600;color:{{ $selectedUser['is_active'] ? '#16a34a' : '#dc2626' }};">
                            {{ $selectedUser['is_active'] ? 'مفعّل' : 'موقوف' }}
                        </p>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">حالة الموافقة</p>
                        <p style="font-weight:600;color:#1e293b;">{{ $selectedUser['approval_status'] }}</p>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">نوع المركبة</p>
                        <p style="font-weight:600;color:#1e293b;">{{ $selectedUser['vehicle_type'] ?? '-' }}</p>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">رقم المحرك</p>
                        <p style="font-weight:600;color:#1e293b;font-size:0.78rem;" dir="ltr">{{ $selectedUser['engine_number'] ?? '-' }}</p>
                    </div>
                </div>

                @if($selectedUser['subscription'])
                <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:14px;">
                    <p style="color:#15803d;font-size:0.75rem;font-weight:700;margin-bottom:8px;">✅ اشتراك نشط</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:0.8rem;">
                        <div>
                            <p style="color:#64748b;font-size:0.72rem;">نوع الباقة</p>
                            <p style="font-weight:600;color:#1e293b;">{{ $selectedUser['subscription']['plan_type'] }}</p>
                        </div>
                        <div>
                            <p style="color:#64748b;font-size:0.72rem;">تاريخ الانتهاء</p>
                            <p style="font-weight:600;color:#1e293b;" dir="ltr">{{ $selectedUser['subscription']['end_date'] }}</p>
                        </div>
                    </div>
                </div>
                @else
                <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:12px;padding:14px;text-align:center;color:#94a3b8;font-size:0.82rem;">
                    لا يوجد اشتراك نشط
                </div>
                @endif

                @if($selectedUser['rejection_reason'])
                <div style="background:#fff5f5;border:1px solid #fca5a5;border-radius:10px;padding:12px;">
                    <p style="color:#dc2626;font-size:0.72rem;font-weight:700;margin-bottom:4px;">سبب الرفض</p>
                    <p style="color:#7f1d1d;font-size:0.82rem;">{{ $selectedUser['rejection_reason'] }}</p>
                </div>
                @endif

                <p style="font-size:0.75rem;color:#94a3b8;">تاريخ التسجيل: {{ $selectedUser['created_at'] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
