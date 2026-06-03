<div>
    {{-- Alert Banner --}}
    @if($pendingUsersCount > 0)
    <div style="background:linear-gradient(135deg,#fff7ed,#fef3c7); border:1.5px solid #fed7aa; border-radius:14px; padding:14px 18px; margin-bottom:22px; display:flex; align-items:center; gap:12px;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#f97316,#fb923c);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(249,115,22,0.3);">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div style="flex:1;">
            <p style="color:#92400e;font-weight:700;font-size:0.875rem;">يوجد <span style="color:#ea580c;">{{ $pendingUsersCount }}</span> طلب انضمام جديد بانتظار المراجعة</p>
            <p style="color:#b45309;font-size:0.75rem;margin-top:2px;">يُنصح بمراجعة الطلبات في أقرب وقت</p>
        </div>
        <a href="{{ route('admin.users.pending') }}" style="background:linear-gradient(135deg,#f97316,#ea580c);color:white;font-size:0.78rem;font-weight:700;padding:8px 16px;border-radius:8px;text-decoration:none;white-space:nowrap;box-shadow:0 3px 8px rgba(249,115,22,0.3);">
            مراجعة الطلبات ←
        </a>
    </div>
    @endif

    {{-- Hero Stats --}}
    <div class="admin-stats-grid">

        {{-- Users --}}
        <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(59,130,246,0.3);">
            <div style="position:absolute;top:-20px;left:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-30px;right:-10px;width:140px;height:140px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:999px;">المستخدمون</span>
            </div>
            <p style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['total_users']) }}</p>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.75);margin-top:6px;">{{ $stats['active_users'] }} مفعّل من الإجمالي</p>
        </div>

        {{-- Subscriptions --}}
        <div style="background:linear-gradient(135deg,#7c2d12,#f97316);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(249,115,22,0.3);">
            <div style="position:absolute;top:-20px;left:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:999px;">الاشتراكات</span>
            </div>
            <p style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['active_subscriptions']) }}</p>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.75);margin-top:6px;">{{ $stats['pending_subscriptions'] }} بانتظار القبول</p>
        </div>

        {{-- Revenue --}}
        <div style="background:linear-gradient(135deg,#064e3b,#10b981);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(16,185,129,0.3);">
            <div style="position:absolute;top:-20px;left:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:999px;">الإيرادات</span>
            </div>
            <p style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['monthly_revenue']) }}</p>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.75);margin-top:6px;">ريال يمني هذا الشهر</p>
        </div>

        {{-- Refuels --}}
        <div style="background:linear-gradient(135deg,#4c1d95,#8b5cf6);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(139,92,246,0.3);">
            <div style="position:absolute;top:-20px;left:-20px;width:100px;height:100px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:999px;">التعبئة</span>
            </div>
            <p style="font-size:2rem;font-weight:800;line-height:1;">{{ number_format($stats['today_refuels']) }}</p>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.75);margin-top:6px;">{{ number_format($stats['monthly_refuels']) }} عملية هذا الشهر</p>
        </div>
    </div>

    {{-- Business Summary + Recent Refuels --}}
    <div class="admin-two-col">

        {{-- Business Summary --}}
        <div>
            <div style="background:white;border-radius:18px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
                <h2 style="font-size:0.9rem;font-weight:700;color:#0f172a;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <span style="width:4px;height:18px;background:linear-gradient(#f97316,#3b82f6);border-radius:4px;display:inline-block;"></span>
                    نظرة على الأعمال
                </h2>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:12px;border:1px solid #bfdbfe;">
                        <div style="width:40px;height:40px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:1.4rem;font-weight:800;color:#1e3a8a;">{{ $stats['total_stations'] }}</p>
                            <p style="font-size:0.75rem;color:#3b82f6;font-weight:600;">محطة وقود &bull; {{ $stats['active_stations'] }} نشطة</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:linear-gradient(135deg,#fff7ed,#ffedd5);border-radius:12px;border:1px solid #fed7aa;">
                        <div style="width:40px;height:40px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div>
                            <p style="font-size:1.4rem;font-weight:800;color:#7c2d12;">{{ $stats['car_wash_centers'] }}</p>
                            <p style="font-size:0.75rem;color:#f97316;font-weight:600;">مغسلة &bull; {{ $stats['monthly_car_washes'] }} غسلة هذا الشهر</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:linear-gradient(135deg,#faf5ff,#ede9fe);border-radius:12px;border:1px solid #ddd6fe;">
                        <div style="width:40px;height:40px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:1.4rem;font-weight:800;color:#4c1d95;">{{ $stats['maintenance_centers'] }}</p>
                            <p style="font-size:0.75rem;color:#8b5cf6;font-weight:600;">مركز صيانة &bull; {{ $stats['monthly_maintenance'] }} خدمة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Refuels Table --}}
        <div style="background:white;border-radius:18px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;">
                <span style="width:4px;height:18px;background:linear-gradient(#f97316,#3b82f6);border-radius:4px;display:inline-block;"></span>
                <h2 style="font-size:0.9rem;font-weight:700;color:#0f172a;">آخر عمليات التعبئة</h2>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:#f8faff;">
                            <th style="text-align:right;padding:10px 16px;color:#64748b;font-weight:600;font-size:0.75rem;">المستخدم</th>
                            <th style="text-align:right;padding:10px 16px;color:#64748b;font-weight:600;font-size:0.75rem;">المحطة</th>
                            <th style="text-align:right;padding:10px 16px;color:#64748b;font-weight:600;font-size:0.75rem;">اللترات</th>
                            <th style="text-align:right;padding:10px 16px;color:#64748b;font-weight:600;font-size:0.75rem;">المبلغ</th>
                            <th style="text-align:right;padding:10px 16px;color:#64748b;font-weight:600;font-size:0.75rem;">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRefuels as $refuel)
                        <tr class="table-row" style="border-top:1px solid #f8faff;">
                            <td style="padding:11px 16px;font-weight:600;color:#1e293b;">{{ $refuel['user_name'] }}</td>
                            <td style="padding:11px 16px;color:#475569;">{{ $refuel['station_name'] }}</td>
                            <td style="padding:11px 16px;">
                                <span style="background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:0.78rem;padding:3px 8px;border-radius:6px;">{{ $refuel['liters'] }} L</span>
                            </td>
                            <td style="padding:11px 16px;font-weight:700;color:#15803d;">{{ number_format($refuel['final_price']) }} ر.ي</td>
                            <td style="padding:11px 16px;color:#94a3b8;font-size:0.75rem;">{{ \Carbon\Carbon::parse($refuel['date'])->format('Y-m-d H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;">لا توجد عمليات تعبئة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
