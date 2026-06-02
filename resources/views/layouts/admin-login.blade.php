<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'تسجيل الدخول — غازي' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e2a4a 50%, #0f172a 100%);
            position: relative; overflow-y: auto; overflow-x: hidden;
            padding: 20px 0;
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
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div style="width:100%; max-width:460px; padding:16px; position:relative; z-index:1;">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
