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
            <input wire:model.live="search" type="text" placeholder="بحث باسم المحطة أو الكود..." class="input-field" style="padding-right:38px;">
        </div>
        <button wire:click="openCreate" class="btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline; margin-left:6px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة محطة
        </button>
    </div>

    {{-- Note --}}
    <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:10px 14px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="font-size:0.82rem; color:#9a3412; font-weight:500;">التفعيل/الإيقاف الكلي يتم من الإدارة فقط. يمكنك التحكم في حالة فتح/إغلاق المحطة.</span>
    </div>

    {{-- Stations Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px;">
        @forelse($stations as $station)
        <div class="stat-card" style="position:relative;">
            {{-- Status badges --}}
            <div style="display:flex; gap:6px; margin-bottom:12px; flex-wrap:wrap;">
                <span class="badge {{ $station->is_active ? 'badge-green' : 'badge-slate' }}">
                    {{ $station->is_active ? 'مفعّلة' : 'غير مفعّلة' }}
                </span>
                <span class="badge {{ $station->is_open ? 'badge-blue' : 'badge-red' }}">
                    {{ $station->is_open ? 'مفتوحة' : 'مغلقة' }}
                </span>
            </div>

            <h3 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 6px;">{{ $station->station_name }}</h3>
            <p style="font-size:0.8rem; color:#64748b; margin:0 0 4px;">{{ $station->location }}</p>
            <p style="font-size:0.75rem; color:#94a3b8; margin:0 0 12px;">كود: {{ $station->station_code }}</p>
            @if($station->phone)
            <p style="font-size:0.78rem; color:#94a3b8; margin:0 0 12px;">📞 {{ $station->phone }}</p>
            @endif

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button wire:click="openEdit({{ $station->id }})" style="flex:1; padding:8px 12px; background:#f0f4ff; color:#3b82f6; border:none; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif;">
                    تعديل
                </button>
                <button wire:click="toggleOpen({{ $station->id }})"
                        style="flex:1; padding:8px 12px; background:{{ $station->is_open ? '#fee2e2' : '#f0fdf4' }}; color:{{ $station->is_open ? '#dc2626' : '#15803d' }}; border:none; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif;">
                    {{ $station->is_open ? 'إغلاق' : 'فتح' }}
                </button>
                <button wire:click="delete({{ $station->id }})" wire:confirm="هل أنت متأكد من حذف هذه المحطة؟"
                        style="padding:8px 12px; background:#fee2e2; color:#dc2626; border:none; border-radius:8px; font-size:0.8rem; cursor:pointer;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; text-align:center; padding:48px; color:#94a3b8;">
            <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" style="margin:0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <p style="font-size:0.95rem; font-weight:600;">لا توجد محطات مضافة بعد</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px;">{{ $stations->links() }}</div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay" x-data>
        <div class="modal-box">
            <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
                <h2 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0;">{{ $editingId ? 'تعديل المحطة' : 'إضافة محطة جديدة' }}</h2>
                <button wire:click="$set('showModal', false)" style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:24px;">
                <div style="display:grid; gap:14px;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">اسم المحطة *</label>
                        <input wire:model="station_name" type="text" class="input-field" placeholder="اسم المحطة">
                        @error('station_name') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">الموقع *</label>
                        <input wire:model="location" type="text" class="input-field" placeholder="العنوان التفصيلي">
                        @error('location') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">خط العرض</label>
                            <input wire:model="latitude" type="number" step="any" class="input-field" placeholder="24.7136">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">خط الطول</label>
                            <input wire:model="longitude" type="number" step="any" class="input-field" placeholder="46.6753">
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">كود المحطة *</label>
                            <input wire:model="station_code" type="text" class="input-field" placeholder="STN-001">
                            @error('station_code') <p style="color:#dc2626; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">رقم الهاتف</label>
                            <input wire:model="phone" type="text" class="input-field" placeholder="0512345678">
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; background:#f8faff; padding:12px 14px; border-radius:10px;">
                        <input wire:model="is_open" type="checkbox" id="is_open_check" style="width:16px; height:16px; accent-color:#f97316;">
                        <label for="is_open_check" style="font-size:0.875rem; color:#374151; font-weight:500; cursor:pointer;">المحطة مفتوحة الآن</label>
                    </div>
                </div>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary" style="flex:1;">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'حفظ التعديلات' : 'إضافة المحطة' }}</span>
                        <span wire:loading wire:target="save">جاري الحفظ...</span>
                    </button>
                    <button wire:click="$set('showModal', false)" style="flex:1; padding:10px; background:#f1f5f9; color:#64748b; border:none; border-radius:10px; font-size:0.875rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif;">إلغاء</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
