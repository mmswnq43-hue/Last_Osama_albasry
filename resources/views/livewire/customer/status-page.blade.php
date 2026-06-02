<div>

{{-- ═══════════════ Ads Banner Carousel ═══════════════ --}}
@php $activeAds = \App\Models\Advertisement::active()->get(); @endphp
@if($activeAds->isNotEmpty())
<div x-data="{
        current: 0,
        total: {{ $activeAds->count() }},
        timer: null,
        start() { this.timer = setInterval(() => this.next(), 5000); },
        stop()  { clearInterval(this.timer); },
        next()  { this.current = (this.current + 1) % this.total; },
        prev()  { this.current = (this.current - 1 + this.total) % this.total; },
    }"
    x-init="start()"
    @mouseenter="stop()" @mouseleave="start()"
    style="position:relative;border-radius:18px;overflow:hidden;margin-bottom:22px;box-shadow:0 8px 32px rgba(0,0,0,0.3);">

    {{-- Slides --}}
    @foreach($activeAds as $i => $ad)
    <div x-show="current === {{ $i }}"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position:{{ $i === 0 ? 'relative' : 'absolute' }};top:0;left:0;width:100%;display:block;"
         @if($i !== 0) x-cloak @endif>

        @if($ad->link_url)
        <a href="{{ $ad->link_url }}" target="_blank" rel="noopener" style="display:block;text-decoration:none;">
        @endif

        <div style="min-height:180px;background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#f97316 100%);position:relative;overflow:hidden;display:flex;align-items:flex-end;">
            @if($ad->image_url)
                <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}"
                     style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);"></div>
            @else
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(30,58,138,0.9),rgba(249,115,22,0.8));"></div>
            @endif

            {{-- Text overlay --}}
            <div style="position:relative;padding:20px 22px;width:100%;">
                <h3 style="color:white;font-size:1.05rem;font-weight:800;margin:0 0 4px;text-shadow:0 2px 8px rgba(0,0,0,0.3);">{{ $ad->title }}</h3>
                @if($ad->description)
                    <p style="color:rgba(255,255,255,0.85);font-size:0.8rem;margin:0;line-height:1.5;text-shadow:0 1px 4px rgba(0,0,0,0.3);">{{ $ad->description }}</p>
                @endif
                @if($ad->link_url)
                    <span style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;background:rgba(249,115,22,0.9);color:white;padding:4px 12px;border-radius:999px;font-size:0.74rem;font-weight:700;">
                        اعرف أكثر ←
                    </span>
                @endif
            </div>
        </div>

        @if($ad->link_url) </a> @endif
    </div>
    @endforeach

    {{-- Prev / Next arrows --}}
    @if($activeAds->count() > 1)
    <button @click="prev()"
            style="position:absolute;top:50%;right:12px;transform:translateY(-50%);background:rgba(0,0,0,0.4);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;z-index:5;"
            onmouseover="this.style.background='rgba(0,0,0,0.7)'" onmouseout="this.style.background='rgba(0,0,0,0.4)'">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <button @click="next()"
            style="position:absolute;top:50%;left:12px;transform:translateY(-50%);background:rgba(0,0,0,0.4);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;z-index:5;"
            onmouseover="this.style.background='rgba(0,0,0,0.7)'" onmouseout="this.style.background='rgba(0,0,0,0.4)'">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>

    {{-- Dots --}}
    <div style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:5;">
        @foreach($activeAds as $i => $_)
        <button @click="current = {{ $i }}"
                :style="current === {{ $i }} ? 'background:white;width:20px;' : 'background:rgba(255,255,255,0.45);width:8px;'"
                style="height:8px;border-radius:999px;border:none;cursor:pointer;transition:.3s;padding:0;"></button>
        @endforeach
    </div>
    @endif
</div>
@endif

{{-- ═══════════════ Header ═══════════════ --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="color:#f8fafc;font-size:1.25rem;font-weight:800;margin:0;">
            مرحباً، {{ $user->full_name }}
        </h2>
        <p style="color:#94a3b8;font-size:0.84rem;margin:4px 0 0;" dir="ltr">{{ $user->phone }}</p>
    </div>
    <button wire:click="openPlanModal" class="c-btn" style="width:auto;padding:11px 22px;display:flex;align-items:center;gap:8px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        الباقات
    </button>
</div>

{{-- ═══════════════ Account status banner (if no active sub) ═══════════════ --}}
@if($user->approval_status === 'pending')
    <div style="background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);border-radius:14px;padding:16px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:1.6rem;">⏳</span>
        <div>
            <p style="color:#fde047;font-weight:700;font-size:0.95rem;margin:0;">حسابك قيد المراجعة</p>
            <p style="color:#94a3b8;font-size:0.82rem;margin:4px 0 0;">بانتظار موافقة الإدارة</p>
        </div>
    </div>
@elseif($user->approval_status === 'rejected')
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:14px;padding:16px 18px;margin-bottom:18px;">
        <p style="color:#fca5a5;font-weight:700;font-size:0.95rem;margin:0;">❌ تم رفض حسابك</p>
        @if($user->rejection_reason)
            <p style="color:#94a3b8;font-size:0.82rem;margin:4px 0 0;">{{ $user->rejection_reason }}</p>
        @endif
    </div>
@endif

{{-- ═══════════════ Subscriptions list ═══════════════ --}}
@if($subscriptions->isEmpty())
    <div class="c-card" style="text-align:center;padding:40px 20px;">
        <svg width="48" height="48" fill="none" stroke="#475569" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p style="color:#94a3b8;font-size:0.95rem;margin:0;">لا توجد اشتراكات بعد</p>
        <p style="color:#64748b;font-size:0.82rem;margin:8px 0 0;">اضغط "الباقات" لإنشاء اشتراكك الأول</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:14px;">
        @foreach($subscriptions as $sub)
            @php
                $isActive    = $sub->status === 'active';
                $isScheduled = $sub->status === 'scheduled';
                $isPending   = $sub->status === 'pending';
                $isCancelled = $sub->status === 'cancelled';
                $isExpired   = $sub->status === 'expired' || (!$isActive && !$isPending && !$isScheduled && !$isCancelled);

                $planNames = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                $planName  = $planNames[$sub->plan_type] ?? $sub->plan_type;
            @endphp

            <div style="border-radius:18px;overflow:hidden;
                @if($isActive) border:2px solid rgba(34,197,94,0.5);
                @elseif($isScheduled) border:2px solid rgba(59,130,246,0.5);
                @elseif($isPending) border:2px solid rgba(234,179,8,0.4);
                @else border:1px solid rgba(255,255,255,0.08); @endif">

                {{-- Card header --}}
                <div style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
                    @if($isActive) background:linear-gradient(135deg,rgba(34,197,94,0.18),rgba(20,184,166,0.1));
                    @elseif($isScheduled) background:linear-gradient(135deg,rgba(59,130,246,0.18),rgba(99,102,241,0.1));
                    @elseif($isPending) background:rgba(234,179,8,0.1);
                    @else background:rgba(255,255,255,0.03); @endif">

                    <div style="display:flex;align-items:center;gap:10px;">
                        <div>
                            <p style="color:#f8fafc;font-weight:800;font-size:1rem;margin:0;">باقة {{ $planName }}</p>
                            <p style="color:#94a3b8;font-size:0.76rem;margin:3px 0 0;">
                                @if($sub->start_date) {{ $sub->start_date->format('Y-m-d') }} @endif
                                @if($sub->end_date) — {{ $sub->end_date->format('Y-m-d') }} @endif
                            </p>
                        </div>
                    </div>

                    {{-- Status badge --}}
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($isActive)
                            <span style="background:rgba(34,197,94,0.2);color:#86efac;border:1px solid rgba(34,197,94,0.4);padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">● نشط</span>
                        @elseif($isScheduled)
                            <span style="background:rgba(59,130,246,0.2);color:#93c5fd;border:1px solid rgba(59,130,246,0.4);padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">◷ مجدوَل</span>
                        @elseif($isPending)
                            <span style="background:rgba(234,179,8,0.2);color:#fde047;border:1px solid rgba(234,179,8,0.4);padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">⏳ قيد المراجعة</span>
                        @elseif($isCancelled)
                            <span style="background:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">✕ ملغي</span>
                        @else
                            <span style="background:rgba(100,116,139,0.2);color:#94a3b8;border:1px solid rgba(100,116,139,0.3);padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">منتهي</span>
                        @endif
                    </div>
                </div>

                {{-- Card body --}}
                <div style="background:rgba(15,23,42,0.4);padding:14px 18px;">
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                        <div style="display:flex;gap:20px;flex-wrap:wrap;">
                            <div>
                                <p style="color:#64748b;font-size:0.72rem;margin:0;">السعر</p>
                                <p style="color:#fb923c;font-weight:800;font-size:0.95rem;margin:3px 0 0;">${{ rtrim(rtrim(number_format($sub->price,2),'0'),'.') }}</p>
                            </div>
                            @if($sub->remaining_car_washes > 0)
                            <div>
                                <p style="color:#64748b;font-size:0.72rem;margin:0;">غسيل متبقي</p>
                                <p style="color:#86efac;font-weight:700;font-size:0.95rem;margin:3px 0 0;">{{ $sub->remaining_car_washes }}</p>
                            </div>
                            @endif
                            @if($sub->remaining_maintenance > 0)
                            <div>
                                <p style="color:#64748b;font-size:0.72rem;margin:0;">صيانة متبقية</p>
                                <p style="color:#c4b5fd;font-weight:700;font-size:0.95rem;margin:3px 0 0;">{{ $sub->remaining_maintenance }}</p>
                            </div>
                            @endif
                            @if($isActive && $sub->end_date)
                            <div>
                                <p style="color:#64748b;font-size:0.72rem;margin:0;">ينتهي خلال</p>
                                <p style="color:#f8fafc;font-weight:700;font-size:0.95rem;margin:3px 0 0;">
                                    {{ now()->diffInDays($sub->end_date) }} يوم
                                </p>
                            </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if($isActive || $isScheduled || $isPending)
                        <button wire:click="confirmCancel({{ $sub->id }})"
                                style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:7px 14px;font-size:0.8rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;white-space:nowrap;">
                            إلغاء الاشتراك
                        </button>
                        @endif
                    </div>

                    {{-- Expiry warning --}}
                    @if($isActive && $sub->end_date && now()->diffInDays($sub->end_date) <= 7)
                    <div style="margin-top:12px;background:rgba(234,179,8,0.12);border:1px solid rgba(234,179,8,0.3);border-radius:10px;padding:8px 12px;font-size:0.8rem;color:#fde047;">
                        ⚠️ اشتراكك سينتهي خلال {{ now()->diffInDays($sub->end_date) }} أيام. يمكنك تجديده الآن من زر "الباقات".
                    </div>
                    @endif

                    {{-- Scheduled note --}}
                    @if($isScheduled)
                    <div style="margin-top:12px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.25);border-radius:10px;padding:8px 12px;font-size:0.8rem;color:#93c5fd;">
                        ◷ هذا الاشتراك مجدوَل ليبدأ بتاريخ {{ $sub->start_date?->format('Y-m-d') }}
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- ═══════════════ Cancel confirm dialog ═══════════════ --}}
@if($cancelConfirmId)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:60;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#1e293b;border:1px solid rgba(255,255,255,0.1);border-radius:20px;padding:28px;max-width:380px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
        <div style="font-size:2.5rem;margin-bottom:12px;">⚠️</div>
        <h3 style="color:#f8fafc;font-weight:800;font-size:1.1rem;margin:0 0 8px;">تأكيد الإلغاء</h3>
        <p style="color:#94a3b8;font-size:0.88rem;margin:0 0 22px;">هل أنت متأكد من إلغاء الاشتراك؟ لا يمكن التراجع عن هذه العملية.</p>
        <div style="display:flex;gap:10px;">
            <button wire:click="cancelSubscription" style="flex:1;background:#ef4444;color:white;border:none;border-radius:12px;padding:12px;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:'Tajawal',sans-serif;">نعم، إلغاء</button>
            <button wire:click="cancelConfirmClose" style="flex:1;background:rgba(255,255,255,0.08);color:#cbd5e1;border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:12px;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:'Tajawal',sans-serif;">تراجع</button>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════ Plan wizard modal ═══════════════ --}}
@if($step > 0)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:60;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto;">
    <div style="background:#0f1629;border:1px solid rgba(255,255,255,0.1);border-radius:22px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,0.6);">

        {{-- Modal header --}}
        <div style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="color:#f8fafc;font-weight:800;font-size:1.1rem;margin:0;">
                @if($step === 1) اختر باقة الاشتراك
                @elseif($step === 2) كيف تريد تفعيل الباقة؟
                @endif
            </h3>
            <button wire:click="closePlanModal" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:1.1rem;">✕</button>
        </div>

        <div style="padding:24px;">

            {{-- ───── STEP 1: Select plan ───── --}}
            @if($step === 1)
                @error('plan') <div class="c-error">{{ $message }}</div> @enderror

                <div class="grid-2">
                    @foreach($plans as $key => $plan)
                        <div wire:click="selectPlan('{{ $key }}')"
                             style="cursor:pointer;border-radius:14px;padding:16px;transition:.2s;position:relative;
                             @if($selectedPlan === $key) background:linear-gradient(135deg,rgba(249,115,22,0.18),rgba(234,88,12,0.06));border:2px solid #f97316;box-shadow:0 0 20px rgba(249,115,22,0.15);
                             @else background:rgba(255,255,255,0.03);border:2px solid rgba(255,255,255,0.08); @endif">

                            @if($selectedPlan === $key)
                                <div style="position:absolute;top:10px;left:10px;width:22px;height:22px;border-radius:50%;background:#f97316;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:800;">✓</div>
                            @endif

                            <div style="color:#f97316;font-size:1.5rem;font-weight:800;margin-bottom:6px;">
                                ${{ rtrim(rtrim(number_format($plan['price'],2),'0'),'.') }}
                            </div>
                            <h4 style="color:#f8fafc;font-size:0.95rem;font-weight:800;margin:0 0 5px;">
                                @switch($key)
                                    @case('monthly') باقة شهرية @break
                                    @case('3months') باقة 3 أشهر @break
                                    @case('6months') باقة 6 أشهر @break
                                    @case('yearly') باقة سنوية @break
                                @endswitch
                            </h4>
                            <p style="color:#64748b;font-size:0.76rem;line-height:1.5;margin:0 0 8px;">{{ $plan['description'] }}</p>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                <span style="font-size:0.68rem;background:rgba(59,130,246,0.18);color:#93c5fd;padding:2px 8px;border-radius:999px;">{{ $plan['duration_months'] }} شهر</span>
                                @if($plan['car_washes'] > 0)<span style="font-size:0.68rem;background:rgba(34,197,94,0.18);color:#86efac;padding:2px 8px;border-radius:999px;">{{ $plan['car_washes'] }} غسيل</span>@endif
                                @if($plan['maintenance'] > 0)<span style="font-size:0.68rem;background:rgba(168,85,247,0.18);color:#c4b5fd;padding:2px 8px;border-radius:999px;">{{ $plan['maintenance'] }} صيانة</span>@endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex-btns" style="margin-top:22px;">
                    <button type="button" wire:click="closePlanModal" class="c-btn c-btn-ghost" style="flex:0 0 120px;">إلغاء</button>
                    <button type="button" wire:click="nextFromPlan" class="c-btn" style="flex:1;">التالي ←</button>
                </div>
            @endif

            {{-- ───── STEP 2: Choose activation type ───── --}}
            @if($step === 2)
                @php
                    $currentSub = \App\Models\Subscription::where('user_id', auth()->id())
                        ->whereIn('status', ['active','scheduled','pending'])
                        ->orderByRaw("FIELD(status,'active','scheduled','pending')")
                        ->latest('id')->first();
                    $statusLabels = ['active'=>'نشط','scheduled'=>'مجدوَل','pending'=>'قيد المراجعة'];
                    $currentLabel = $statusLabels[$currentSub?->status ?? ''] ?? '';
                    $planNames = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                    $currentPlanName = $planNames[$currentSub?->plan_type ?? ''] ?? '';
                @endphp

                <p style="color:#94a3b8;font-size:0.86rem;margin:0 0 6px;">
                    لديك اشتراك حالي:
                    <span style="color:#f8fafc;font-weight:700;">باقة {{ $currentPlanName }}</span>
                    @if($currentLabel)
                        <span style="font-size:0.75rem;background:rgba(249,115,22,0.18);color:#fb923c;padding:2px 8px;border-radius:999px;margin-right:6px;">{{ $currentLabel }}</span>
                    @endif
                </p>
                <p style="color:#64748b;font-size:0.82rem;margin:0 0 20px;">كيف تريد تفعيل الباقة الجديدة؟</p>

                <div style="display:flex;flex-direction:column;gap:12px;">

                    {{-- Replace --}}
                    <div wire:click="chooseType('replace')" wire:loading.attr="disabled"
                         style="cursor:pointer;border:2px solid rgba(249,115,22,0.3);border-radius:14px;padding:18px;background:rgba(249,115,22,0.06);transition:.2s;"
                         onmouseover="this.style.borderColor='#f97316';this.style.background='rgba(249,115,22,0.12)'"
                         onmouseout="this.style.borderColor='rgba(249,115,22,0.3)';this.style.background='rgba(249,115,22,0.06)'">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:44px;height:44px;background:rgba(249,115,22,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem;">
                                🔄
                            </div>
                            <div>
                                <p style="color:#f8fafc;font-weight:800;font-size:0.96rem;margin:0 0 3px;">استبدال فوري</p>
                                <p style="color:#94a3b8;font-size:0.8rem;margin:0;line-height:1.5;">يُلغى الاشتراك الحالي ويبدأ الجديد فور موافقة الإدارة على السند</p>
                            </div>
                            <svg width="18" height="18" fill="none" stroke="#f97316" viewBox="0 0 24 24" style="margin-right:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </div>
                    </div>

                    {{-- After expiry --}}
                    <div wire:click="chooseType('after')" wire:loading.attr="disabled"
                         style="cursor:pointer;border:2px solid rgba(59,130,246,0.3);border-radius:14px;padding:18px;background:rgba(59,130,246,0.06);transition:.2s;"
                         onmouseover="this.style.borderColor='#3b82f6';this.style.background='rgba(59,130,246,0.12)'"
                         onmouseout="this.style.borderColor='rgba(59,130,246,0.3)';this.style.background='rgba(59,130,246,0.06)'">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:44px;height:44px;background:rgba(59,130,246,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem;">
                                ⏰
                            </div>
                            <div>
                                <p style="color:#f8fafc;font-weight:800;font-size:0.96rem;margin:0 0 3px;">بعد انتهاء الاشتراك الحالي</p>
                                <p style="color:#94a3b8;font-size:0.8rem;margin:0;line-height:1.5;">يُرسَل طلبك للمراجعة والاشتراك الجديد يبدأ تلقائياً عند انتهاء الحالي</p>
                            </div>
                            <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" style="margin-right:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </div>
                    </div>

                </div>

                <button type="button" wire:click="$set('step', 1)"
                        style="margin-top:18px;background:transparent;border:none;color:#64748b;font-size:0.82rem;cursor:pointer;font-family:'Tajawal',sans-serif;padding:4px 0;display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    رجوع لاختيار الباقة
                </button>
            @endif

        </div>
    </div>
</div>
@endif

{{-- Logout --}}
<form method="POST" action="{{ route('logout') }}" style="margin-top:22px;text-align:center;">
    @csrf
    <button type="submit" class="c-btn c-btn-ghost" style="width:auto;padding:10px 28px;">تسجيل الخروج</button>
</form>

</div>
