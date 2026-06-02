<div>
    @if($successMessage)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    @if(count($users) === 0)
    <div style="background:white;border-radius:20px;padding:64px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="36" height="36" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p style="color:#0f172a;font-size:1.1rem;font-weight:700;">لا توجد طلبات معلقة</p>
        <p style="color:#94a3b8;font-size:0.875rem;margin-top:6px;">جميع الطلبات تمت معالجتها بنجاح</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($users as $user)
        <div style="background:white;border-radius:18px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.06);border:1px solid #f1f5f9;transition:box-shadow 0.2s;">
            {{-- Card Header --}}
            <div style="background:linear-gradient(135deg,#f8faff,#eff6ff);padding:18px 20px;border-bottom:1px solid #e8edf5;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#f97316,#3b82f6);border-radius:14px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1.2rem;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,0.3);">
                        {{ mb_substr($user['full_name'], 0, 1) }}
                    </div>
                    <div>
                        <h3 style="font-weight:700;color:#0f172a;font-size:1rem;">{{ $user['full_name'] }}</h3>
                        <p style="color:#64748b;font-size:0.8rem;margin-top:2px;" dir="ltr">{{ $user['phone'] }}</p>
                    </div>
                    <span style="background:#eff6ff;color:#1d4ed8;font-size:0.72rem;font-weight:600;padding:4px 10px;border-radius:999px;margin-right:4px;">{{ $user['user_role'] }}</span>
                </div>
                <span style="color:#94a3b8;font-size:0.75rem;">{{ $user['created_at'] }}</span>
            </div>

            <div style="padding:18px 20px;display:grid;grid-template-columns:1fr auto;gap:20px;">
                {{-- Vehicle Info --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">نوع المركبة</p>
                        <p style="color:#1e293b;font-weight:600;font-size:0.875rem;">{{ $user['vehicle_type'] ?? 'غير محدد' }}</p>
                    </div>
                    <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;margin-bottom:4px;">رقم المحرك</p>
                        <p style="color:#1e293b;font-weight:600;font-size:0.8rem;" dir="ltr">{{ $user['engine_number'] ?? 'غير محدد' }}</p>
                    </div>
                </div>

                {{-- Subscription Info --}}
                <div style="width:260px;flex-shrink:0;">
                    @if($user['subscription'])
                    <div style="background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1.5px solid #fed7aa;border-radius:14px;padding:14px;">
                        <p style="color:#ea580c;font-weight:700;font-size:0.8rem;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            معلومات الاشتراك
                        </p>
                        <div style="display:flex;flex-direction:column;gap:7px;font-size:0.82rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="color:#78350f;">الباقة</span>
                                <span style="font-weight:700;color:#7c2d12;">{{ $user['subscription']['plan_type'] }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="color:#78350f;">السعر</span>
                                <span style="font-weight:700;color:#7c2d12;">{{ number_format($user['subscription']['price']) }} ر.ي</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="color:#78350f;">الحالة</span>
                                <span style="background:#fef3c7;color:#92400e;font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:999px;">{{ $user['subscription']['status'] }}</span>
                            </div>
                        </div>
                        @if($user['subscription']['receipt_url'])
                        <a href="{{ $user['subscription']['receipt_url'] }}" target="_blank" rel="noopener"
                           style="margin-top:10px;display:inline-flex;align-items:center;gap:5px;color:#ea580c;font-size:0.75rem;font-weight:600;text-decoration:none;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            عرض السند البنكي
                        </a>
                        @else
                        <p style="margin-top:8px;font-size:0.72rem;color:#a16207;">لم يرفق سنداً بنكياً</p>
                        @endif
                    </div>
                    @else
                    <div style="background:#f8faff;border:1.5px dashed #e2e8f0;border-radius:14px;padding:24px;text-align:center;">
                        <p style="color:#94a3b8;font-size:0.82rem;">لم يختر باقة اشتراك</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div style="padding:12px 20px;background:#fafbff;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:flex-end;gap:10px;">
                <button wire:click="openRejectModal({{ $user['id'] }})"
                    style="display:flex;align-items:center;gap:6px;padding:8px 18px;border:1.5px solid #fca5a5;background:#fff5f5;color:#dc2626;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.2s;"
                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    رفض الطلب
                </button>
                <button wire:click="openApproveModal({{ $user['id'] }})"
                    style="display:flex;align-items:center;gap:6px;padding:8px 18px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;box-shadow:0 4px 10px rgba(22,163,74,0.3);transition:all 0.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    قبول الطلب
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Approve Modal --}}
    @if($showModal === 'approve' && $selectedUser)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:420px;">
            <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);padding:20px 24px;border-bottom:1px solid #bbf7d0;display:flex;align-items:center;gap:12px;">
                <div style="width:42px;height:42px;background:linear-gradient(135deg,#16a34a,#15803d);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 style="font-weight:700;color:#14532d;font-size:1rem;">تأكيد قبول المستخدم</h3>
            </div>
            <div style="padding:22px 24px;">
                <p style="color:#475569;font-size:0.875rem;">هل تريد قبول المستخدم <strong style="color:#0f172a;">{{ $selectedUser['full_name'] }}</strong>؟</p>
                <p style="color:#94a3b8;font-size:0.8rem;margin-top:8px;">سيتم إرسال إشعار للمستخدم بقبول حسابه وتفعيله فوراً.</p>
            </div>
            <div style="padding:14px 24px;background:#f8faff;display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="closeModal" style="padding:8px 18px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;background:white;">إلغاء</button>
                <button wire:click="approveUser" style="padding:8px 18px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;" wire:loading.attr="disabled">
                    <span wire:loading.remove>تأكيد القبول</span>
                    <span wire:loading>جاري القبول...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Reject Modal --}}
    @if($showModal === 'reject' && $selectedUser)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:440px;">
            <div style="background:linear-gradient(135deg,#fff5f5,#fee2e2);padding:20px 24px;border-bottom:1px solid #fca5a5;display:flex;align-items:center;gap:12px;">
                <div style="width:42px;height:42px;background:linear-gradient(135deg,#dc2626,#b91c1c);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 style="font-weight:700;color:#7f1d1d;font-size:1rem;">رفض طلب الانضمام</h3>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                <p style="color:#475569;font-size:0.875rem;">رفض طلب انضمام <strong style="color:#0f172a;">{{ $selectedUser['full_name'] }}</strong></p>
                <div>
                    <label style="display:block;color:#374151;font-size:0.82rem;font-weight:600;margin-bottom:6px;">سبب الرفض <span style="color:#dc2626;">*</span></label>
                    <textarea wire:model="rejectionReason" rows="4" placeholder="اكتب سبب الرفض بشكل واضح..." class="input-field" style="width:100%;resize:none;box-sizing:border-box;"></textarea>
                    @error('rejectionReason') <span style="color:#dc2626;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
            </div>
            <div style="padding:14px 24px;background:#fafbff;display:flex;gap:10px;justify-content:flex-end;">
                <button wire:click="closeModal" style="padding:8px 18px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;background:white;">إلغاء</button>
                <button wire:click="rejectUser" style="padding:8px 18px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;" wire:loading.attr="disabled">
                    <span wire:loading.remove>تأكيد الرفض</span>
                    <span wire:loading>جاري الرفض...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
