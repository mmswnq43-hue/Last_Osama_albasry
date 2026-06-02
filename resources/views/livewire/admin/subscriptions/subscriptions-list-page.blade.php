<div>
    {{-- Success message --}}
    @if($successMessage)
    <div x-data="{show:true}" x-init="setTimeout(()=>show=false,4000)" x-show="show"
         style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    {{-- Filters --}}
    <div style="background:white;border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;box-shadow:0 1px 8px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <select wire:model.live="statusFilter" class="input-field" style="min-width:160px;">
            <option value="">كل الحالات</option>
            <option value="pending">⏳ قيد المراجعة</option>
            <option value="active">● فعال</option>
            <option value="scheduled">◷ مجدوَل</option>
            <option value="cancelled">ملغي</option>
            <option value="expired">منتهي</option>
        </select>
        <select wire:model.live="planFilter" class="input-field" style="min-width:140px;">
            <option value="">كل الباقات</option>
            <option value="monthly">شهري</option>
            <option value="3months">3 أشهر</option>
            <option value="6months">6 أشهر</option>
            <option value="yearly">سنوي</option>
        </select>
        @php $pendingCount = \App\Models\Subscription::where('status','pending')->count(); @endphp
        @if($pendingCount > 0)
        <span style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;padding:5px 12px;border-radius:999px;font-size:0.78rem;font-weight:700;">
            ⏳ {{ $pendingCount }} بانتظار الموافقة
        </span>
        @endif
    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#fff7ed,#ffedd5);">
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">المستخدم</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">الباقة</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">السعر</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">الحالة</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">تاريخ الانتهاء</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;white-space:nowrap;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                    <tr class="table-row" style="border-top:1px solid #fffbf5;@if($sub->status==='pending') background:#fffbeb; @endif">
                        <td style="padding:12px 16px;">
                            <p style="font-weight:600;color:#1e293b;margin:0;">{{ $sub->user?->full_name ?? '-' }}</p>
                            <p style="color:#94a3b8;font-size:0.74rem;margin:2px 0 0;" dir="ltr">{{ $sub->user?->phone ?? '-' }}</p>
                        </td>
                        <td style="padding:12px 16px;">
                            @php
                                $planNames = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                            @endphp
                            <span class="badge badge-blue">{{ $planNames[$sub->plan_type] ?? $sub->plan_type }}</span>
                            @if($sub->notes && str_contains($sub->notes, 'renewal:'))
                                @if(str_contains($sub->notes, 'replace'))
                                    <span class="badge badge-orange" style="margin-top:3px;display:block;width:fit-content;">استبدال</span>
                                @elseif(str_contains($sub->notes, 'after'))
                                    <span class="badge badge-slate" style="margin-top:3px;display:block;width:fit-content;">مجدوَل</span>
                                @endif
                            @endif
                        </td>
                        <td style="padding:12px 16px;font-weight:700;color:#ea580c;white-space:nowrap;">${{ rtrim(rtrim(number_format($sub->price,2),'0'),'.') }}</td>
                        <td style="padding:12px 16px;">
                            @if($sub->status === 'active')
                                <span class="badge badge-green">● فعال</span>
                            @elseif($sub->status === 'pending')
                                <span class="badge badge-yellow">⏳ قيد المراجعة</span>
                            @elseif($sub->status === 'scheduled')
                                <span class="badge badge-blue">◷ مجدوَل</span>
                            @elseif($sub->status === 'cancelled')
                                <span class="badge badge-red">ملغي</span>
                            @elseif($sub->status === 'expired')
                                <span class="badge badge-slate">منتهي</span>
                            @else
                                <span class="badge badge-slate">{{ $sub->status }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:0.75rem;white-space:nowrap;">{{ $sub->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                {{-- View receipt --}}
                                @if($sub->payment_receipt_image)
                                <button wire:click="viewReceipt({{ $sub->id }})"
                                        style="padding:5px 10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;white-space:nowrap;">
                                    📄 السند
                                </button>
                                @endif

                                {{-- Approve / Reject buttons for pending --}}
                                @if($sub->status === 'pending')
                                <button wire:click="approve({{ $sub->id }})"
                                        style="padding:5px 12px;background:#f0fdf4;color:#16a34a;border:1px solid #86efac;border-radius:7px;font-size:0.72rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                                    ✓ قبول
                                </button>
                                <button wire:click="confirmReject({{ $sub->id }})"
                                        style="padding:5px 12px;background:#fff5f5;color:#dc2626;border:1px solid #fca5a5;border-radius:7px;font-size:0.72rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                                    ✕ رفض
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:48px;text-align:center;color:#94a3b8;">لا توجد اشتراكات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">{{ $subscriptions->links() }}</div>
    </div>

    {{-- Reject Confirm Dialog --}}
    @if($rejectConfirmId)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:360px;padding:28px;text-align:center;">
            <div style="font-size:2.2rem;margin-bottom:12px;">⚠️</div>
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 8px;">تأكيد رفض الاشتراك</h3>
            <p style="color:#64748b;font-size:0.85rem;margin:0 0 22px;">هل أنت متأكد من رفض هذا الطلب؟ سيصل إشعار للعميل.</p>
            <div style="display:flex;gap:10px;">
                <button wire:click="reject({{ $rejectConfirmId }})"
                        style="flex:1;background:#dc2626;color:white;border:none;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    نعم، رفض
                </button>
                <button wire:click="closeModal"
                        style="flex:1;border:1.5px solid #e2e8f0;background:white;border-radius:10px;padding:11px;font-weight:600;font-size:0.875rem;color:#475569;cursor:pointer;font-family:'Tajawal',sans-serif;">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Receipt Modal --}}
    @if($showModal === 'receipt' && $selectedSub)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:440px;">
            <div style="background:linear-gradient(135deg,#fff7ed,#ffedd5);padding:18px 22px;border-bottom:1px solid #fed7aa;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-weight:700;color:#7c2d12;font-size:1rem;margin:0;">تفاصيل الاشتراك</h3>
                <button wire:click="closeModal" style="background:rgba(0,0,0,0.05);border:none;border-radius:8px;padding:6px;cursor:pointer;">
                    <svg width="16" height="16" fill="none" stroke="#78350f" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:20px 22px;display:flex;flex-direction:column;gap:2px;font-size:0.84rem;">
                @php
                    $planNames2 = ['monthly'=>'شهرية','3months'=>'3 أشهر','6months'=>'6 أشهر','yearly'=>'سنوية'];
                    $typeLabel  = '';
                    if(str_contains($selectedSub['notes'] ?? '', 'renewal:replace')) $typeLabel = '🔄 استبدال فوري';
                    elseif(str_contains($selectedSub['notes'] ?? '', 'renewal:after')) $typeLabel = '⏰ بعد انتهاء الحالي';
                    elseif(str_contains($selectedSub['notes'] ?? '', 'renewal:immediate')) $typeLabel = '⚡ اشتراك جديد';
                @endphp
                @foreach([
                    ['العميل', $selectedSub['user_name']],
                    ['الهاتف', $selectedSub['user_phone']],
                    ['الباقة', ($planNames2[$selectedSub['plan_type']] ?? $selectedSub['plan_type'])],
                    ['النوع', $typeLabel ?: 'اشتراك جديد'],
                    ['السعر', '$'.rtrim(rtrim(number_format($selectedSub['price'],2),'0'),'.')],
                    ['تاريخ الطلب', $selectedSub['created_at']],
                    ['بداية الاشتراك', $selectedSub['start_date'] ?? '-'],
                    ['نهاية الاشتراك', $selectedSub['end_date'] ?? '-'],
                ] as [$label, $value])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f8faff;">
                    <span style="color:#64748b;">{{ $label }}</span>
                    <strong style="color:#1e293b;text-align:left;" dir="{{ in_array($label,['الهاتف']) ? 'ltr' : 'rtl' }}">{{ $value }}</strong>
                </div>
                @endforeach

                {{-- Receipt link --}}
                @if($selectedSub['receipt_url'])
                <div style="margin-top:12px;padding:12px;background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe;display:flex;justify-content:space-between;align-items:center;">
                    <p style="color:#1d4ed8;font-size:0.78rem;font-weight:600;margin:0;">السند البنكي</p>
                    <a href="{{ $selectedSub['receipt_url'] }}" target="_blank" rel="noopener"
                       style="color:#3b82f6;font-size:0.82rem;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:5px;">
                        عرض السند
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                @endif

                {{-- Approve/Reject buttons inside modal --}}
                @if($selectedSub['status'] === 'pending')
                <div style="display:flex;gap:10px;margin-top:16px;">
                    <button wire:click="approve({{ $selectedSub['id'] }})"
                            style="flex:1;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:10px;padding:11px;font-weight:700;font-size:0.875rem;cursor:pointer;font-family:'Tajawal',sans-serif;">
                        ✓ قبول الاشتراك
                    </button>
                    <button wire:click="confirmReject({{ $selectedSub['id'] }})"
                            style="flex:1;background:white;color:#dc2626;border:1.5px solid #fca5a5;border-radius:10px;padding:11px;font-weight:700;font-size:0.875rem;cursor:pointer;font-family:'Tajawal',sans-serif;">
                        ✕ رفض
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
