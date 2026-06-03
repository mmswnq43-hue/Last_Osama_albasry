<div style="padding:20px 16px 10px;">

{{-- ═══ Header ═══ --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <h1 style="color:#f8fafc;font-size:1.25rem;font-weight:800;margin:0;">الإشعارات</h1>
        @if($unreadCount > 0)
            <span style="background:#ef4444;color:white;font-size:0.72rem;font-weight:800;padding:2px 8px;border-radius:999px;min-width:20px;text-align:center;">
                {{ $unreadCount }}
            </span>
        @endif
    </div>

    @if($unreadCount > 0)
    <button wire:click="markAllRead"
            style="background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.3);color:#fb923c;border-radius:10px;padding:8px 14px;font-size:0.8rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        تحديد الكل كمقروء
    </button>
    @endif
</div>

{{-- ═══ Notifications List ═══ --}}
@if($notifications->isEmpty())
    <div style="text-align:center;padding:60px 20px;">
        <div style="width:72px;height:72px;border-radius:24px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="32" height="32" fill="none" stroke="#475569" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p style="color:#64748b;font-size:0.95rem;margin:0;">لا توجد إشعارات</p>
        <p style="color:#475569;font-size:0.82rem;margin:6px 0 0;">ستظهر الإشعارات الجديدة هنا</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($notifications as $notif)
        @php
            $typeIcons = [
                'refuel'       => ['icon'=>'⛽','bg'=>'rgba(249,115,22,0.15)'],
                'subscription' => ['icon'=>'📋','bg'=>'rgba(59,130,246,0.15)'],
                'car_wash'     => ['icon'=>'🚗','bg'=>'rgba(34,197,94,0.15)'],
                'maintenance'  => ['icon'=>'🔧','bg'=>'rgba(168,85,247,0.15)'],
                'payment'      => ['icon'=>'💳','bg'=>'rgba(234,179,8,0.15)'],
                'system'       => ['icon'=>'⚙️','bg'=>'rgba(100,116,139,0.15)'],
                'alert'        => ['icon'=>'⚠️','bg'=>'rgba(239,68,68,0.15)'],
            ];
            $type   = $notif->notification_type ?? $notif->type ?? 'system';
            $info   = $typeIcons[$type] ?? ['icon'=>'🔔','bg'=>'rgba(100,116,139,0.15)'];
            $isNew  = !$notif->is_read;
        @endphp
        <div wire:click="markRead({{ $notif->id }})"
             style="background:{{ $isNew ? 'rgba(249,115,22,0.06)' : 'rgba(255,255,255,0.03)' }};border:1px solid {{ $isNew ? 'rgba(249,115,22,0.2)' : 'rgba(255,255,255,0.07)' }};border-radius:16px;padding:14px;display:flex;align-items:flex-start;gap:12px;cursor:pointer;transition:.2s;"
             onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='{{ $isNew ? 'rgba(249,115,22,0.06)' : 'rgba(255,255,255,0.03)' }}'">

            <div style="width:42px;height:42px;border-radius:14px;background:{{ $info['bg'] }};display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;position:relative;">
                {{ $info['icon'] }}
                @if($isNew)
                <div style="position:absolute;top:-2px;left:-2px;width:10px;height:10px;border-radius:50%;background:#f97316;border:2px solid #0a0f1e;"></div>
                @endif
            </div>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:4px;">
                    <p style="color:{{ $isNew ? '#f8fafc' : '#cbd5e1' }};font-weight:{{ $isNew ? '700' : '600' }};font-size:0.88rem;margin:0;line-height:1.4;">
                        {{ $notif->title }}
                    </p>
                    <span style="color:#475569;font-size:0.68rem;flex-shrink:0;padding-top:1px;">{{ $notif->created_at?->diffForHumans() }}</span>
                </div>
                @if(!empty($notif->message))
                    <p style="color:#64748b;font-size:0.78rem;margin:0;line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $notif->message }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div style="margin-top:20px;display:flex;justify-content:center;gap:8px;">
        @if($notifications->onFirstPage())
            <span style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#475569;padding:8px 16px;border-radius:10px;font-size:0.82rem;">السابق</span>
        @else
            <button wire:click="previousPage" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">السابق</button>
        @endif

        <span style="background:rgba(249,115,22,0.15);border:1px solid rgba(249,115,22,0.3);color:#fb923c;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:700;">
            {{ $notifications->currentPage() }} / {{ $notifications->lastPage() }}
        </span>

        @if($notifications->hasMorePages())
            <button wire:click="nextPage" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;">التالي</button>
        @else
            <span style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#475569;padding:8px 16px;border-radius:10px;font-size:0.82rem;">التالي</span>
        @endif
    </div>
    @endif
@endif

</div>
