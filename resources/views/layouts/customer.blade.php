<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'غازي' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        /* ── Auth mode body ──────────────────────────── */
        body.auth-mode {
            margin: 0; min-height: 100vh;
            display: flex; align-items: flex-start; justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e2a4a 50%, #0f172a 100%);
            position: relative; overflow-x: hidden; padding: 24px 0;
        }
        body.auth-mode::before {
            content: ''; position: fixed; top: -250px; right: -150px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(249,115,22,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        body.auth-mode::after {
            content: ''; position: fixed; bottom: -250px; left: -150px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── App mode body ──────────────────────────── */
        body.app-mode {
            margin: 0; padding: 0; overflow: hidden;
            background: #0a0f1e;
        }

        .grid-bg {
            position: fixed; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 44px 44px; pointer-events: none; z-index: 0;
        }

        /* ── Shared customer UI ──────────────────────────── */
        .c-card {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; backdrop-filter: blur(14px);
            box-shadow: 0 24px 60px rgba(0,0,0,0.45); padding: 24px;
        }
        .c-label { display:block; color:#cbd5e1; font-size:0.85rem; font-weight:600; margin-bottom:7px; }
        .c-input {
            width:100%; padding:11px 14px; border-radius:12px;
            background: rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.12);
            color:#f1f5f9; font-size:0.92rem; transition:.2s; outline:none;
            font-family: 'Tajawal', sans-serif;
        }
        .c-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,0.18); }
        .c-input::placeholder { color:#64748b; }
        .c-btn {
            width:100%; padding:13px; border:none; border-radius:12px; cursor:pointer;
            font-weight:700; font-size:0.95rem; color:#fff; transition:.2s;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow:0 10px 24px rgba(249,115,22,0.35);
            font-family: 'Tajawal', sans-serif;
        }
        .c-btn:hover { transform: translateY(-1px); box-shadow:0 14px 30px rgba(249,115,22,0.45); }
        .c-btn-ghost {
            background: rgba(255,255,255,0.06); color:#cbd5e1;
            border:1px solid rgba(255,255,255,0.12); box-shadow:none;
        }
        .c-btn-ghost:hover { background: rgba(255,255,255,0.1); transform:none; box-shadow:none; }
        .c-error {
            background: rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.35);
            color:#fca5a5; padding:11px 14px; border-radius:11px; font-size:0.85rem; margin-bottom:16px;
        }
        .c-success {
            background: rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.35);
            color:#86efac; padding:11px 14px; border-radius:11px; font-size:0.85rem; margin-bottom:16px;
        }
        .field-err { color:#fca5a5; font-size:0.78rem; margin-top:5px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Responsive grids ───────────────────────────── */
        .grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .grid-span-2 { grid-column: 1 / -1; }
        .flex-btns { display:flex; gap:12px; }

        /* ── App Shell ──────────────────────────────────── */
        #app-shell {
            position: relative; width: 100%; height: 100vh;
            display: flex; flex-direction: column;
            background: linear-gradient(160deg, #0a0f1e 0%, #0f172a 40%, #0a1628 100%);
        }

        /* Top bar */
        #top-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 40;
            height: 58px;
            background: rgba(10,15,30,0.92);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(16px);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 18px;
        }
        #top-bar .logo {
            display: flex; align-items: center; gap: 9px;
        }
        #top-bar .logo-icon {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(249,115,22,0.4);
        }
        #top-bar .logo-text {
            color: #f8fafc; font-size: 1.2rem; font-weight: 800;
        }
        #top-bar .user-info {
            display: flex; align-items: center; gap: 8px;
        }
        #top-bar .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, rgba(249,115,22,0.3), rgba(234,88,12,0.2));
            border: 1.5px solid rgba(249,115,22,0.5);
            display: flex; align-items: center; justify-content: center;
            color: #fb923c; font-size: 0.75rem; font-weight: 700;
        }
        #top-bar .user-name {
            color: #cbd5e1; font-size: 0.82rem; font-weight: 600;
        }

        /* Content area */
        #content-area {
            flex: 1;
            padding-top: 58px;
            padding-bottom: 70px;
            overflow-y: auto;
            overflow-x: hidden;
            height: 100vh;
            -webkit-overflow-scrolling: touch;
        }
        #content-area.fullscreen-map {
            padding-bottom: 0;
            overflow: hidden;
        }

        /* Bottom Navigation */
        #bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 40;
            height: 66px;
            background: rgba(10,15,30,0.96);
            border-top: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(20px);
            display: flex; align-items: stretch;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.4);
        }
        .nav-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 4px; cursor: pointer; text-decoration: none;
            border: none; background: transparent;
            padding: 8px 0 10px;
            position: relative; transition: .2s;
            -webkit-tap-highlight-color: transparent;
        }
        .nav-item .nav-icon {
            width: 22px; height: 22px;
            transition: transform .2s, color .2s;
        }
        .nav-item .nav-label {
            font-size: 0.62rem; font-weight: 600;
            color: #475569; transition: color .2s;
            font-family: 'Tajawal', sans-serif;
        }
        .nav-item.active .nav-icon { color: #f97316; transform: translateY(-1px); }
        .nav-item.active .nav-label { color: #f97316; }
        .nav-item:not(.active):hover .nav-label { color: #94a3b8; }
        .nav-item.active::after {
            content: ''; position: absolute; top: 0; left: 50%;
            transform: translateX(-50%);
            width: 28px; height: 2.5px; border-radius: 0 0 3px 3px;
            background: #f97316;
        }
        .nav-item .nav-badge {
            position: absolute; top: 6px; right: calc(50% - 18px);
            min-width: 16px; height: 16px; border-radius: 999px;
            background: #ef4444; color: white;
            font-size: 0.6rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px; border: 1.5px solid #0a0f1e;
        }

        @media (max-width: 600px) {
            body.auth-mode { padding: 16px 0; }
            .c-card { padding: 18px; border-radius: 16px; }
            .grid-2 { grid-template-columns: 1fr; }
            .grid-span-2 { grid-column: auto; }
            .flex-btns { flex-direction: column-reverse; }
            .flex-btns .c-btn-ghost { flex: none !important; }
        }
    </style>
</head>
<body class="{{ ($showNav ?? false) ? 'app-mode' : 'auth-mode' }}">

@if($showNav ?? false)
    {{-- ═══════════════ App Shell Mode ═══════════════ --}}
    <div id="app-shell">

        {{-- Top Bar --}}
        <div id="top-bar">
            <div class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text">غازي</span>
            </div>
            @auth
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->full_name ?? 'المستخدم' }}</span>
                <div class="user-avatar">
                    {{ mb_substr(auth()->user()->full_name ?? 'م', 0, 1) }}
                </div>
            </div>
            @endauth
        </div>

        {{-- Content --}}
        <div id="content-area" class="{{ ($fullscreen ?? false) ? 'fullscreen-map' : '' }}">
            {{ $slot }}
        </div>

        {{-- Bottom Navigation --}}
        <nav id="bottom-nav">
            <a href="{{ route('customer.dashboard') }}" class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" style="color: {{ request()->routeIs('customer.dashboard') ? '#f97316' : '#475569' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-label">الرئيسية</span>
            </a>
            <a href="{{ route('customer.subscriptions') }}" class="nav-item {{ request()->routeIs('customer.subscriptions') ? 'active' : '' }}">
                <svg class="nav-icon" style="color: {{ request()->routeIs('customer.subscriptions') ? '#f97316' : '#475569' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="nav-label">اشتراكاتي</span>
            </a>
            <a href="{{ route('customer.map') }}" class="nav-item {{ request()->routeIs('customer.map') ? 'active' : '' }}">
                <svg class="nav-icon" style="color: {{ request()->routeIs('customer.map') ? '#f97316' : '#475569' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="nav-label">الخريطة</span>
            </a>
            <a href="{{ route('customer.notifications') }}" class="nav-item {{ request()->routeIs('customer.notifications') ? 'active' : '' }}">
                <svg class="nav-icon" style="color: {{ request()->routeIs('customer.notifications') ? '#f97316' : '#475569' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="nav-label">الإشعارات</span>
            </a>
            <a href="{{ route('customer.settings') }}" class="nav-item {{ request()->routeIs('customer.settings') ? 'active' : '' }}">
                <svg class="nav-icon" style="color: {{ request()->routeIs('customer.settings') ? '#f97316' : '#475569' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-label">الإعدادات</span>
            </a>
        </nav>

    </div>

@else
    {{-- ═══════════════ Auth Mode ═══════════════ --}}
    <div class="grid-bg"></div>
    <div style="width:100%; max-width:760px; padding:0 16px; position:relative; z-index:1;">
        <div style="text-align:center; margin-bottom:22px;">
            <div style="display:inline-flex; align-items:center; gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(249,115,22,0.4);">
                    <svg width="24" height="24" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span style="color:#f8fafc;font-size:1.5rem;font-weight:800;">غازي</span>
            </div>
        </div>
        {{ $slot }}
    </div>
@endif

@livewireScripts
</body>
</html>
