<div>
    @if($successMessage)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:12px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span style="color:#15803d;font-weight:600;font-size:0.875rem;">{{ $successMessage }}</span>
    </div>
    @endif

    {{-- Filters --}}
    <div style="background:white;border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;box-shadow:0 1px 8px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <select wire:model.live="statusFilter" class="input-field">
            <option value="">كل الحالات</option>
            <option value="open">مفتوحة</option>
            <option value="in_progress">قيد المعالجة</option>
            <option value="resolved">محلولة</option>
            <option value="closed">مغلقة</option>
        </select>
        <select wire:model.live="priorityFilter" class="input-field">
            <option value="">كل الأولويات</option>
            <option value="low">منخفضة</option>
            <option value="normal">عادية</option>
            <option value="high">عالية</option>
            <option value="urgent">عاجلة</option>
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        @forelse($tickets as $ticket)
        <div wire:click="openTicket({{ $ticket->id }})" style="background:white;border-radius:14px;padding:18px 20px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.05);border:1px solid #f1f5f9;transition:all 0.2s;" onmouseover="this.style.boxShadow='0 6px 20px rgba(59,130,246,0.12)';this.style.borderColor='#bfdbfe'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)';this.style.borderColor='#f1f5f9'">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                        @if($ticket->priority === 'urgent')
                            <span class="badge badge-red">🔥 عاجل</span>
                        @elseif($ticket->priority === 'high')
                            <span class="badge badge-orange">⚡ عالي</span>
                        @elseif($ticket->priority === 'normal')
                            <span class="badge badge-blue">عادي</span>
                        @else
                            <span class="badge badge-slate">منخفض</span>
                        @endif
                        @if($ticket->status === 'open')
                            <span class="badge badge-blue">● مفتوحة</span>
                        @elseif($ticket->status === 'resolved')
                            <span class="badge badge-green">✓ محلولة</span>
                        @elseif($ticket->status === 'in_progress')
                            <span class="badge badge-yellow">⏳ قيد المعالجة</span>
                        @else
                            <span class="badge badge-slate">مغلقة</span>
                        @endif
                    </div>
                    <h3 style="font-weight:700;color:#1e293b;font-size:0.95rem;">{{ $ticket->title }}</h3>
                    <p style="color:#64748b;font-size:0.8rem;margin-top:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $ticket->description }}</p>
                </div>
                <div style="text-align:left;flex-shrink:0;">
                    <p style="font-weight:600;color:#1e293b;font-size:0.82rem;">{{ $ticket->user?->full_name ?? '-' }}</p>
                    <p style="color:#94a3b8;font-size:0.72rem;margin-top:4px;">{{ $ticket->created_at?->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
        @empty
        <div style="background:white;border-radius:16px;padding:64px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <p style="color:#94a3b8;font-size:1rem;">لا توجد تذاكر دعم</p>
        </div>
        @endforelse
    </div>
    <div style="margin-top:16px;">{{ $tickets->links() }}</div>

    {{-- Ticket Detail Modal --}}
    @if($selectedTicket)
    <div class="modal-overlay">
        <div class="modal-box" style="max-width:600px;max-height:90vh;overflow-y:auto;">
            <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <h3 style="font-weight:700;color:white;font-size:1rem;">{{ $selectedTicket['title'] }}</h3>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.75rem;margin-top:2px;">{{ $selectedTicket['user_name'] ?? '-' }}</p>
                </div>
                <button wire:click="$set('selectedTicket', null)" style="background:rgba(255,255,255,0.15);border:none;border-radius:8px;padding:6px;cursor:pointer;color:white;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:16px;">
                <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
                    <p style="color:#94a3b8;font-size:0.72rem;font-weight:700;margin-bottom:6px;">تفاصيل المشكلة</p>
                    <p style="color:#1e293b;font-size:0.85rem;line-height:1.6;">{{ $selectedTicket['description'] }}</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:0.82rem;">
                    <div style="background:#f8faff;border-radius:10px;padding:10px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;">المستخدم</p>
                        <p style="font-weight:600;color:#1e293b;margin-top:2px;">{{ $selectedTicket['user_name'] ?? '-' }}</p>
                    </div>
                    <div style="background:#f8faff;border-radius:10px;padding:10px;">
                        <p style="color:#94a3b8;font-size:0.72rem;font-weight:600;">تاريخ الإرسال</p>
                        <p style="font-weight:600;color:#1e293b;margin-top:2px;">{{ $selectedTicket['created_at'] }}</p>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:6px;">الحالة</label>
                        <select wire:model="newStatus" class="input-field" style="width:100%;">
                            <option value="open">مفتوحة</option>
                            <option value="in_progress">قيد المعالجة</option>
                            <option value="resolved">محلولة</option>
                            <option value="closed">مغلقة</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:6px;">الأولوية</label>
                        <select wire:model="newPriority" class="input-field" style="width:100%;">
                            <option value="low">منخفضة</option>
                            <option value="normal">عادية</option>
                            <option value="high">عالية</option>
                            <option value="urgent">عاجلة</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:6px;">رد الإدارة</label>
                    <textarea wire:model="adminResponse" rows="4" placeholder="اكتب الرد هنا..." class="input-field" style="width:100%;resize:none;box-sizing:border-box;"></textarea>
                </div>
            </div>
            <div style="padding:14px 24px;background:#f8faff;display:flex;gap:10px;justify-content:flex-end;border-top:1px solid #f1f5f9;">
                <button wire:click="$set('selectedTicket', null)" style="padding:8px 18px;color:#64748b;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;background:white;">إغلاق</button>
                <button wire:click="updateTicket" class="btn-primary">حفظ التحديث</button>
            </div>
        </div>
    </div>
    @endif
</div>
