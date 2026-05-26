<div>
    @if($successMessage)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    {{-- Filters --}}
    <div style="background:white;border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;box-shadow:0 1px 8px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <select wire:model.live="statusFilter" class="input-field">
            <option value="">كل الحالات</option>
            <option value="pending_payment">بانتظار الدفع</option>
            <option value="active">فعال</option>
            <option value="expired">منتهي</option>
            <option value="rejected">مرفوض</option>
        </select>
        <select wire:model.live="planFilter" class="input-field">
            <option value="">كل الباقات</option>
            <option value="monthly">شهري</option>
            <option value="3months">3 أشهر</option>
            <option value="6months">6 أشهر</option>
            <option value="yearly">سنوي</option>
        </select>
    </div>

    <div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#fff7ed,#ffedd5);">
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">المستخدم</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">الباقة</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">السعر</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">الحالة</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">تاريخ الانتهاء</th>
                        <th style="text-align:right;padding:12px 16px;color:#7c2d12;font-weight:700;font-size:0.75rem;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                    <tr class="table-row" style="border-top:1px solid #fffbf5;">
                        <td style="padding:12px 16px;">
                            <p style="font-weight:600;color:#1e293b;">{{ $sub->user?->full_name ?? '-' }}</p>
                            <p style="color:#94a3b8;font-size:0.75rem;" dir="ltr">{{ $sub->user?->phone ?? '-' }}</p>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-blue">{{ $sub->plan_type }}</span>
                        </td>
                        <td style="padding:12px 16px;font-weight:700;color:#ea580c;">{{ number_format($sub->price) }} ر.ي</td>
                        <td style="padding:12px 16px;">
                            @if($sub->status === 'active')
                                <span class="badge badge-green">● فعال</span>
                            @elseif($sub->status === 'pending_payment')
                                <span class="badge badge-yellow">⏳ بانتظار الدفع</span>
                            @elseif($sub->status === 'rejected')
                                <span class="badge badge-red">مرفوض</span>
                            @else
                                <span class="badge badge-slate">{{ $sub->status }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:0.75rem;">{{ $sub->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @if($sub->payment_receipt_image)
                                <button wire:click="viewReceipt({{ $sub->id }})" style="padding:5px 10px;background:#eff6ff;color:#1d4ed8;border:none;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;">السند</button>
                                @endif
                                @if($sub->status === 'pending_payment')
                                <button wire:click="approve({{ $sub->id }})" style="padding:5px 10px;background:#f0fdf4;color:#16a34a;border:none;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;">قبول</button>
                                <button wire:click="reject({{ $sub->id }})" style="padding:5px 10px;background:#fff5f5;color:#dc2626;border:none;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;">رفض</button>
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

    {{-- Receipt Modal --}}
    @if($showModal === 'receipt' && $selectedSub)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:420px;">
            <div style="background:linear-gradient(135deg,#fff7ed,#ffedd5);padding:18px 22px;border-bottom:1px solid #fed7aa;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-weight:700;color:#7c2d12;font-size:1rem;">تفاصيل الاشتراك</h3>
                <button wire:click="closeModal" style="background:rgba(0,0,0,0.05);border:none;border-radius:8px;padding:6px;cursor:pointer;">
                    <svg width="16" height="16" fill="none" stroke="#78350f" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:20px 22px;display:flex;flex-direction:column;gap:10px;font-size:0.85rem;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8faff;">
                    <span style="color:#64748b;">المستخدم</span><strong>{{ $selectedSub['user_name'] }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8faff;">
                    <span style="color:#64748b;">الهاتف</span><span dir="ltr">{{ $selectedSub['user_phone'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8faff;">
                    <span style="color:#64748b;">الباقة</span><strong>{{ $selectedSub['plan_type'] }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f8faff;">
                    <span style="color:#64748b;">السعر</span><strong style="color:#ea580c;">{{ number_format($selectedSub['price']) }} ر.ي</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;">
                    <span style="color:#64748b;">تاريخ الطلب</span><span>{{ $selectedSub['created_at'] }}</span>
                </div>
                @if($selectedSub['payment_receipt_image'])
                <div style="margin-top:8px;padding:12px;background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe;">
                    <p style="color:#1d4ed8;font-size:0.75rem;font-weight:600;margin-bottom:6px;">السند البنكي</p>
                    <a href="/storage/{{ $selectedSub['payment_receipt_image'] }}" target="_blank" style="color:#3b82f6;font-size:0.82rem;font-weight:600;text-decoration:none;">
                        ← عرض الصورة في تبويب جديد
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
