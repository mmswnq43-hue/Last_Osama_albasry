<div style="padding:20px 16px 10px;">

{{-- ═══ Header ═══ --}}
<div style="margin-bottom:20px;">
    <h1 style="color:#f8fafc;font-size:1.25rem;font-weight:800;margin:0 0 4px;">سجل النشاطات</h1>
    <p style="color:#64748b;font-size:0.82rem;margin:0;">تاريخ جميع عملياتك</p>
</div>

{{-- ═══ Filter Tabs ═══ --}}
<div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;margin-bottom:20px;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
    @php
        $filters = [
            ''            => 'الكل',
            'refuel'      => 'تعبئة',
            'subscription'=> 'اشتراك',
            'car_wash'    => 'غسيل',
            'maintenance' => 'صيانة',
            'login'       => 'دخول',
        ];
    @endphp
    @foreach($filters as $val => $label)
    <button wire:click="$set('filterType', '{{ $val }}')"
            style="flex-shrink:0;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;transition:.2s;border:1.5px solid;white-space:nowrap;
            {{ $filterType === $val ? 'background:rgba(249,115,22,0.18);border-color:rgba(249,115,22,0.5);color:#fb923c;' : 'background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#94a3b8;' }}">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ═══ Log List ═══ --}}
@if($logs->isEmpty())
    <div style="text-align:center;padding:60px 20px;">
        <div style="width:72px;height:72px;border-radius:24px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="32" height="32" fill="none" stroke="#475569" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p style="color:#64748b;font-size:0.95rem;margin:0;">لا توجد سجلات</p>
        @if($filterType)
        <p style="color:#475569;font-size:0.82rem;margin:6px 0 0;">جرب تغيير فلتر النوع</p>
        @endif
    </div>
@else
    {{-- Date-grouped list --}}
    @php $prevDate = null; @endphp
    <div style="display:flex;flex-direction:column;gap:0;">
        @foreach($logs as $log)
        @php
            $logDate = $log->created_at?->format('Y-m-d');
            $colorMap = ['green'=>'#22c55e','blue'=>'#3b82f6','orange'=>'#f97316','red'=>'#ef4444'];
            $bgMap    = ['green'=>'rgba(34,197,94,0.15)','blue'=>'rgba(59,130,246,0.15)','orange'=>'rgba(249,115,22,0.15)','red'=>'rgba(239,68,68,0.15)'];
            $typeIcons= ['refuel'=>['icon'=>'⛽','color'=>'orange'],'subscription'=>['icon'=>'📋','color'=>'blue'],'car_wash'=>['icon'=>'🚗','color'=>'green'],'maintenance'=>['icon'=>'🔧','color'=>'blue'],'login'=>['icon'=>'🔑','color'=>'green'],'setting_change'=>['icon'=>'⚙️','color'=>'orange']];
            $ti       = $typeIcons[$log->action_type] ?? null;
            $ic       = $log->icon ?? ($ti['icon'] ?? '📌');
            $cl       = $log->color ?? ($ti['color'] ?? null);
            $c        = $colorMap[$cl] ?? '#94a3b8';
            $bg       = $bgMap[$cl] ?? 'rgba(148,163,184,0.1)';
        @endphp

        @if($logDate !== $prevDate)
        @php $prevDate = $logDate; @endphp
        <div style="padding:16px 0 8px;display:flex;align-items:center;gap:10px;">
            <span style="color:#475569;font-size:0.74rem;font-weight:700;white-space:nowrap;">
                @if($log->created_at?->isToday()) اليوم
                @elseif($log->created_at?->isYesterday()) أمس
                @else {{ $log->created_at?->format('d M Y') }}
                @endif
            </span>
            <div style="flex:1;height:1px;background:rgba(255,255,255,0.06);"></div>
        </div>
        @endif

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:14px;padding:14px;display:flex;align-items:flex-start;gap:12px;margin-bottom:8px;">
            <div style="width:42px;height:42px;border-radius:13px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0;">
                {{ $ic }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                    <p style="color:#f8fafc;font-weight:700;font-size:0.88rem;margin:0 0 3px;line-height:1.3;">{{ $log->title }}</p>
                    <span style="color:#475569;font-size:0.68rem;flex-shrink:0;">{{ $log->created_at?->format('H:i') }}</span>
                </div>
                @if($log->description)
                    <p style="color:#64748b;font-size:0.78rem;margin:0;line-height:1.5;">{{ $log->description }}</p>
                @endif
                @if($log->meta)
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;">
                    @foreach((array)$log->meta as $mk => $mv)
                    @if($mv)
                    <span style="font-size:0.68rem;background:rgba(255,255,255,0.06);color:#94a3b8;padding:2px 8px;border-radius:6px;">
                        {{ $mk }}: {{ $mv }}
                    </span>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;gap:8px;">
        @if($logs->onFirstPage())
            <span style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#475569;padding:8px 16px;border-radius:10px;font-size:0.82rem;">السابق</span>
        @else
            <button wire:click="previousPage" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">السابق</button>
        @endif
        <span style="background:rgba(249,115,22,0.15);border:1px solid rgba(249,115,22,0.3);color:#fb923c;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:700;">
            {{ $logs->currentPage() }} / {{ $logs->lastPage() }}
        </span>
        @if($logs->hasMorePages())
            <button wire:click="nextPage" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">التالي</button>
        @else
            <span style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#475569;padding:8px 16px;border-radius:10px;font-size:0.82rem;">التالي</span>
        @endif
    </div>
    @endif
@endif

</div>
