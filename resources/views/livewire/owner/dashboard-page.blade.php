<div>
    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
        <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d; font-weight:600; font-size:0.875rem;">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Top 4 stat cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:20px;">
        {{-- محطاتي --}}
        <div class="stat-card" style="border-right:4px solid #f97316;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <p style="color:#64748b; font-size:0.8rem; font-weight:500; margin:0;">محطاتي</p>
                <div style="width:36px; height:36px; background:#fff7ed; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <p style="color:#0f172a; font-size:1.8rem; font-weight:800; margin:0;">{{ $stats['total_stations'] }}</p>
            <p style="color:#94a3b8; font-size:0.75rem; margin:4px 0 0;">إجمالي المحطات</p>
        </div>

        {{-- مفتوحة اليوم --}}
        <div class="stat-card" style="border-right:4px solid #22c55e;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <p style="color:#64748b; font-size:0.8rem; font-weight:500; margin:0;">مفتوحة اليوم</p>
                <div style="width:36px; height:36px; background:#f0fdf4; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#22c55e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p style="color:#0f172a; font-size:1.8rem; font-weight:800; margin:0;">{{ $stats['open_stations'] }}</p>
            <p style="color:#94a3b8; font-size:0.75rem; margin:4px 0 0;">من {{ $stats['total_stations'] }} محطة</p>
        </div>

        {{-- تعبئات اليوم --}}
        <div class="stat-card" style="border-right:4px solid #3b82f6;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <p style="color:#64748b; font-size:0.8rem; font-weight:500; margin:0;">تعبئات اليوم</p>
                <div style="width:36px; height:36px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
            <p style="color:#0f172a; font-size:1.8rem; font-weight:800; margin:0;">{{ $stats['refuels_today'] }}</p>
            <p style="color:#94a3b8; font-size:0.75rem; margin:4px 0 0;">تعبئة اليوم</p>
        </div>

        {{-- إيراد اليوم --}}
        <div class="stat-card" style="border-right:4px solid #a855f7;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <p style="color:#64748b; font-size:0.8rem; font-weight:500; margin:0;">إيراد اليوم</p>
                <div style="width:36px; height:36px; background:#faf5ff; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p style="color:#0f172a; font-size:1.8rem; font-weight:800; margin:0;">{{ number_format($stats['revenue_today'], 0) }}</p>
            <p style="color:#94a3b8; font-size:0.75rem; margin:4px 0 0;">ريال سعودي</p>
        </div>
    </div>

    {{-- Month stats row --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:24px;">
        <div class="stat-card" style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; background:linear-gradient(135deg,#f97316,#ea580c); border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 12px rgba(249,115,22,0.3);">
                <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.78rem; margin:0;">إيراد الشهر</p>
                <p style="color:#0f172a; font-size:1.4rem; font-weight:800; margin:0;">{{ number_format($stats['revenue_month'], 0) }} <span style="font-size:0.8rem; color:#94a3b8;">ريال</span></p>
            </div>
        </div>
        <div class="stat-card" style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; background:linear-gradient(135deg,#3b82f6,#1d4ed8); border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.78rem; margin:0;">تعبئات الشهر</p>
                <p style="color:#0f172a; font-size:1.4rem; font-weight:800; margin:0;">{{ number_format($stats['refuels_month']) }} <span style="font-size:0.8rem; color:#94a3b8;">تعبئة</span></p>
            </div>
        </div>
        <div class="stat-card" style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; background:linear-gradient(135deg,#22c55e,#15803d); border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 12px rgba(34,197,94,0.3);">
                <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.78rem; margin:0;">الموظفون النشطون</p>
                <p style="color:#0f172a; font-size:1.4rem; font-weight:800; margin:0;">{{ $stats['employees_count'] }} <span style="font-size:0.8rem; color:#94a3b8;">موظف</span></p>
            </div>
        </div>
    </div>

    {{-- Recent refuels table --}}
    <div class="glass-card" style="overflow:hidden;">
        <div style="padding:18px 20px; border-bottom:1px solid #f0f4ff; display:flex; align-items:center; justify-content:space-between;">
            <h2 style="font-size:0.95rem; font-weight:700; color:#0f172a; margin:0;">آخر التعبئات</h2>
            <a href="{{ route('owner.refuels') }}" style="font-size:0.8rem; color:#f97316; font-weight:600; text-decoration:none;">عرض الكل ←</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8faff;">
                        <th style="padding:10px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">العميل</th>
                        <th style="padding:10px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">المحطة</th>
                        <th style="padding:10px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">اللترات</th>
                        <th style="padding:10px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">المبلغ</th>
                        <th style="padding:10px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRefuels as $r)
                    <tr class="table-row" style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px;">
                            <p style="font-size:0.875rem; font-weight:600; color:#0f172a; margin:0;">{{ $r->user?->full_name ?? '-' }}</p>
                            <p style="font-size:0.75rem; color:#94a3b8; margin:0;">{{ $r->user?->phone ?? '' }}</p>
                        </td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#475569;">{{ $r->station?->station_name ?? '-' }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#0f172a;">{{ number_format($r->liters, 1) }} L</td>
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#15803d;">{{ number_format($r->final_price, 2) }} ر.س</td>
                        <td style="padding:12px 16px; font-size:0.78rem; color:#94a3b8;">{{ \Carbon\Carbon::parse($r->refuel_date)->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="padding:30px; text-align:center; color:#94a3b8;">لا توجد تعبئات حتى الآن</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
