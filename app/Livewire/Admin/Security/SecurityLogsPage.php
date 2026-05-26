<?php

namespace App\Livewire\Admin\Security;

use App\Models\SecurityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('سجلات الأمان - غازي')]
class SecurityLogsPage extends Component
{
    use WithPagination;

    public string $typeFilter = '';
    public string $dateFrom = '';

    public function updatedTypeFilter(): void { $this->resetPage(); }

    public function render()
    {
        $query = SecurityLog::with('user:id,full_name,phone');

        if ($this->typeFilter) {
            $query->where('log_type', $this->typeFilter);
        }
        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        return view('livewire.admin.security.security-logs-page', [
            'logs' => $query->latest('created_at')->paginate(20),
        ]);
    }
}
