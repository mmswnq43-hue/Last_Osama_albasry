<div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        {{-- Send Form --}}
        <div style="background:white;border-radius:18px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#f97316,#3b82f6);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <h2 style="font-weight:700;color:#0f172a;font-size:1rem;">إرسال إشعار جماعي</h2>
            </div>

            @if($successMessage)
            <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
                <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
            </div>
            @endif

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#374151;margin-bottom:6px;">الجمهور المستهدف</label>
                    <select wire:model="targetRole" class="input-field" style="width:100%;">
                        <option value="all">🌐 جميع المستخدمين</option>
                        <option value="customer">👤 المستخدمون العاديون</option>
                        <option value="station_owner">⛽ ملاك المحطات</option>
                        <option value="car_wash_owner">🚿 ملاك المغاسل</option>
                        <option value="maintenance_owner">🔧 ملاك مراكز الصيانة</option>
                        <option value="station_worker">👷 موظفو المحطات</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#374151;margin-bottom:6px;">عنوان الإشعار <span style="color:#ef4444;">*</span></label>
                    <input wire:model="title" type="text" placeholder="مثال: تنبيه مهم، عرض خاص..." class="input-field" style="width:100%;box-sizing:border-box;">
                    @error('title') <span style="color:#dc2626;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:700;color:#374151;margin-bottom:6px;">نص الإشعار <span style="color:#ef4444;">*</span></label>
                    <textarea wire:model="message" rows="5" placeholder="اكتب نص الإشعار هنا..." class="input-field" style="width:100%;resize:none;box-sizing:border-box;"></textarea>
                    @error('message') <span style="color:#dc2626;font-size:0.75rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
                <div style="display:flex;align-items:center;gap:10px;background:#fffbf5;border:1px solid #fed7aa;border-radius:10px;padding:12px 14px;">
                    <input wire:model="isImportant" type="checkbox" id="isImportant" style="width:16px;height:16px;accent-color:#f97316;">
                    <label for="isImportant" style="font-size:0.85rem;color:#374151;font-weight:500;cursor:pointer;">🔔 تمييز كإشعار مهم (سيظهر بشكل بارز للمستخدم)</label>
                </div>
                <button wire:click="send" class="btn-primary" style="width:100%;justify-content:center;display:flex;align-items:center;gap:8px;padding:12px;" wire:loading.attr="disabled">
                    <svg wire:loading.remove width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span wire:loading.remove style="font-size:0.9rem;">إرسال الإشعار</span>
                    <span wire:loading style="font-size:0.9rem;">⏳ جاري الإرسال...</span>
                </button>
            </div>
        </div>

        {{-- Recent Notifications --}}
        <div style="background:white;border-radius:18px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9;">
            <h2 style="font-weight:700;color:#0f172a;font-size:0.9rem;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <span style="width:4px;height:16px;background:linear-gradient(#f97316,#3b82f6);border-radius:4px;display:inline-block;"></span>
                آخر الإشعارات المرسلة
            </h2>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @forelse($recentNotifications as $notif)
                <div style="border:1px solid #f1f5f9;border-radius:12px;padding:12px;transition:border-color 0.2s;" onmouseover="this.style.borderColor='#bfdbfe'" onmouseout="this.style.borderColor='#f1f5f9'">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;flex-wrap:wrap;">
                        @if($notif->is_important)
                        <span style="background:linear-gradient(135deg,#fff7ed,#ffedd5);color:#ea580c;font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:999px;border:1px solid #fed7aa;">🔔 مهم</span>
                        @endif
                        <p style="font-weight:700;color:#1e293b;font-size:0.82rem;">{{ $notif->title }}</p>
                    </div>
                    <p style="color:#64748b;font-size:0.75rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $notif->message }}</p>
                    <p style="color:#94a3b8;font-size:0.7rem;margin-top:6px;">{{ $notif->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                @empty
                <div style="text-align:center;padding:32px;color:#94a3b8;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p style="font-size:0.82rem;">لا توجد إشعارات مرسلة</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
