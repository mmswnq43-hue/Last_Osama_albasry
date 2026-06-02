<div>
    {{-- Step indicator --}}
    @php $steps = [1 => 'البيانات', 2 => 'الباقة', 3 => 'الدفع']; @endphp
    <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:22px;">
        @foreach($steps as $n => $label)
            <div style="display:flex;align-items:center;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                    <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.9rem;
                        @if($step >= $n) background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;box-shadow:0 6px 16px rgba(249,115,22,0.4);
                        @else background:rgba(255,255,255,0.06);color:#64748b;border:1px solid rgba(255,255,255,0.12); @endif">
                        @if($step > $n) ✓ @else {{ $n }} @endif
                    </div>
                    <span style="font-size:0.72rem;font-weight:600;@if($step >= $n)color:#f8fafc;@else color:#64748b;@endif">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <div style="width:40px;height:2px;margin:0 4px 18px;border-radius:2px;@if($step > $n)background:#f97316;@else background:rgba(255,255,255,0.12);@endif"></div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="c-card">
        @if($error)
            <div class="c-error">{{ $error }}</div>
        @endif

        {{-- ═══════════════ STEP 1: Personal data ═══════════════ --}}
        @if($step === 1)
            <h2 style="color:#f8fafc;font-size:1.2rem;font-weight:800;margin:0 0 4px;">إنشاء حساب جديد</h2>
            <p style="color:#94a3b8;font-size:0.84rem;margin:0 0 20px;">أدخل بياناتك الشخصية للبدء</p>

            <form wire:submit="nextFromData">
                <div class="grid-2">
                    <div class="grid-span-2">
                        <label class="c-label">الاسم الكامل *</label>
                        <input type="text" wire:model="full_name" class="c-input" placeholder="الاسم الثلاثي" autocomplete="name">
                        @error('full_name') <div class="field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="c-label">رقم الجوال *</label>
                        <input type="tel" wire:model="phone" class="c-input" placeholder="7XXXXXXXX" dir="ltr" autocomplete="tel">
                        @error('phone') <div class="field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="c-label">البريد الإلكتروني (اختياري)</label>
                        <input type="email" wire:model="email" class="c-input" placeholder="example@mail.com" dir="ltr" autocomplete="email">
                        @error('email') <div class="field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="c-label">نوع المركبة (اختياري)</label>
                        <input type="text" wire:model="vehicle_type" class="c-input" placeholder="مثال: تويوتا" autocomplete="off">
                    </div>
                    <div>
                        <label class="c-label">رقم المحرك (اختياري)</label>
                        <input type="text" wire:model="engine_number" class="c-input" placeholder="رقم المحرك" dir="ltr" autocomplete="off">
                    </div>
                    <div>
                        <label class="c-label">كلمة المرور *</label>
                        <input type="password" wire:model="password" class="c-input" placeholder="••••••" autocomplete="new-password">
                        @error('password') <div class="field-err">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="c-label">تأكيد كلمة المرور *</label>
                        <input type="password" wire:model="password_confirmation" class="c-input" placeholder="••••••" autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="c-btn" style="margin-top:22px;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="nextFromData">التالي ←</span>
                    <span wire:loading wire:target="nextFromData">جارٍ التحقق...</span>
                </button>
            </form>

            <p style="text-align:center;color:#94a3b8;font-size:0.84rem;margin-top:16px;">
                لديك حساب بالفعل؟
                <a href="{{ route('login') }}" wire:navigate style="color:#fb923c;font-weight:700;text-decoration:none;">تسجيل الدخول</a>
            </p>
        @endif

        {{-- ═══════════════ STEP 2: Subscription plans ═══════════════ --}}
        @if($step === 2)
            <h2 style="color:#f8fafc;font-size:1.2rem;font-weight:800;margin:0 0 4px;">اختر باقة الاشتراك</h2>
            <p style="color:#94a3b8;font-size:0.84rem;margin:0 0 20px;">اختر الباقة التي تناسبك</p>

            @error('selectedPlan') <div class="c-error">{{ $message }}</div> @enderror

            <div class="grid-2">
                @foreach($planList as $key => $plan)
                    <div wire:click="selectPlan('{{ $key }}')"
                         style="cursor:pointer;border-radius:14px;padding:16px;transition:.2s;position:relative;
                         @if($selectedPlan === $key) background:linear-gradient(135deg,rgba(249,115,22,0.18),rgba(234,88,12,0.08));border:2px solid #f97316;
                         @else background:rgba(15,23,42,0.5);border:2px solid rgba(255,255,255,0.08); @endif">
                        @if($selectedPlan === $key)
                            <div style="position:absolute;top:10px;left:10px;width:22px;height:22px;border-radius:50%;background:#f97316;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;">✓</div>
                        @endif
                        <div style="color:#f97316;font-size:1.4rem;font-weight:800;margin-bottom:6px;">
                            ${{ rtrim(rtrim(number_format($plan['price'],2),'0'),'.') }}
                        </div>
                        <h3 style="color:#f8fafc;font-size:0.98rem;font-weight:800;margin:0 0 5px;">
                            @switch($key)
                                @case('monthly') باقة شهرية @break
                                @case('3months') باقة 3 أشهر @break
                                @case('6months') باقة 6 أشهر @break
                                @case('yearly') باقة سنوية @break
                            @endswitch
                        </h3>
                        <p style="color:#94a3b8;font-size:0.78rem;line-height:1.5;margin:0;">{{ $plan['description'] }}</p>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:8px;">
                            <span style="font-size:0.7rem;background:rgba(59,130,246,0.18);color:#93c5fd;padding:2px 8px;border-radius:999px;">{{ $plan['duration_months'] }} شهر</span>
                            @if($plan['car_washes'] > 0)
                                <span style="font-size:0.7rem;background:rgba(34,197,94,0.18);color:#86efac;padding:2px 8px;border-radius:999px;">{{ $plan['car_washes'] }} غسيل</span>
                            @endif
                            @if($plan['maintenance'] > 0)
                                <span style="font-size:0.7rem;background:rgba(168,85,247,0.18);color:#c4b5fd;padding:2px 8px;border-radius:999px;">{{ $plan['maintenance'] }} صيانة</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex-btns" style="margin-top:22px;">
                <button type="button" wire:click="back" class="c-btn c-btn-ghost" style="flex:0 0 120px;">→ السابق</button>
                <button type="button" wire:click="nextFromPlan" class="c-btn" style="flex:1;"
                        wire:loading.attr="disabled" wire:target="nextFromPlan">
                    <span wire:loading.remove wire:target="nextFromPlan">التالي ←</span>
                    <span wire:loading wire:target="nextFromPlan">جارٍ الحفظ...</span>
                </button>
            </div>
        @endif

        {{-- ═══════════════ STEP 3: Bank accounts + receipt ═══════════════ --}}
        @if($step === 3)
            <h2 style="color:#f8fafc;font-size:1.2rem;font-weight:800;margin:0 0 4px;">الدفع وإرفاق السند</h2>
            <p style="color:#94a3b8;font-size:0.84rem;margin:0 0 18px;">حوّل قيمة الاشتراك ثم أرفق سند التحويل</p>

            {{-- Bank accounts --}}
            @if($bankAccounts->isEmpty())
                <div style="background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);color:#fde047;padding:14px;border-radius:12px;font-size:0.84rem;margin-bottom:16px;">
                    لا توجد حسابات بنكية متاحة حالياً. يرجى التواصل مع الإدارة.
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
                    @foreach($bankAccounts as $acc)
                        <div style="background:rgba(15,23,42,0.5);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:14px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;flex-wrap:wrap;gap:6px;">
                                <span style="color:#f8fafc;font-weight:800;font-size:0.95rem;">{{ $acc->bank_name }}</span>
                                <span style="font-size:0.7rem;background:rgba(59,130,246,0.18);color:#93c5fd;padding:2px 9px;border-radius:999px;">{{ $acc->currency }}</span>
                            </div>
                            <p style="color:#cbd5e1;font-size:0.82rem;margin:0 0 6px;">صاحب الحساب: {{ $acc->account_name }}</p>
                            <div x-data="{ copied: false }"
                                 style="display:flex;align-items:center;gap:8px;background:rgba(0,0,0,0.3);border-radius:10px;padding:10px 14px;flex-wrap:wrap;">
                                <span dir="ltr" style="color:#fb923c;font-weight:800;font-size:1rem;flex:1;min-width:0;word-break:break-all;letter-spacing:0.5px;">{{ $acc->account_number }}</span>
                                <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $acc->account_number }}').then(()=>{copied=true;setTimeout(()=>copied=false,2000)})"
                                        style="background:rgba(249,115,22,0.2);border:1px solid rgba(249,115,22,0.5);color:#fb923c;border-radius:8px;padding:6px 14px;font-size:0.8rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;white-space:nowrap;transition:.2s;">
                                    <span x-show="!copied">📋 نسخ</span>
                                    <span x-show="copied" x-cloak style="color:#86efac;">✓ تم النسخ</span>
                                </button>
                            </div>
                            @if($acc->iban)
                                <p style="color:#94a3b8;font-size:0.76rem;margin:8px 0 0;" dir="ltr">IBAN: {{ $acc->iban }}</p>
                            @endif
                            @if($acc->notes)
                                <p style="color:#94a3b8;font-size:0.76rem;margin:6px 0 0;">{{ $acc->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ─── Receipt upload (x-data على div خارجي — يحل مشكلة $el.submit) ─── --}}
            <div x-data="{
                    file: null,
                    fileName: '',
                    fileSize: '',
                    fileError: '',
                    submitting: false,
                    handleFile(e) {
                        const f = e.target.files[0];
                        this.fileError = '';
                        if (!f) return;
                        const allowed = ['image/jpeg','image/jpg','image/png','image/webp','application/pdf'];
                        if (!allowed.includes(f.type)) {
                            this.fileError = 'الملف يجب أن يكون صورة (JPG/PNG/WEBP) أو PDF';
                            e.target.value = ''; this.file = null; return;
                        }
                        if (f.size > 5 * 1024 * 1024) {
                            this.fileError = 'حجم الملف يتجاوز 5 ميجابايت';
                            e.target.value = ''; this.file = null; return;
                        }
                        this.file = f;
                        this.fileName = f.name;
                        this.fileSize = (f.size / 1024).toFixed(0) + ' KB';
                    },
                    doSubmit() {
                        if (!this.file) { this.fileError = 'يرجى اختيار ملف السند أولاً'; return; }
                        this.fileError = '';
                        this.submitting = true;
                        this.$refs.receiptForm.submit();
                    }
                }">

                <form method="POST"
                      action="{{ route('customer.complete-registration') }}"
                      enctype="multipart/form-data"
                      x-ref="receiptForm">
                    @csrf

                    <label class="c-label">سند التحويل البنكي *</label>

                    {{-- Upload area --}}
                    <label for="receipt-input"
                           :style="file ? 'border-color:rgba(34,197,94,0.5);background:rgba(34,197,94,0.06);' : ''"
                           style="display:block;border:2px dashed rgba(255,255,255,0.2);border-radius:14px;padding:22px 16px;text-align:center;cursor:pointer;transition:.3s;">
                        <input type="file" name="receipt" id="receipt-input"
                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                               style="display:none;"
                               @change="handleFile($event)">

                        {{-- Empty state --}}
                        <template x-if="!file">
                            <div>
                                <svg width="36" height="36" fill="none" stroke="#64748b" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <p style="color:#cbd5e1;font-weight:700;margin:0 0 4px;font-size:0.92rem;">اضغط هنا لاختيار الملف</p>
                                <p style="color:#64748b;font-size:0.76rem;margin:0;">JPG, PNG, WEBP أو PDF — بحد أقصى 5MB</p>
                            </div>
                        </template>

                        {{-- File selected --}}
                        <template x-if="file">
                            <div>
                                <svg width="32" height="32" fill="none" stroke="#86efac" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p style="color:#86efac;font-weight:700;margin:0 0 2px;font-size:0.9rem;">✓ <span x-text="fileName"></span></p>
                                <p style="color:#64748b;font-size:0.76rem;margin:0;"><span x-text="fileSize"></span> — اضغط لتغيير الملف</p>
                            </div>
                        </template>
                    </label>

                    {{-- Error messages --}}
                    <p x-show="fileError" x-text="fileError"
                       style="color:#fca5a5;font-size:0.8rem;margin:8px 0 0;display:none;" x-cloak></p>

                    @error('receipt')
                        <p style="color:#fca5a5;font-size:0.8rem;margin:8px 0 0;">{{ $message }}</p>
                    @enderror

                    @if(session('error'))
                        <div class="c-error" style="margin-top:12px;">{{ session('error') }}</div>
                    @endif

                    <div class="flex-btns" style="margin-top:22px;">
                        <button type="button" wire:click="back"
                                class="c-btn c-btn-ghost" style="flex:0 0 120px;">→ السابق</button>

                        <button type="button" @click="doSubmit()" class="c-btn" style="flex:1;"
                                :disabled="submitting"
                                :style="submitting ? 'opacity:0.7;cursor:wait;' : ''">
                            <span x-show="!submitting">إنشاء الحساب ✓</span>
                            <span x-show="submitting" x-cloak
                                  style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="animation:spin 1s linear infinite;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                جارٍ الإنشاء...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
