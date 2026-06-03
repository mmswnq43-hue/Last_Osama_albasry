<div style="padding:20px 16px 10px;">

{{-- ═══ Greeting ═══ --}}
<div style="margin-bottom:20px;">
    <p style="color:#94a3b8;font-size:0.82rem;margin:0 0 2px;">مرحباً بعودتك 👋</p>
    <h1 style="color:#f8fafc;font-size:1.35rem;font-weight:800;margin:0;">{{ $user->full_name }}</h1>
</div>

{{-- ═══ Ads Carousel ═══ --}}
@if($ads->isNotEmpty())
<div x-data="{
        current: 0, total: {{ $ads->count() }}, timer: null,
        start() { this.timer = setInterval(() => this.next(), 5000); },
        stop()  { clearInterval(this.timer); },
        next()  { this.current = (this.current + 1) % this.total; },
        prev()  { this.current = (this.current - 1 + this.total) % this.total; },
    }"
    x-init="start()" @mouseenter="stop()" @mouseleave="start()"
    style="position:relative;border-radius:18px;overflow:hidden;margin-bottom:22px;box-shadow:0 8px 32px rgba(0,0,0,0.3);">

    @foreach($ads as $i => $ad)
    <div x-show="current === {{ $i }}"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:{{ $i === 0 ? 'relative' : 'absolute' }};top:0;left:0;width:100%;"
         @if($i !== 0) x-cloak @endif>
        @if($ad->link_url)<a href="{{ $ad->link_url }}" target="_blank" rel="noopener" style="display:block;text-decoration:none;">@endif
        <div style="min-height:160px;background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#f97316 100%);position:relative;overflow:hidden;display:flex;align-items:flex-end;">
            @if($ad->image_url)
                <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 60%);"></div>
            @else
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(30,58,138,0.9),rgba(249,115,22,0.8));"></div>
            @endif
            <div style="position:relative;padding:18px 20px;width:100%;">
                <h3 style="color:white;font-size:1rem;font-weight:800;margin:0 0 3px;text-shadow:0 2px 8px rgba(0,0,0,0.3);">{{ $ad->title }}</h3>
                @if($ad->description)
                    <p style="color:rgba(255,255,255,0.85);font-size:0.78rem;margin:0;line-height:1.5;">{{ $ad->description }}</p>
                @endif
            </div>
        </div>
        @if($ad->link_url)</a>@endif
    </div>
    @endforeach

    @if($ads->count() > 1)
    <div style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);display:flex;gap:5px;z-index:5;">
        @foreach($ads as $i => $_)
        <button @click="current = {{ $i }}"
                :style="current === {{ $i }} ? 'background:white;width:18px;' : 'background:rgba(255,255,255,0.4);width:7px;'"
                style="height:7px;border-radius:999px;border:none;cursor:pointer;transition:.3s;padding:0;"></button>
        @endforeach
    </div>
    @endif
</div>
@endif

{{-- ═══ Active Subscription Card ═══ --}}
@if($activeSub)
@php
    $planLabels = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
    $planLabel  = $planLabels[$activeSub->plan_type] ?? $activeSub->plan_type;
    $daysLeft   = $activeSub->end_date ? max(0, now()->diffInDays($activeSub->end_date, false)) : null;
    $totalDays  = ($activeSub->start_date && $activeSub->end_date)
                    ? $activeSub->start_date->diffInDays($activeSub->end_date)
                    : 30;
    $progress   = $daysLeft !== null && $totalDays > 0
                    ? min(100, round(($daysLeft / $totalDays) * 100))
                    : 0;
@endphp
<div style="background:linear-gradient(135deg,rgba(34,197,94,0.14),rgba(20,184,166,0.08));border:1.5px solid rgba(34,197,94,0.4);border-radius:20px;padding:20px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
        <div>
            <p style="color:#86efac;font-size:0.78rem;font-weight:700;margin:0 0 3px;text-transform:uppercase;letter-spacing:.05em;">الاشتراك النشط</p>
            <h2 style="color:#f8fafc;font-size:1.15rem;font-weight:800;margin:0;">باقة {{ $planLabel }}</h2>
        </div>
        <span style="background:rgba(34,197,94,0.2);color:#86efac;border:1px solid rgba(34,197,94,0.4);padding:4px 12px;border-radius:999px;font-size:0.76rem;font-weight:700;">
            {{ $activeSub->status === 'active' ? '● نشط' : '◷ مجدوَل' }}
        </span>
    </div>

    {{-- Progress Bar --}}
    @if($daysLeft !== null)
    <div style="margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="color:#94a3b8;font-size:0.74rem;">المتبقي من الاشتراك</span>
            <span style="color:#86efac;font-size:0.74rem;font-weight:700;">{{ $daysLeft }} يوم</span>
        </div>
        <div style="height:6px;background:rgba(255,255,255,0.08);border-radius:999px;overflow:hidden;">
            <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(90deg,#22c55e,#14b8a6);border-radius:999px;transition:.3s;"></div>
        </div>
    </div>
    @endif

    <div style="display:flex;gap:16px;flex-wrap:wrap;">
        @if($activeSub->remaining_car_washes > 0)
        <div style="background:rgba(255,255,255,0.06);border-radius:12px;padding:10px 14px;flex:1;min-width:80px;text-align:center;">
            <p style="color:#64748b;font-size:0.7rem;margin:0 0 3px;">غسيل سيارة</p>
            <p style="color:#86efac;font-size:1.1rem;font-weight:800;margin:0;">{{ $activeSub->remaining_car_washes }}</p>
        </div>
        @endif
        @if($activeSub->remaining_maintenance > 0)
        <div style="background:rgba(255,255,255,0.06);border-radius:12px;padding:10px 14px;flex:1;min-width:80px;text-align:center;">
            <p style="color:#64748b;font-size:0.7rem;margin:0 0 3px;">صيانة</p>
            <p style="color:#c4b5fd;font-size:1.1rem;font-weight:800;margin:0;">{{ $activeSub->remaining_maintenance }}</p>
        </div>
        @endif
        @if($activeSub->end_date)
        <div style="background:rgba(255,255,255,0.06);border-radius:12px;padding:10px 14px;flex:1;min-width:80px;text-align:center;">
            <p style="color:#64748b;font-size:0.7rem;margin:0 0 3px;">تاريخ الانتهاء</p>
            <p style="color:#f8fafc;font-size:0.82rem;font-weight:700;margin:0;" dir="ltr">{{ $activeSub->end_date->format('Y-m-d') }}</p>
        </div>
        @endif
    </div>
</div>
@else
<div style="background:rgba(249,115,22,0.08);border:1.5px dashed rgba(249,115,22,0.3);border-radius:20px;padding:20px;margin-bottom:20px;text-align:center;">
    <svg width="36" height="36" fill="none" stroke="#f97316" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;opacity:0.7;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p style="color:#fb923c;font-weight:700;font-size:0.92rem;margin:0 0 4px;">لا يوجد اشتراك نشط</p>
    <p style="color:#64748b;font-size:0.8rem;margin:0 0 14px;">اشترك الآن للاستفادة من خدمات غازي</p>
    <a href="{{ route('customer.subscriptions') }}"
       style="display:inline-block;background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:10px 24px;border-radius:12px;font-weight:700;font-size:0.86rem;text-decoration:none;box-shadow:0 6px 18px rgba(249,115,22,0.3);">
        استعرض الباقات
    </a>
</div>
@endif

{{-- ═══ Stats Grid ═══ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:22px;">

    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;text-align:center;">
        <div style="width:38px;height:38px;background:rgba(249,115,22,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
            <svg width="20" height="20" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <p style="color:#f97316;font-size:1.4rem;font-weight:800;margin:0 0 3px;">{{ $totalRefuels }}</p>
        <p style="color:#64748b;font-size:0.72rem;margin:0;">إجمالي التعبئات</p>
    </div>

    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;text-align:center;">
        <div style="width:38px;height:38px;background:rgba(59,130,246,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
            <svg width="20" height="20" fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </div>
        <p style="color:#60a5fa;font-size:1.4rem;font-weight:800;margin:0 0 3px;">{{ number_format($totalLiters, 0) }}</p>
        <p style="color:#64748b;font-size:0.72rem;margin:0;">إجمالي اللترات</p>
    </div>

    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;text-align:center;">
        <div style="width:38px;height:38px;background:rgba(34,197,94,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
            <svg width="20" height="20" fill="none" stroke="#22c55e" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p style="color:#22c55e;font-size:1.4rem;font-weight:800;margin:0 0 3px;">{{ $thisMonthRefuels }}</p>
        <p style="color:#64748b;font-size:0.72rem;margin:0;">تعبئات هذا الشهر</p>
    </div>

    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;text-align:center;">
        <div style="width:38px;height:38px;background:rgba(168,85,247,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
            <svg width="20" height="20" fill="none" stroke="#a855f7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <p style="color:#a855f7;font-size:1.4rem;font-weight:800;margin:0 0 3px;">{{ number_format($thisMonthLiters, 0) }}</p>
        <p style="color:#64748b;font-size:0.72rem;margin:0;">لترات هذا الشهر</p>
    </div>

</div>

{{-- ═══ Recent Activity ═══ --}}
<div style="margin-bottom:10px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="color:#f8fafc;font-size:1rem;font-weight:800;margin:0;">آخر النشاطات</h3>
        <a href="{{ route('customer.history') }}"
           style="color:#f97316;font-size:0.8rem;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">
            السجل الكامل
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    </div>

    @if($recentLogs->isEmpty())
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:28px;text-align:center;">
        <svg width="36" height="36" fill="none" stroke="#475569" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p style="color:#64748b;font-size:0.88rem;margin:0;">لا توجد نشاطات بعد</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($recentLogs as $log)
        @php
            $colorMap = ['green'=>'#22c55e','blue'=>'#3b82f6','orange'=>'#f97316','red'=>'#ef4444'];
            $bgMap    = ['green'=>'rgba(34,197,94,0.15)','blue'=>'rgba(59,130,246,0.15)','orange'=>'rgba(249,115,22,0.15)','red'=>'rgba(239,68,68,0.15)'];
            $c        = $colorMap[$log->color] ?? '#94a3b8';
            $bg       = $bgMap[$log->color] ?? 'rgba(148,163,184,0.1)';
        @endphp
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:14px;padding:14px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:12px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                {{ $log->icon ?? '📋' }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="color:#f8fafc;font-weight:700;font-size:0.88rem;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->title }}</p>
                @if($log->description)
                    <p style="color:#64748b;font-size:0.76rem;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->description }}</p>
                @endif
            </div>
            <span style="color:#475569;font-size:0.7rem;flex-shrink:0;">{{ $log->created_at?->diffForHumans() }}</span>
        </div>
        @endforeach
    </div>
    @endif
</div>

</div>
