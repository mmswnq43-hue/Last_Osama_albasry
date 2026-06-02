<?php

namespace App\Livewire\Admin\Settings;

use App\Models\SystemSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SettingsPage extends Component
{
    public int $priorityCardLitersThreshold = 500;
    public int $priorityCardValidityDays = 30;
    public string $successMessage = '';

    public function mount(): void
    {
        $this->priorityCardLitersThreshold = (int) SystemSetting::get('priority_card_liters_threshold', 500);
        $this->priorityCardValidityDays    = (int) SystemSetting::get('priority_card_validity_days', 30);
    }

    public function save(): void
    {
        $this->validate([
            'priorityCardLitersThreshold' => ['required', 'integer', 'min:1', 'max:10000'],
            'priorityCardValidityDays'    => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        SystemSetting::set('priority_card_liters_threshold', $this->priorityCardLitersThreshold);
        SystemSetting::set('priority_card_validity_days', $this->priorityCardValidityDays);

        $this->successMessage = 'تم حفظ الإعدادات بنجاح.';
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-page');
    }
}
