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
        body {
            margin: 0; min-height: 100vh;
            display: flex; align-items: flex-start; justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e2a4a 50%, #0f172a 100%);
            position: relative; overflow-x: hidden; padding: 24px 0;
        }
        body::before {
            content: ''; position: fixed; top: -250px; right: -150px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(249,115,22,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: ''; position: fixed; bottom: -250px; left: -150px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .grid-bg {
            position: fixed; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 44px 44px; pointer-events: none;
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

        @media (max-width: 600px) {
            body { padding: 16px 0; }
            .c-card { padding: 18px; border-radius: 16px; }
            .grid-2 { grid-template-columns: 1fr; }
            .grid-span-2 { grid-column: auto; }
            .flex-btns { flex-direction: column-reverse; }
            .flex-btns .c-btn-ghost { flex: none !important; }
        }
    </style>
</head>
<body>
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
    @livewireScripts
</body>
</html>
