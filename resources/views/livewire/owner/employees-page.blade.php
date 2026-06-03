<div>
    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
        <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d; font-weight:600; font-size:0.875rem;">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; gap:12px; flex-wrap:wrap;">
        <div style="position:relative; flex:1; min-width:200px; max-width:340px;">
            <svg width="16" height="16" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="position:absolute; right:12px; top:50%; transform:translateY(-50%);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live="search" type="text" placeholder="بحث بالاسم أو الجوال..." class="input-field" style="padding-right:38px;">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline; margin-left:6px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة موظف
        </button>
    </div>

    {{-- Table --}}
    <div class="glass-card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:700px;">
                <thead>
                    <tr style="background:#f8faff;">
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">اسم الموظف</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الجوال</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">المحطة</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">المنصب</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الراتب</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">تاريخ التعيين</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الحالة</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr class="table-row" style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px;">
                            <p style="font-size:0.875rem; font-weight:600; color:#0f172a; margin:0;">{{ $emp->user?->full_name ?? '-' }}</p>
                            <p style="font-size:0.72rem; color:#94a3b8; margin:0;">{{ $emp->employee_code }}</p>
                        </td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#475569;">{{ $emp->user?->phone ?? '-' }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#475569;">{{ $emp->station?->station_name ?? '-' }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#0f172a; font-weight:500;">{{ $emp->position }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#0f172a;">{{ $emp->salary ? number_format($emp->salary, 0) . ' ر.س' : '-' }}</td>
                        <td style="padding:12px 16px; font-size:0.78rem; color:#64748b;">{{ \Carbon\Carbon::parse($emp->hire_date)->format('Y/m/d') }}</td>
                        <td style="padding:12px 16px;">
                            <span class="badge {{ $emp->is_active ? 'badge-green' : 'badge-red' }}">{{ $emp->is_active ? 'نشط' : 'موقوف' }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex; gap:6px;">
                                <button wire:click="toggleActive({{ $emp->id }})"
                                        style="padding:5px 10px; background:{{ $emp->is_active ? '#fff7ed' : '#f0fdf4' }}; color:{{ $emp->is_active ? '#ea580c' : '#15803d' }}; border:none; border-radius:7px; font-size:0.75rem; cursor:pointer; font-family:'Tajawal',sans-serif; font-weight:600;">
                                    {{ $emp->is_active ? 'إيقاف' : 'تفعيل' }}
                                </button>
                                <button wire:click="fire({{ $emp->id }})" wire:confirm="هل تريد إنهاء خدمة هذا الموظف؟"
                                        style="padding:5px 10px; background:#fee2e2; color:#dc2626; border:none; border-radius:7px; font-size:0.75rem; cursor:pointer; font-family:'Tajawal',sans-serif; font-weight:600;">
                                    إنهاء
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="padding:36px; text-align:center; color:#94a3b8;">لا يوجد موظفون حتى الآن</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:14px 16px;">{{ $employees->links() }}</div>
    </div>

    {{-- Add Employee Modal --}}
    @if($showModal)
    <div class="modal-overlay">
        <div class="modal-box">
            <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
                <h2 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0;">إضافة موظف جديد</h2>
                <button wire:click="$set('showModal', false)" style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:24px; display:grid; gap:14px;">
                {{-- Phone search --}}
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">رقم جوال المستخدم *</label>
                    <div style="display:flex; gap:8px;">
                        <input wire:model="user_phone" type="text" class="input-field" placeholder="05XXXXXXXX" style="flex:1;">
                        <button wire:click="searchUser" class="btn-blue" style="padding:9px 16px; white-space:nowrap;">بحث</button>
                    </div>
                    @error('user_phone') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                @if($foundUserName)
                <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:10px; padding:10px 14px;">
                    <p style="color:#15803d; font-weight:600; font-size:0.875rem; margin:0;">تم العثور على: {{ $foundUserName }}</p>
                </div>
                @endif

                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">المحطة *</label>
                    <select wire:model="station_id" class="input-field">
                        <option value="">-- اختر المحطة --</option>
                        @foreach($myStations as $st)
                        <option value="{{ $st->id }}">{{ $st->station_name }}</option>
                        @endforeach
                    </select>
                    @error('station_id') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">المنصب *</label>
                        <input wire:model="position" type="text" class="input-field" placeholder="محاسب / عامل تعبئة...">
                        @error('position') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">الراتب (ريال)</label>
                        <input wire:model="salary" type="number" step="any" class="input-field" placeholder="3000">
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">تاريخ التعيين *</label>
                    <input wire:model="hire_date" type="date" class="input-field">
                    @error('hire_date') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                @error('foundUserId') <p style="color:#dc2626; font-size:0.78rem; background:#fee2e2; padding:8px 12px; border-radius:8px;">يجب البحث عن مستخدم أولاً</p> @enderror

                <div style="display:flex; gap:10px; margin-top:6px;">
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary" style="flex:1;">
                        <span wire:loading.remove wire:target="save">إضافة الموظف</span>
                        <span wire:loading wire:target="save">جاري الحفظ...</span>
                    </button>
                    <button wire:click="$set('showModal', false)" style="flex:1; padding:10px; background:#f1f5f9; color:#64748b; border:none; border-radius:10px; font-size:0.875rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif;">إلغاء</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
