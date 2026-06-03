<div>
    {{-- Period selector --}}
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:24px; flex-wrap:wrap;">
        <span style="font-size:0.85rem; font-weight:600; color:#374151;">عرض البيانات:</span>
        <button wire:click="$set('period','monthly')"
                style="padding:7px 16px; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif; transition:all 0.2s; border:none;
                       {{ $period === 'monthly' ? 'background:linear-gradient(135deg,#f97316,#ea580c); color:white; box-shadow:0 4px 12px rgba(249,115,22,0.3);' : 'background:#f1f5f9; color:#64748b;' }}">
            شهري
        </button>
        <button wire:click="$set('period','weekly')"
                style="padding:7px 16px; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'Tajawal',sans-serif; transition:all 0.2s; border:none;
                       {{ $period === 'weekly' ? 'background:linear-gradient(135deg,#f97316,#ea580c); color:white; box-shadow:0 4px 12px rgba(249,115,22,0.3);' : 'background:#f1f5f9; color:#64748b;' }}">
            أسبوعي
        </button>
    </div>

    {{-- Chart --}}
    <div class="glass-card" style="padding:24px; margin-bottom:24px;">
        <h2 style="font-size:0.95rem; font-weight:700; color:#0f172a; margin:0 0 20px;">الإيراد خلال آخر 6 أشهر</h2>
        <canvas id="revenueChart" style="max-height:300px;"></canvas>
    </div>

    {{-- Per-station stats table --}}
    <div class="glass-card" style="overflow:hidden;">
        <div style="padding:18px 20px; border-bottom:1px solid #f0f4ff;">
            <h2 style="font-size:0.95rem; font-weight:700; color:#0f172a; margin:0;">إحصائيات المحطات</h2>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:600px;">
                <thead>
                    <tr style="background:#f8faff;">
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">اسم المحطة</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">إجمالي التعبئات</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">إجمالي اللترات</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">إجمالي الإيراد</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">متوسط / يوم</th>
                        <th style="padding:12px 16px; text-align:right; font-size:0.78rem; color:#64748b; font-weight:600;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stationStats as $st)
                    <tr class="table-row" style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#0f172a;">{{ $st['name'] }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#0f172a;">{{ number_format($st['total_refuels']) }}</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#0f172a;">{{ number_format($st['total_liters'], 1) }} L</td>
                        <td style="padding:12px 16px; font-size:0.875rem; font-weight:600; color:#15803d;">{{ number_format($st['total_revenue'], 0) }} ر.س</td>
                        <td style="padding:12px 16px; font-size:0.875rem; color:#475569;">{{ number_format($st['avg_per_day'], 0) }} ر.س</td>
                        <td style="padding:12px 16px;">
                            <span class="badge {{ $st['is_active'] ? 'badge-green' : 'badge-slate' }}">{{ $st['is_active'] ? 'مفعّلة' : 'غير مفعّلة' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="padding:36px; text-align:center; color:#94a3b8;">لا توجد محطات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const chartData = @json($chartData);
    const labels = chartData.map(d => d.label);
    const revenues = chartData.map(d => d.revenue);

    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'الإيراد (ريال)',
                data: revenues,
                backgroundColor: 'rgba(249, 115, 22, 0.7)',
                borderColor: '#f97316',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y.toLocaleString('ar-SA') + ' ريال'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => val.toLocaleString('ar-SA')
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
