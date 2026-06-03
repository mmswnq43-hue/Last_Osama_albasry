<div style="padding:20px 16px 10px;">

{{-- ═══ Header ═══ --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="color:#f8fafc;font-size:1.25rem;font-weight:800;margin:0;">اشتراكاتي</h1>
        <p style="color:#94a3b8;font-size:0.82rem;margin:4px 0 0;">إدارة باقات الاشتراك</p>
    </div>
    <button wire:click="openPlanModal" class="c-btn" style="width:auto;padding:11px 20px;display:flex;align-items:center;gap:8px;font-size:0.88rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        باقة جديدة
    </button>
</div>

{{-- ═══ Account status banner ═══ --}}
@if($user->approval_status === 'pending')
    <div style="background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:1.4rem;">⏳</span>
        <div>
            <p style="color:#fde047;font-weight:700;font-size:0.9rem;margin:0;">حسابك قيد المراجعة</p>
            <p style="color:#94a3b8;font-size:0.8rem;margin:4px 0 0;">بانتظار موافقة الإدارة</p>
        </div>
    </div>
@elseif($user->approval_status === 'rejected')
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:14px;padding:14px 18px;margin-bottom:18px;">
        <p style="color:#fca5a5;font-weight:700;font-size:0.9rem;margin:0;">❌ تم رفض حسابك</p>
        @if($user->rejection_reason)
            <p style="color:#94a3b8;font-size:0.8rem;margin:4px 0 0;">{{ $user->rejection_reason }}</p>
        @endif
    </div>
@endif

{{-- ═══ Subscriptions list ═══ --}}
@if($subscriptions->isEmpty())
    <div class="c-card" style="text-align:center;padding:40px 20px;">
        <svg width="48" height="48" fill="none" stroke="#475569" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p style="color:#94a3b8;font-size:0.95rem;margin:0 0 6px;">لا توجد اشتراكات بعد</p>
        <p style="color:#64748b;font-size:0.82rem;margin:0 0 18px;">اضغط "باقة جديدة" لإنشاء اشتراكك الأول</p>
        <button wire:click="openPlanModal" class="c-btn" style="width:auto;padding:11px 28px;display:inline-block;">
            استعرض الباقات
        </button>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:14px;">
        @foreach($subscriptions as $sub)
            @php
                $isActive    = $sub->status === 'active';
                $isScheduled = $sub->status === 'scheduled';
                $isPending   = $sub->status === 'pending';
                $isCancelled = $sub->status === 'cancelled';
                $isExpired   = !$isActive && !$isPending && !$isScheduled && !$isCancelled;
                $planNames   = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                $planName    = $planNames[$sub->plan_type] ?? $sub->plan_type;
            @endphp

            <div style="border-radius:18px;overflow:hidden;
                @if($isActive) border:2px solid rgba(34,197,94,0.5);
                @elseif($isScheduled) border:2px solid rgba(59,130,246,0.5);
                @elseif($isPending) border:2px solid rgba(234,179,8,0.4);
                @else border:1px solid rgba(255,255,255,0.08); @endif">

                <div style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
                    @if($isActive) background:linear-gradient(135deg,rgba(34,197,94,0.18),rgba(20,184,166,0.1));
                    @elseif($isScheduled) background:linear-gradient(135deg,rgba(59,130,246,0.18),rgba(99,102,241,0.1));
                    @elseif($isPending) background:rgba(234,179,8,0.1);
                    @else background:rgba(255,255,255,0.03); @endif">

                    <div>
                        <p style="color:#f8fafc;font-weight:800;font-size:1rem;margin:0;">باقة {{ $planName }}</p>
                        <p style="color:#94a3b8;font-size:0.76rem;margin:3px 0 0;" dir="ltr">
                            @if($sub->start_date) {{ $sub->start_date->format('Y-m-d') }} @endif
                            @if($sub->end_date) — {{ $sub->end_date->format('Y-m-d') }} @endif
                        </p>
                    </div>

                    <div>
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
                                <p style="color:#f8fafc;font-weight:700;font-size:0.95rem;margin:3px 0 0;">{{ now()->diffInDays($sub->end_date) }} يوم</p>
                            </div>
                            @endif
                        </div>

                        @if($isActive || $isScheduled || $isPending)
                        <button wire:click="confirmCancel({{ $sub->id }})"
                                style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:10px;padding:7px 14px;font-size:0.8rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;white-space:nowrap;">
                            إلغاء الاشتراك
                        </button>
                        @endif
                    </div>

                    @if($isActive && $sub->end_date && now()->diffInDays($sub->end_date) <= 7)
                    <div style="margin-top:12px;background:rgba(234,179,8,0.12);border:1px solid rgba(234,179,8,0.3);border-radius:10px;padding:8px 12px;font-size:0.8rem;color:#fde047;">
                        ⚠️ اشتراكك سينتهي خلال {{ now()->diffInDays($sub->end_date) }} أيام. يمكنك تجديده من زر "باقة جديدة".
                    </div>
                    @endif

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

{{-- ═══ Cancel confirm dialog ═══ --}}
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

{{-- ═══ Plan wizard modal ═══ --}}
@if($step > 0)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:60;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto;">
    <div style="background:#0f1629;border:1px solid rgba(255,255,255,0.1);border-radius:22px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,0.6);">

        <div style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="color:#f8fafc;font-weight:800;font-size:1.1rem;margin:0;">
                @if($step === 1) اختر باقة الاشتراك
                @elseif($step === 2) كيف تريد تفعيل الباقة؟
                @endif
            </h3>
            <button wire:click="closePlanModal" style="background:rgba(255,255,255,0.07);border:none;color:#94a3b8;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:1.1rem;">✕</button>
        </div>

        <div style="padding:24px;">

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

            @if($step === 2)
                @php
                    $currentSub = \App\Models\Subscription::where('user_id', auth()->id())
                        ->whereIn('status', ['active','scheduled','pending'])
                        ->orderByRaw("FIELD(status,'active','scheduled','pending')")
                        ->latest('id')->first();
                    $statusLabels = ['active'=>'نشط','scheduled'=>'مجدوَل','pending'=>'قيد المراجعة'];
                    $currentLabel = $statusLabels[$currentSub?->status ?? ''] ?? '';
                    $planNames2 = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                    $currentPlanName = $planNames2[$currentSub?->plan_type ?? ''] ?? '';
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
                    <div wire:click="chooseType('replace')" wire:loading.attr="disabled"
                         style="cursor:pointer;border:2px solid rgba(249,115,22,0.3);border-radius:14px;padding:18px;background:rgba(249,115,22,0.06);transition:.2s;"
                         onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='rgba(249,115,22,0.3)'">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:44px;height:44px;background:rgba(249,115,22,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem;">🔄</div>
                            <div>
                                <p style="color:#f8fafc;font-weight:800;font-size:0.96rem;margin:0 0 3px;">استبدال فوري</p>
                                <p style="color:#94a3b8;font-size:0.8rem;margin:0;line-height:1.5;">يُلغى الاشتراك الحالي ويبدأ الجديد فور موافقة الإدارة</p>
                            </div>
                        </div>
                    </div>

                    <div wire:click="chooseType('after')" wire:loading.attr="disabled"
                         style="cursor:pointer;border:2px solid rgba(59,130,246,0.3);border-radius:14px;padding:18px;background:rgba(59,130,246,0.06);transition:.2s;"
                         onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='rgba(59,130,246,0.3)'">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:44px;height:44px;background:rgba(59,130,246,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.3rem;">⏰</div>
                            <div>
                                <p style="color:#f8fafc;font-weight:800;font-size:0.96rem;margin:0 0 3px;">بعد انتهاء الاشتراك الحالي</p>
                                <p style="color:#94a3b8;font-size:0.8rem;margin:0;line-height:1.5;">يبدأ الاشتراك الجديد تلقائياً عند انتهاء الحالي</p>
                            </div>
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

</div>
