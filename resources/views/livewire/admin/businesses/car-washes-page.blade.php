<div>
    @if($successMessage)
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 mb-5 text-sm">{{ $successMessage }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-4 flex gap-3 items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="بحث بالاسم أو الموقع..." class="border border-slate-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-orange-400 flex-1">
        <button wire:click="openCreate" class="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة مغسلة
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($centers as $center)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $center->center_name }}</h3>
                    <p class="text-slate-500 text-xs mt-0.5">{{ $center->owner?->full_name ?? '-' }}</p>
                </div>
                <span class="text-xs {{ $center->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full px-2 py-0.5">{{ $center->is_active ? 'نشطة' : 'موقوفة' }}</span>
            </div>
            <p class="text-slate-500 text-sm mb-3">{{ $center->location }}</p>
            <div class="flex gap-2 border-t border-slate-100 pt-3">
                <button wire:click="openEdit({{ $center->id }})" class="flex-1 text-center text-xs text-blue-600 hover:underline py-1">تعديل</button>
                <button wire:click="toggleStatus({{ $center->id }})" class="flex-1 text-center text-xs {{ $center->is_active ? 'text-red-600' : 'text-green-600' }} hover:underline py-1">{{ $center->is_active ? 'إيقاف' : 'تفعيل' }}</button>
                <button wire:click="delete({{ $center->id }})" wire:confirm="هل تريد الحذف؟" class="flex-1 text-center text-xs text-red-600 hover:underline py-1">حذف</button>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-xl p-16 text-center text-slate-400">لا توجد مغاسل</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $centers->links() }}</div>

    @if($showModal === 'form')
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">{{ $editingId ? 'تعديل المغسلة' : 'إضافة مغسلة جديدة' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-slate-700 text-sm font-medium mb-1">اسم المغسلة *</label><input wire:model="center_name" type="text" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-orange-400">@error('center_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                <div><label class="block text-slate-700 text-sm font-medium mb-1">الموقع *</label><input wire:model="location" type="text" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-orange-400">@error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                @if(!$editingId)
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-slate-700 text-sm font-medium mb-1">السجل التجاري *</label><input wire:model="commercial_register" type="text" dir="ltr" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-orange-400">@error('commercial_register') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                    <div><label class="block text-slate-700 text-sm font-medium mb-1">رمز المركز *</label><input wire:model="center_code" type="text" dir="ltr" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-orange-400">@error('center_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                </div>
                <div><label class="block text-slate-700 text-sm font-medium mb-1">الصاحب *</label><select wire:model="owner_id" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-orange-400"><option value="0">اختر...</option>@foreach($owners as $o)<option value="{{ $o->id }}">{{ $o->full_name }}</option>@endforeach</select>@error('owner_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                @endif
                @if($editingId)<div class="flex items-center gap-2"><input wire:model="is_active" type="checkbox" id="is_active" class="rounded"><label for="is_active" class="text-sm text-slate-700">المغسلة نشطة</label></div>@endif
            </div>
            <div class="px-6 py-4 bg-slate-50 rounded-b-2xl flex gap-3 justify-end">
                <button wire:click="closeModal" class="px-4 py-2 text-slate-600 border border-slate-200 rounded-lg text-sm hover:bg-slate-100 transition">إلغاء</button>
                <button wire:click="save" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingId ? 'حفظ' : 'إضافة' }}</span><span wire:loading>جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
