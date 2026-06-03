<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'لوحة تحكم غازي' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4ff; margin: 0; }

        /* ── Navigation ─────────────────────────────── */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 10px;
            color: #94a3b8; font-size: 0.875rem; font-weight: 500;
            transition: all 0.2s; text-decoration: none;
        }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-item.active {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #fff; box-shadow: 0 4px 12px rgba(249,115,22,0.4);
        }
        .nav-item .icon { width: 18px; height: 18px; flex-shrink: 0; }
        .nav-section { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;
            color: #475569; text-transform: uppercase; padding: 0 14px; margin: 18px 0 6px; }

        /* ── Reusable UI ────────────────────────────── */
        .stat-card { background: white; border-radius: 16px; padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            border: 1px solid rgba(255,255,255,0.8); }
        .glass-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.6); border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c);
            color: white; padding: 10px 20px; border-radius: 10px; font-weight: 600;
            font-size: 0.875rem; transition: all 0.2s; border: none; cursor: pointer;
            box-shadow: 0 4px 12px rgba(249,115,22,0.3); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(249,115,22,0.4); }
        .btn-primary:active { transform: translateY(0); }
        .btn-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white; padding: 10px 20px; border-radius: 10px; font-weight: 600;
            font-size: 0.875rem; transition: all 0.2s; border: none; cursor: pointer;
            box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
        .btn-blue:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px;
            border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-orange { background: #fff7ed; color: #ea580c; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-slate { background: #f1f5f9; color: #475569; }
        .input-field { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 9px 14px;
            font-size: 0.875rem; transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Tajawal', sans-serif; outline: none; background: white; width: 100%; }
        .input-field:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px); z-index: 60; display: flex;
            align-items: center; justify-content: center; padding: 16px; }
        .modal-box { background: white; border-radius: 20px; width: 100%; max-width: 520px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2); overflow: hidden;
            max-height: 90vh; overflow-y: auto; }
        .table-row:hover { background: #f8faff; }
        [wire\:loading] { opacity: 0.7; cursor: wait; }

        /* ── Sidebar ────────────────────────────────── */
        .admin-sidebar {
            width: 260px; background: linear-gradient(180deg, #0f172a 0%, #1e2a4a 50%, #0f172a 100%);
            position: fixed; top: 0; bottom: 0; right: 0; z-index: 50;
            display: flex; flex-direction: column; overflow: hidden;
            box-shadow: -4px 0 24px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .admin-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 45; transition: opacity 0.3s;
        }
        .admin-main { margin-right: 260px; transition: margin 0.3s; }
        .admin-topbar { height: 64px; padding: 0 20px; }
        .admin-content { padding: 20px; }
        .hamburger { display: none; }

        /* ── Mobile ≤768px ──────────────────────────── */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
            .admin-overlay.open {
                display: block;
            }
            .admin-main {
                margin-right: 0 !important;
            }
            .admin-topbar {
                height: 56px; padding: 0 14px;
            }
            .admin-content {
                padding: 14px;
            }
            .hamburger {
                display: flex; align-items: center; justify-content: center;
                width: 40px; height: 40px; border: none; background: #f0f4ff;
                border-radius: 10px; cursor: pointer; flex-shrink: 0;
            }
            .modal-overlay { padding: 10px; }
            .modal-box { border-radius: 16px; }
            .stat-card { padding: 16px; border-radius: 14px; }
            .glass-card { border-radius: 14px; }
        }
    </style>
</head>
<body>

<div x-data="{ sidebarOpen: false }" style="display:flex; min-height:100vh;">

    {{-- ===== OVERLAY (mobile) ===== --}}
    <div class="admin-overlay" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="admin-sidebar" :class="{ 'open': sidebarOpen }">

        {{-- Close button (mobile) --}}
        <button @click="sidebarOpen = false"
                style="display:none; position:absolute; top:14px; left:14px; background:rgba(255,255,255,0.1); border:none; color:#94a3b8; width:34px; height:34px; border-radius:10px; cursor:pointer; align-items:center; justify-content:center; z-index:5;"
                class="sidebar-close">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Logo --}}
        <div style="padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.07);">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; background: linear-gradient(135deg, #f97316, #3b82f6); border-radius:12px; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(249,115,22,0.4); flex-shrink:0;">
                    <svg width="22" height="22" fill="white" viewBox="0 0 24 24">
                        <path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77z"/>
                    </svg>
                </div>
                <div>
                    <p style="color:white; font-weight:800; font-size:1.1rem; line-height:1.2; margin:0;">غـازي</p>
                    <p style="color:#64748b; font-size:0.72rem; font-weight:500; margin:0;">لوحة الإدارة</p>
                </div>
            </div>
        </div>

        {{-- Admin Profile --}}
        <div style="padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.07);">
            <div style="display:flex; align-items:center; gap:10px; background:rgba(255,255,255,0.05); padding:10px 12px; border-radius:12px;">
                <div style="width:36px; height:36px; background: linear-gradient(135deg, #f97316, #3b82f6); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:0.9rem; flex-shrink:0;">
                    {{ mb_substr(auth()->user()->full_name ?? 'A', 0, 1) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="color:white; font-size:0.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0;">{{ auth()->user()->full_name ?? 'Admin' }}</p>
                    <p style="color:#f97316; font-size:0.68rem; font-weight:500; margin:0;">مدير النظام</p>
                </div>
                <div style="width:8px; height:8px; background:#22c55e; border-radius:999px; border:2px solid #0f172a;"></div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav style="flex:1; min-height:0; overflow-y:auto; padding: 12px 12px; scrollbar-width:thin; scrollbar-color:#334155 transparent;">
            <p class="nav-section">الرئيسية</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                لوحة التحكم
            </a>

            <p class="nav-section">المستخدمون</p>
            <a href="{{ route('admin.users.pending') }}" class="nav-item {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                طلبات الانضمام
                @php $pendingCount = \App\Models\User::where('approval_status','pending')->count(); @endphp
                @if($pendingCount > 0)
                <span style="margin-right:auto; background:#f97316; color:white; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:999px; box-shadow:0 2px 6px rgba(249,115,22,0.4);">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                إدارة العملاء
            </a>
            <a href="{{ route('admin.owners.index') }}" class="nav-item {{ request()->routeIs('admin.owners.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                إدارة الملاك
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="nav-item {{ request()->routeIs('admin.subscriptions.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                الاشتراكات
            </a>
            <a href="{{ route('admin.bankaccounts.index') }}" class="nav-item {{ request()->routeIs('admin.bankaccounts.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 10l9-6 9 6M4 10v10h16V10M9 21v-6h6v6"/></svg>
                الحسابات البنكية
            </a>
            <a href="{{ route('admin.ads.index') }}" class="nav-item {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                الإعلانات
            </a>

            <p class="nav-section">الأعمال</p>
            <a href="{{ route('admin.stations.index') }}" class="nav-item {{ request()->routeIs('admin.stations.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                محطات الوقود
            </a>
            <a href="{{ route('admin.carwashes.index') }}" class="nav-item {{ request()->routeIs('admin.carwashes.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                مغاسل السيارات
            </a>
            <a href="{{ route('admin.maintenance.index') }}" class="nav-item {{ request()->routeIs('admin.maintenance.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                مراكز الصيانة
            </a>

            <p class="nav-section">النظام</p>
            <a href="{{ route('admin.security.logs') }}" class="nav-item {{ request()->routeIs('admin.security.logs') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                سجلات الأمان
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="nav-item {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                تذاكر الدعم
            </a>
            <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                الإشعارات
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" @click="sidebarOpen = false">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                إعدادات النظام
            </a>
        </nav>

        {{-- Logout --}}
        <div style="padding: 12px; border-top: 1px solid rgba(255,255,255,0.07);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="width:100%; border:none; background:none; cursor:pointer; color:#ef4444; text-align:right; font-family:'Tajawal',sans-serif;">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN ===== --}}
    <main class="admin-main" style="flex:1; display:flex; flex-direction:column; min-height:100vh;">

        {{-- Topbar --}}
        <header class="admin-topbar" style="background:white; border-bottom:1px solid #e8edf5; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex; align-items:center; gap:10px;">
                {{-- Hamburger (mobile only) --}}
                <button class="hamburger" @click="sidebarOpen = true">
                    <svg width="22" height="22" fill="none" stroke="#0f172a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; color:#94a3b8;">
                    <span>غازي</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </div>
                <h1 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $title ?? 'لوحة التحكم' }}</h1>
            </div>
            <div style="display:flex; align-items:center; gap:14px; flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:6px; background:#f0f4ff; border-radius:8px; padding:6px 12px;">
                    <svg width="14" height="14" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span style="font-size:0.78rem; color:#3b82f6; font-weight:600;">{{ now()->format('d / m / Y') }}</span>
                </div>
            </div>
        </header>

        <div class="admin-content" style="flex:1;">
            {{-- Session Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px; box-shadow:0 4px 12px rgba(22,163,74,0.1);">
                <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span style="color:#15803d; font-weight:600; font-size:0.875rem;">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                 style="background:#fef2f2; border:1.5px solid #fca5a5; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:10px; box-shadow:0 4px 12px rgba(220,38,38,0.1);">
                <svg width="18" height="18" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span style="color:#dc2626; font-weight:600; font-size:0.875rem;">{{ session('error') }}</span>
            </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

@livewireScripts

<style>
    /* Show close button on mobile */
    @media (max-width: 768px) {
        .sidebar-close { display: flex !important; }
    }
</style>
</body>
</html>
