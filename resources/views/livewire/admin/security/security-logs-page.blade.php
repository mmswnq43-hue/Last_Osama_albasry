<div>
    {{-- Filters --}}
    <div style="background:white;border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;box-shadow:0 1px 8px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <select wire:model.live="typeFilter" class="input-field">
            <option value="">كل الأنواع</option>
            <option value="failed_login">محاولة دخول فاشلة</option>
            <option value="qr_session">جلسة QR</option>
            <option value="account_locked">قفل حساب</option>
            <option value="two_factor_enabled">تفعيل 2FA</option>
            <option value="two_factor_disabled">تعطيل 2FA</option>
        </select>
        <input wire:model.live="dateFrom" type="date" class="input-field">
    </div>

    <div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#1e2a4a,#1e3a8a);">
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">المستخدم</th>
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">النوع</th>
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">IP</th>
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">الحالة</th>
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">الملاحظة</th>
                        <th style="text-align:right;padding:12px 16px;color:rgba(255,255,255,0.8);font-weight:600;font-size:0.75rem;">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="table-row" style="border-top:1px solid #f8faff;">
                        <td style="padding:12px 16px;">
                            <p style="font-weight:600;color:#1e293b;">{{ $log->user?->full_name ?? 'مجهول' }}</p>
                            <p style="color:#94a3b8;font-size:0.72rem;" dir="ltr">{{ $log->user?->phone ?? '-' }}</p>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-blue" style="font-size:0.7rem;">{{ $log->log_type }}</span>
                        </td>
                        <td style="padding:12px 16px;color:#64748b;font-size:0.75rem;font-family:monospace;" dir="ltr">{{ $log->ip_address ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            @if($log->is_successful)
                                <span class="badge badge-green">✓ ناجح</span>
                            @else
                                <span class="badge badge-red">✗ فاشل</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#64748b;font-size:0.75rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->error_message ?? '-' }}</td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:0.72rem;white-space:nowrap;">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:48px;text-align:center;color:#94a3b8;">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">{{ $logs->links() }}</div>
    </div>
</div>
