<?php

namespace App\Livewire\Admin\Advertisements;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('إدارة الإعلانات - غازي')]
class AdvertisementsPage extends Component
{
    public string $successMessage  = '';
    public ?int   $deleteConfirmId = null;
    public ?int   $editingId       = null; // id الإعلان المراد تعديله

    public function toggleActive(int $id): void
    {
        $ad = Advertisement::findOrFail($id);
        $ad->update(['is_active' => ! $ad->is_active]);
        $this->successMessage = $ad->is_active ? 'تم تفعيل الإعلان' : 'تم إيقاف الإعلان';
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteConfirmId = $id;
    }

    public function deleteAd(): void
    {
        if (! $this->deleteConfirmId) return;

        $ad = Advertisement::find($this->deleteConfirmId);
        if ($ad) {
            if ($ad->image_path) {
                $disk = config('filesystems.default', 'public');
                Storage::disk($disk)->delete($ad->image_path);
            }
            $ad->delete();
        }

        $this->successMessage  = 'تم حذف الإعلان';
        $this->deleteConfirmId = null;
    }

    public function setEditing(int $id): void
    {
        $this->editingId = $id;
    }

    public function render()
    {
        return view('livewire.admin.advertisements.advertisements-page', [
            'ads'       => Advertisement::orderBy('sort_order')->orderBy('id')->get(),
            'editingAd' => $this->editingId ? Advertisement::find($this->editingId) : null,
        ]);
    }
}
