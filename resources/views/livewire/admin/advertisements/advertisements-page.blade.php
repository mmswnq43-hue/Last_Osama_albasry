<div>

    {{-- ── Success toast ── --}}
    @if($successMessage)
    <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3500)" x-show="show"
         style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:70;background:#16a34a;color:white;padding:11px 24px;border-radius:12px;box-shadow:0 8px 24px rgba(22,163,74,0.4);font-weight:600;font-size:0.88rem;display:flex;align-items:center;gap:8px;white-space:nowrap;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ $successMessage }}
    </div>
    @endif

    {{-- ── Page header ── --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:0;">إدارة الإعلانات</h2>
            <p style="color:#64748b;font-size:0.85rem;margin:4px 0 0;">الإعلانات التي تظهر كـ banner في صفحة العملاء</p>
        </div>
        <a href="{{ route('admin.ads.create') }}"
           style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:10px 20px;border-radius:10px;font-weight:600;font-size:0.875rem;text-decoration:none;box-shadow:0 4px 12px rgba(249,115,22,0.3);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            إضافة إعلان جديد
        </a>
    </div>

    {{-- ── Empty state ── --}}
    @if($ads->isEmpty())
    <div class="glass-card" style="padding:60px 20px;text-align:center;">
        <div style="font-size:3rem;margin-bottom:12px;">📢</div>
        <p style="color:#475569;font-weight:600;font-size:1rem;margin:0 0 6px;">لا توجد إعلانات بعد</p>
        <p style="color:#94a3b8;font-size:0.85rem;margin:0;">أضف أول إعلان ليظهر في صفحة العملاء</p>
    </div>
    @else

    {{-- ── Ads grid ── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,340px),1fr));gap:18px;">
        @foreach($ads as $ad)
        <div class="glass-card" style="padding:0;overflow:hidden;{{ !$ad->is_active ? 'opacity:0.65;' : '' }}">

            {{-- Image --}}
            <div style="height:160px;background:linear-gradient(135deg,#1e3a8a,#1d4ed8);position:relative;overflow:hidden;">
                @if($ad->image_url)
                    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}"
                         style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.4);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                {{-- Status badge --}}
                <div style="position:absolute;top:10px;right:10px;">
                    @if($ad->is_active)
                        <span style="background:rgba(34,197,94,0.9);color:white;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;">● نشط</span>
                    @else
                        <span style="background:rgba(100,116,139,0.9);color:white;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;">متوقف</span>
                    @endif
                </div>

                {{-- Sort order badge --}}
                <div style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.5);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;">
                    {{ $ad->sort_order }}
                </div>
            </div>

            {{-- Content --}}
            <div style="padding:14px 16px;">
                <h3 style="color:#0f172a;font-weight:700;font-size:0.95rem;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ad->title }}</h3>
                @if($ad->description)
                    <p style="color:#64748b;font-size:0.78rem;line-height:1.5;margin:0 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $ad->description }}</p>
                @endif

                {{-- Dates --}}
                @if($ad->start_date || $ad->end_date)
                <p style="color:#94a3b8;font-size:0.73rem;margin:0 0 10px;">
                    📅
                    {{ $ad->start_date?->format('Y-m-d') ?? '∞' }}
                    →
                    {{ $ad->end_date?->format('Y-m-d') ?? '∞' }}
                </p>
                @endif

                {{-- Actions --}}
                <div style="display:flex;gap:8px;padding-top:10px;border-top:1px solid #f1f5f9;flex-wrap:wrap;">
                    <a href="{{ route('admin.ads.edit', $ad->id) }}"
                       style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:8px;padding:7px 10px;font-size:0.78rem;font-weight:600;text-decoration:none;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </a>
                    <button wire:click="toggleActive({{ $ad->id }})"
                            style="flex:1;background:#fffbeb;color:#a16207;border:none;border-radius:8px;padding:7px 10px;font-size:0.78rem;font-weight:600;cursor:pointer;">
                        {{ $ad->is_active ? 'إيقاف' : 'تفعيل' }}
                    </button>
                    <button wire:click="confirmDelete({{ $ad->id }})"
                            style="background:#fef2f2;color:#dc2626;border:none;border-radius:8px;padding:7px 10px;font-size:0.78rem;font-weight:600;cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Delete confirm ── --}}
    @if($deleteConfirmId)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:360px;padding:28px;text-align:center;">
            <div style="font-size:2.2rem;margin-bottom:12px;">🗑️</div>
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 8px;">حذف الإعلان</h3>
            <p style="color:#64748b;font-size:0.85rem;margin:0 0 22px;">هل أنت متأكد؟ سيتم حذف الإعلان والصورة نهائياً.</p>
            <div style="display:flex;gap:10px;">
                <button wire:click="deleteAd"
                        style="flex:1;background:#dc2626;color:white;border:none;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    نعم، احذف
                </button>
                <button wire:click="$set('deleteConfirmId', null)"
                        style="flex:1;border:1.5px solid #e2e8f0;background:white;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;color:#475569;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
