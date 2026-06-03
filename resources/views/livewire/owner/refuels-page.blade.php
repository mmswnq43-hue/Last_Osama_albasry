<div>
    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
        <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d; font-weight:600; font-size:0.875rem;">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Summary bar --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:20px;">
        <div class="stat-card" style="display:flex; align-items:center; gap:12px; border-right:4px solid #3b82f6;">
            <div style="width:38px; height:38px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.72rem; margin:0;">تعبئات اليوم</p>
                <p style="color:#0f172a; font-size:1.3rem; font-weight:800; margin:0;">{{ $summaryToday['count'] }}</p>
            </div>
        </div>
        <div class="stat-card" style="display:flex; align-items:center; gap:12px; border-right:4px solid #22c55e;">
            <div style="width:38px; height:38px; background:#f0fdf4; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#22c55e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.72rem; margin:0;">لترات اليوم</p>
                <p style="color:#0f172a; font-size:1.3rem; font-weight:800; margin:0;">{{ number_format($summaryToday['liters'], 1) }}</p>
            </div>
        </div>
        <div class="stat-card" style="display:flex; align-items:center; gap:12px; border-right:4px solid #f97316;">
            <div style="width:38px; height:38px; background:#fff7ed; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p style="color:#64748b; font-size:0.72rem; margin:0;">إيراد اليوم</p>
                <p style="color:#0f172a; font-size:1.3rem; font-weight:800; margin:0;">{{ number_format($summaryToday['revenue'], 0) }} <span style="font-size:0.7rem; color:#94a3b8;">ر.س</span></p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card" style="padding:16px; margin-bottom:16px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:160px;">
                <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;">بحث</label>
                <div style="position:relative;">
                    <svg width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="position:absolute; right:10px; top:50%; transform:translateY(-50%);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live="search" type="text" class="input-field" placeholder="اسم أو جوال..." style="padding-right:32px;">
                </div>
            </div>
            <div style="min-width:130px;">
                <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;">من تاريخ</label>
                <input wire:model.live="dateFrom" type="date" class="input-field">
            </div>
            <div style="min-width:130px;">
                <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;">إلى تاريخ</label>
                <input wire:model.live="dateTo" type="date" class="input-field">
            </div>
            <div style="min-width:160px;">
                <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:5px;">المحطة</label>
                <select wire:model.live="stationFilter" class="input-field">
                    <option value="">كل المحطات</option>
                    @foreach($myStations as $st)
                    <option value="{{ $st->id }}">{{ $st->station_name }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="export" style="padding:9px 16px; background:#f0fdf4; color:#15803d; border:1.5px solid #86efac; border-radius:10px; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif; white-space:nowrap;">
                تصدير
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass-card" style="overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:650px;">
                <thead>
                    <tr style="background:#f8faff;">
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">التاريخ</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">العميل</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">المحطة</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">اللترات</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">السعر</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refuels as $r)
                    <tr class="table-row" style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px; font-size:0.78rem; color:#64748b;">{{ \Carbon\Carbon::parse($r->refuel_date)->format('Y/m/d H:i') }}</td>
                        <td style="padding:12px 16px;">
                            <p style="font-size:0.875rem; font-weight:600; color:#0f172a; margin:0;">{{ $r->user?->full_name ?? '-' }}</p>
                            <p style="font-size:0.72rem; color:#94a3b8; margin:0;">{{ $r->user?->phone ?? '' }}</p>
                        </td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#475569;">{{ $r->station?->station_name ?? '-' }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#0f172a;">{{ number_format($r->liters, 1) }} L</td>
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#15803d;">{{ number_format($r->final_price, 2) }} ر.س</td>
                        <td style="padding:12px 16px;">
                            @php
                                $statusMap = ['completed' => ['badge-green', 'مكتملة'], 'pending' => ['badge-yellow', 'معلقة'], 'cancelled' => ['badge-red', 'ملغاة']];
                                [$cls, $label] = $statusMap[$r->status] ?? ['badge-slate', $r->status];
                            @endphp
                            <span class="badge {{ $cls }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:36px; text-align:center; color:#94a3b8;">لا توجد تعبئات تطابق الفلتر</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:14px 16px;">{{ $refuels->links() }}</div>
    </div>
</div>
