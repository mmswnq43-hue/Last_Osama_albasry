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
                                <button wire:click="openEdit({{ $owner->id }})"
                                    style="padding:5px 12px;background:#fef9c3;color:#a16207;border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    تعديل
                                </button>
                                <button wire:click="confirmDelete({{ $owner->id }})"
                                    style="padding:5px 12px;background:#fff5f5;color:#dc2626;border:none;border-radius:7px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    حذف
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

    {{-- Create Owner Modal (2-step) --}}
    @if($showModal === 'create')
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:540px;">
            {{-- Header --}}
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

            {{-- Step Indicator --}}
            <div style="padding:16px 24px;background:#faf5ff;border-bottom:1px solid #ede9fe;display:flex;align-items:center;justify-content:center;gap:0;">
                {{-- Step 1 --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;
                        background:{{ $createStep >= 1 ? 'linear-gradient(135deg,#7c3aed,#6d28d9)' : '#e2e8f0' }};
                        color:{{ $createStep >= 1 ? 'white' : '#94a3b8' }};">1</div>
                    <span style="font-size:0.78rem;font-weight:600;color:{{ $createStep >= 1 ? '#7c3aed' : '#94a3b8' }};">بيانات المالك</span>
                </div>
                {{-- Connector --}}
                <div style="width:48px;height:2px;margin:0 8px;background:{{ $createStep >= 2 ? 'linear-gradient(to left,#7c3aed,#6d28d9)' : '#e2e8f0' }};border-radius:2px;"></div>
                {{-- Step 2 --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;
                        background:{{ $createStep >= 2 ? 'linear-gradient(135deg,#7c3aed,#6d28d9)' : '#e2e8f0' }};
                        color:{{ $createStep >= 2 ? 'white' : '#94a3b8' }};">2</div>
                    <span style="font-size:0.78rem;font-weight:600;color:{{ $createStep >= 2 ? '#7c3aed' : '#94a3b8' }};">بيانات المحطة</span>
                </div>
            </div>

            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;max-height:60vh;overflow-y:auto;">
                @if($createError)
                <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:10px 14px;color:#dc2626;font-size:0.82rem;">{{ $createError }}</div>
                @endif

                {{-- Step 1: Owner Data --}}
                @if($createStep === 1)
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
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم الهوية الوطنية</label>
                        <input wire:model="createForm.national_id" type="text" class="input-field" placeholder="10 أرقام" dir="ltr" autocomplete="off">
                        @error('createForm.national_id') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">البريد الإلكتروني</label>
                        <input wire:model="createForm.email" type="email" class="input-field" placeholder="email@example.com" dir="ltr" autocomplete="off">
                        @error('createForm.email') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">العنوان</label>
                        <textarea wire:model="createForm.address" class="input-field" placeholder="العنوان التفصيلي" rows="2" style="resize:none;"></textarea>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">كلمة المرور <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.password" type="password" class="input-field" placeholder="6 أحرف على الأقل" autocomplete="new-password">
                        @error('createForm.password') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                {{-- Step 2: Station Data --}}
                @if($createStep === 2)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">اسم المحطة <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.station_name" type="text" class="input-field" placeholder="اسم محطة الوقود" autocomplete="off">
                        @error('createForm.station_name') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">المدينة <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.city" type="text" class="input-field" placeholder="الرياض" autocomplete="off">
                        @error('createForm.city') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">الحي</label>
                        <input wire:model="createForm.district" type="text" class="input-field" placeholder="اسم الحي" autocomplete="off">
                        @error('createForm.district') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم الترخيص</label>
                        <input wire:model="createForm.license_number" type="text" class="input-field" placeholder="رقم الترخيص" dir="ltr" autocomplete="off">
                        @error('createForm.license_number') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">عدد المضخات <span style="color:#dc2626;">*</span></label>
                        <input wire:model="createForm.pumps_count" type="number" class="input-field" min="1" max="100" placeholder="1" dir="ltr">
                        @error('createForm.pumps_count') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">تاريخ الإصدار</label>
                        <input wire:model="createForm.license_issue_date" type="date" class="input-field" dir="ltr">
                        @error('createForm.license_issue_date') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">تاريخ الانتهاء</label>
                        <input wire:model="createForm.license_expiry_date" type="date" class="input-field" dir="ltr">
                        @error('createForm.license_expiry_date') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    {{-- Fuel Types --}}
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:8px;">أنواع الوقود</label>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            @foreach(['91' => 'بنزين 91', '95' => 'بنزين 95', 'diesel' => 'ديزل'] as $value => $label)
                            <label style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;border:1.5px solid {{ in_array($value, $createForm['fuel_types'] ?? []) ? '#7c3aed' : '#e2e8f0' }};background:{{ in_array($value, $createForm['fuel_types'] ?? []) ? '#ede9fe' : 'white' }};cursor:pointer;font-size:0.8rem;font-weight:600;color:{{ in_array($value, $createForm['fuel_types'] ?? []) ? '#6d28d9' : '#64748b' }};transition:all 0.15s;">
                                <input wire:model="createForm.fuel_types" type="checkbox" value="{{ $value }}" style="accent-color:#7c3aed;">
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">خط العرض (Latitude)</label>
                        <input wire:model="createForm.latitude" type="number" step="any" class="input-field" placeholder="24.7136" dir="ltr">
                        @error('createForm.latitude') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">خط الطول (Longitude)</label>
                        <input wire:model="createForm.longitude" type="number" step="any" class="input-field" placeholder="46.6753" dir="ltr">
                        @error('createForm.longitude') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">هاتف المحطة</label>
                        <input wire:model="createForm.station_phone" type="tel" class="input-field" placeholder="رقم هاتف المحطة" dir="ltr" autocomplete="off">
                        @error('createForm.station_phone') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer Buttons --}}
            <div style="padding:14px 24px;background:#faf5ff;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
                @if($createStep === 1)
                    <button wire:click="closeModal" style="padding:9px 20px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;">إلغاء</button>
                    <button wire:click="nextStep" wire:loading.attr="disabled"
                            style="padding:9px 24px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;display:flex;align-items:center;gap:8px;">
                        <span wire:loading.remove wire:target="nextStep">التالي</span>
                        <span wire:loading wire:target="nextStep">جاري التحقق...</span>
                        <svg wire:loading.remove wire:target="nextStep" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                @else
                    <button wire:click="$set('createStep', 1)" style="padding:9px 20px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        رجوع
                    </button>
                    <button wire:click="createOwner" wire:loading.attr="disabled"
                            style="padding:9px 24px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">
                        <span wire:loading.remove wire:target="createOwner">إنشاء الحساب</span>
                        <span wire:loading wire:target="createOwner">جاري الإنشاء...</span>
                    </button>
                @endif
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

    {{-- Edit Owner Modal --}}
    @if($showModal === 'edit')
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:500px;">
            <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 style="font-weight:700;color:white;font-size:1rem;">تعديل بيانات المالك</h3>
                </div>
                <button wire:click="closeModal" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;color:white;display:flex;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">الاسم الكامل <span style="color:#dc2626;">*</span></label>
                        <input wire:model="editForm.full_name" type="text" class="input-field" autocomplete="off">
                        @error('editForm.full_name') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم الجوال <span style="color:#dc2626;">*</span></label>
                        <input wire:model="editForm.phone" type="tel" class="input-field" dir="ltr" autocomplete="off">
                        @error('editForm.phone') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">البريد الإلكتروني</label>
                        <input wire:model="editForm.email" type="email" class="input-field" dir="ltr" autocomplete="off">
                        @error('editForm.email') <span style="color:#dc2626;font-size:0.75rem;margin-top:3px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">رقم الهوية / السجل التجاري</label>
                        <input wire:model="editForm.national_id" type="text" class="input-field" dir="ltr" autocomplete="off">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:5px;">العنوان</label>
                        <input wire:model="editForm.address" type="text" class="input-field" autocomplete="off">
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px;background:#fffbeb;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="closeModal" style="padding:9px 20px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;">إلغاء</button>
                <button wire:click="updateOwner" wire:loading.attr="disabled" style="padding:9px 24px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    <span wire:loading.remove wire:target="updateOwner">حفظ التعديلات</span>
                    <span wire:loading wire:target="updateOwner">جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showModal === 'delete')
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:420px;">
            <div style="padding:32px 24px;text-align:center;">
                <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="28" height="28" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:8px;">تأكيد الحذف</h3>
                <p style="color:#64748b;font-size:0.875rem;margin-bottom:24px;">هل أنت متأكد من حذف هذا المالك؟ سيتم حذف جميع بياناته ومحطاته المرتبطة.</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button wire:click="closeModal" style="padding:10px 24px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;background:white;font-family:'Tajawal',sans-serif;">إلغاء</button>
                    <button wire:click="deleteOwner" wire:loading.attr="disabled" style="padding:10px 24px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">
                        <span wire:loading.remove wire:target="deleteOwner">نعم، احذف</span>
                        <span wire:loading wire:target="deleteOwner">جاري الحذف...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
