<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إرفاق السند - غازي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family:'Tajawal',sans-serif; box-sizing:border-box; }
        [x-cloak]{display:none!important;}
        body { margin:0;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;
            background:linear-gradient(135deg,#0f172a 0%,#1e2a4a 50%,#0f172a 100%);
            position:relative;overflow-x:hidden;padding:24px 0; }
        body::before { content:'';position:fixed;top:-250px;right:-150px;width:600px;height:600px;
            background:radial-gradient(circle,rgba(249,115,22,0.18) 0%,transparent 70%);pointer-events:none; }
        body::after { content:'';position:fixed;bottom:-250px;left:-150px;width:600px;height:600px;
            background:radial-gradient(circle,rgba(59,130,246,0.15) 0%,transparent 70%);pointer-events:none; }
        .c-card { background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);
            border-radius:20px;backdrop-filter:blur(14px);box-shadow:0 24px 60px rgba(0,0,0,0.45);padding:24px; }
        .c-label { display:block;color:#cbd5e1;font-size:0.85rem;font-weight:600;margin-bottom:7px; }
        .c-btn { width:100%;padding:13px;border:none;border-radius:12px;cursor:pointer;
            font-weight:700;font-size:0.95rem;color:#fff;transition:.2s;
            background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);
            box-shadow:0 10px 24px rgba(249,115,22,0.35);font-family:'Tajawal',sans-serif; }
        .c-btn-ghost { background:rgba(255,255,255,0.06);color:#cbd5e1;
            border:1px solid rgba(255,255,255,0.12);box-shadow:none;text-decoration:none;
            display:flex;align-items:center;justify-content:center; }
        .field-err { color:#fca5a5;font-size:0.8rem;margin-top:8px; }
        .flex-btns { display:flex;gap:12px; }
        @keyframes spin { to{transform:rotate(360deg);} }
        @media(max-width:600px){.flex-btns{flex-direction:column-reverse;}}
    </style>
</head>
<body>
<div style="width:100%;max-width:600px;padding:0 16px;position:relative;z-index:1;">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:22px;">
        <div style="display:inline-flex;align-items:center;gap:10px;">
            <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(249,115,22,0.4);">
                <svg width="24" height="24" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span style="color:#f8fafc;font-size:1.5rem;font-weight:800;">غازي</span>
        </div>
    </div>

    <div class="c-card">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:22px;">
            <h2 style="color:#f8fafc;font-size:1.2rem;font-weight:800;margin:0 0 6px;">إرفاق سند التحويل</h2>
            @if($plan)
            <p style="color:#94a3b8;font-size:0.84rem;margin:0;">
                باقة <span style="color:#fb923c;font-weight:700;">
                    @switch($req['plan'])
                        @case('monthly')شهرية@break
                        @case('3months')3 أشهر@break
                        @case('6months')6 أشهر@break
                        @case('yearly')سنوية@break
                    @endswitch
                </span>
                — <span style="color:#fb923c;font-weight:700;">${{ rtrim(rtrim(number_format($plan['price'],2),'0'),'.') }}</span>
                @if(isset($req['type']))
                    @if($req['type']==='replace') — <span style="color:#f97316;font-size:0.8rem;">استبدال فوري</span>
                    @elseif($req['type']==='after') — <span style="color:#93c5fd;font-size:0.8rem;">بعد انتهاء الحالي</span>
                    @endif
                @endif
            </p>
            @endif
        </div>

        {{-- Success/Error messages --}}
        @if(session('success'))
            <div style="background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.35);color:#86efac;padding:11px 14px;border-radius:11px;font-size:0.85rem;margin-bottom:16px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#fca5a5;padding:11px 14px;border-radius:11px;font-size:0.85rem;margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        {{-- Bank accounts --}}
        @if($bankAccounts->isEmpty())
            <div style="background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);color:#fde047;padding:14px;border-radius:12px;font-size:0.84rem;margin-bottom:16px;">
                لا توجد حسابات بنكية متاحة حالياً. يرجى التواصل مع الإدارة.
            </div>
        @else
            <p style="color:#94a3b8;font-size:0.84rem;margin:0 0 12px;">حوّل المبلغ إلى أحد الحسابات التالية ثم أرفق السند:</p>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
                @foreach($bankAccounts as $acc)
                    <div style="background:rgba(15,23,42,0.5);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;flex-wrap:wrap;gap:6px;">
                            <span style="color:#f8fafc;font-weight:800;">{{ $acc->bank_name }}</span>
                            <span style="font-size:0.7rem;background:rgba(59,130,246,0.18);color:#93c5fd;padding:2px 9px;border-radius:999px;">{{ $acc->currency }}</span>
                        </div>
                        <p style="color:#cbd5e1;font-size:0.82rem;margin:0 0 6px;">صاحب الحساب: {{ $acc->account_name }}</p>
                        <div x-data="{copied:false}"
                             style="display:flex;align-items:center;gap:8px;background:rgba(0,0,0,0.3);border-radius:10px;padding:9px 12px;flex-wrap:wrap;">
                            <span dir="ltr" style="color:#fb923c;font-weight:800;font-size:1rem;flex:1;min-width:0;word-break:break-all;">{{ $acc->account_number }}</span>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $acc->account_number }}').then(()=>{copied=true;setTimeout(()=>copied=false,2000)})"
                                    style="background:rgba(249,115,22,0.2);border:1px solid rgba(249,115,22,0.5);color:#fb923c;border-radius:8px;padding:5px 12px;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;white-space:nowrap;">
                                <span x-show="!copied">📋 نسخ</span>
                                <span x-show="copied" x-cloak style="color:#86efac;">✓ تم</span>
                            </button>
                        </div>
                        @if($acc->iban)<p style="color:#94a3b8;font-size:0.76rem;margin:6px 0 0;" dir="ltr">IBAN: {{ $acc->iban }}</p>@endif
                        @if($acc->notes)<p style="color:#94a3b8;font-size:0.76rem;margin:4px 0 0;">{{ $acc->notes }}</p>@endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Upload form --}}
        <div x-data="{
                file:null,fileName:'',fileSize:'',fileError:'',submitting:false,
                handleFile(e){
                    const f=e.target.files[0];this.fileError='';
                    if(!f)return;
                    const ok=['image/jpeg','image/jpg','image/png','image/webp','application/pdf'];
                    if(!ok.includes(f.type)){this.fileError='الملف يجب أن يكون صورة أو PDF';e.target.value='';this.file=null;return;}
                    if(f.size>5*1024*1024){this.fileError='الحجم يتجاوز 5MB';e.target.value='';this.file=null;return;}
                    this.file=f;this.fileName=f.name;this.fileSize=(f.size/1024).toFixed(0)+' KB';
                },
                doSubmit(){
                    if(!this.file){this.fileError='يرجى اختيار ملف السند أولاً';return;}
                    this.fileError='';this.submitting=true;this.$refs.form.submit();
                }
            }">

            <form method="POST" action="{{ route('customer.subscription.store') }}"
                  enctype="multipart/form-data" x-ref="form">
                @csrf

                <label class="c-label">سند التحويل البنكي *</label>

                <label for="receipt-file"
                       :style="file?'border-color:rgba(34,197,94,0.5);background:rgba(34,197,94,0.06);':''"
                       style="display:block;border:2px dashed rgba(255,255,255,0.2);border-radius:14px;padding:22px;text-align:center;cursor:pointer;transition:.3s;">
                    <input type="file" name="receipt" id="receipt-file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                           style="display:none;" @change="handleFile($event)">
                    <template x-if="!file">
                        <div>
                            <svg width="34" height="34" fill="none" stroke="#64748b" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <p style="color:#cbd5e1;font-weight:700;margin:0 0 4px;">اضغط لاختيار الملف</p>
                            <p style="color:#64748b;font-size:0.76rem;margin:0;">JPG, PNG, WEBP أو PDF — بحد أقصى 5MB</p>
                        </div>
                    </template>
                    <template x-if="file">
                        <div>
                            <svg width="30" height="30" fill="none" stroke="#86efac" viewBox="0 0 24 24" style="margin:0 auto 6px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p style="color:#86efac;font-weight:700;margin:0 0 2px;">✓ <span x-text="fileName"></span></p>
                            <p style="color:#64748b;font-size:0.76rem;margin:0;"><span x-text="fileSize"></span></p>
                        </div>
                    </template>
                </label>

                <p x-show="fileError" x-text="fileError" class="field-err" style="display:none;" x-cloak></p>
                @error('receipt')<p class="field-err">{{ $message }}</p>@enderror

                <div class="flex-btns" style="margin-top:22px;">
                    <a href="{{ route('customer.status') }}" class="c-btn c-btn-ghost" style="flex:0 0 120px;">→ رجوع</a>
                    <button type="button" @click="doSubmit()" class="c-btn" style="flex:1;"
                            :disabled="submitting" :style="submitting?'opacity:0.7;cursor:wait;':''">
                        <span x-show="!submitting">إرسال الطلب ✓</span>
                        <span x-show="submitting" x-cloak style="display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            جارٍ الإرسال...
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@vite(['resources/js/app.js'])
</body>
</html>
