<?php
namespace App\Livewire\Customer;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.customer')]
#[Title('السجل - غازي')]
class HistoryPage extends Component
{
    use WithPagination;

    public string $filterType = '';

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::where('user_id', Auth::id());
        if ($this->filterType) {
            $query->where('action_type', $this->filterType);
        }
        $logs = $query->latest()->paginate(15);

        return view('livewire.customer.history-page', compact('logs'))
            ->layoutData(['showNav' => true]);
    }
}
