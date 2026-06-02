<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — غازي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family:'Tajawal',sans-serif; box-sizing:border-box; }
        body { margin:0; background:#f0f4ff; min-height:100vh; display:flex; align-items:flex-start; justify-content:center; padding:32px 16px; }
        .form-card { background:white; border-radius:20px; box-shadow:0 4px 24px rgba(0,0,0,0.08); border:1px solid #f1f5f9; width:100%; max-width:640px; overflow:hidden; }
        .form-header { background:linear-gradient(135deg,#f97316,#ea580c); padding:20px 28px; }
        .form-body { padding:28px; }
        .f-label { display:block; font-size:0.82rem; font-weight:700; color:#374151; margin-bottom:7px; }
        .f-input { width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:0.9rem; font-family:'Tajawal',sans-serif; outline:none; transition:.2s; background:white; color:#1e293b; }
        .f-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
        .f-error { color:#dc2626; font-size:0.76rem; margin-top:5px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .btn-primary { background:linear-gradient(135deg,#f97316,#ea580c); color:white; border:none; border-radius:10px; padding:12px 28px; font-size:0.95rem; font-weight:700; cursor:pointer; font-family:'Tajawal',sans-serif; }
        @media(max-width:600px) { .grid-2{grid-template-columns:1fr;} }
    </style>
</head>
<body>
<div class="form-card">

    {{-- Header --}}
    <div class="form-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.ads.index') }}"
               style="background:rgba(255,255,255,0.2);color:white;border:none;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;text-decoration:none;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 style="color:white;font-size:1.1rem;font-weight:800;margin:0;">{{ $title }}</h2>
                <p style="color:rgba(255,255,255,0.75);font-size:0.78rem;margin:3px 0 0;">إدارة الإعلانات / {{ $title }}</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="form-body">
        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:18px;color:#15803d;font-weight:600;font-size:0.875rem;">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:18px;color:#dc2626;font-weight:600;font-size:0.875rem;">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:18px;">
            <ul style="margin:0;padding-right:16px;color:#dc2626;font-size:0.82rem;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if(isset($method)) @method($method) @endif

            <div style="display:flex;flex-direction:column;gap:18px;">

                {{-- Title --}}
                <div>
                    <label class="f-label">عنوان الإعلان *</label>
                    <input type="text" name="title" class="f-input" value="{{ old('title', $ad->title ?? '') }}" placeholder="مثال: عروض الصيف في غازي">
                    @error('title') <p class="f-error">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="f-label">وصف الإعلان (اختياري)</label>
                    <textarea name="description" class="f-input" rows="3" placeholder="نص مختصر يظهر أسفل العنوان...">{{ old('description', $ad->description ?? '') }}</textarea>
                    @error('description') <p class="f-error">{{ $message }}</p> @enderror
                </div>

                {{-- Image upload --}}
                <div x-data="{
                        preview: @if(isset($ad) && $ad->image_url) '{{ $ad->image_url }}' @else null @endif,
                        handleFile(e) {
                            const f = e.target.files[0];
                            if (f) this.preview = URL.createObjectURL(f);
                        }
                    }">
                    <label class="f-label">صورة الإعلان (JPG/PNG/WEBP/GIF — بحد أقصى 5MB)</label>

                    {{-- Image preview --}}
                    <div x-show="preview" style="margin-bottom:10px;border-radius:12px;overflow:hidden;max-height:180px;">
                        <img :src="preview" style="width:100%;height:180px;object-fit:cover;" alt="preview">
                    </div>

                    <label style="display:block;border:2px dashed #e2e8f0;border-radius:12px;padding:16px;text-align:center;cursor:pointer;transition:.2s;background:#fafafa;"
                           onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif"
                               style="display:none;" @change="handleFile($event)">
                        <svg width="28" height="28" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin:0 auto 6px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <p style="color:#64748b;font-size:0.82rem;margin:0;">اضغط لرفع صورة</p>
                    </label>
                    @error('image') <p class="f-error">{{ $message }}</p> @enderror
                </div>

                {{-- Link URL --}}
                <div>
                    <label class="f-label">رابط عند النقر (اختياري)</label>
                    <input type="url" name="link_url" class="f-input" dir="ltr"
                           value="{{ old('link_url', $ad->link_url ?? '') }}" placeholder="https://example.com">
                    @error('link_url') <p class="f-error">{{ $message }}</p> @enderror
                </div>

                {{-- Dates + Order --}}
                <div class="grid-2">
                    <div>
                        <label class="f-label">تاريخ البداية (اختياري)</label>
                        <input type="date" name="start_date" class="f-input"
                               value="{{ old('start_date', isset($ad) ? $ad->start_date?->format('Y-m-d') : '') }}">
                        @error('start_date') <p class="f-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="f-label">تاريخ الانتهاء (اختياري)</label>
                        <input type="date" name="end_date" class="f-input"
                               value="{{ old('end_date', isset($ad) ? $ad->end_date?->format('Y-m-d') : '') }}">
                        @error('end_date') <p class="f-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="f-label">الترتيب (0 = أول)</label>
                        <input type="number" name="sort_order" class="f-input" min="0" max="255"
                               value="{{ old('sort_order', $ad->sort_order ?? 0) }}">
                    </div>
                    <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $ad->is_active ?? true) ? 'checked' : '' }}
                                   style="width:18px;height:18px;accent-color:#f97316;cursor:pointer;">
                            <span style="font-size:0.85rem;color:#374151;font-weight:600;">إعلان نشط</span>
                        </label>
                    </div>
                </div>

                {{-- Buttons --}}
                <div style="display:flex;gap:12px;margin-top:6px;">
                    <button type="submit" class="btn-primary">
                        {{ isset($ad) ? 'حفظ التعديلات' : 'إضافة الإعلان' }}
                    </button>
                    <a href="{{ route('admin.ads.index') }}"
                       style="padding:12px 22px;border:1.5px solid #e2e8f0;border-radius:10px;color:#475569;font-weight:600;font-size:0.9rem;text-decoration:none;display:flex;align-items:center;">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
