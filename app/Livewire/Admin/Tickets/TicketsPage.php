<?php

namespace App\Livewire\Admin\Tickets;

use App\Models\SupportTicket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('تذاكر الدعم - غازي')]
class TicketsPage extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $successMessage = '';
    public ?array $selectedTicket = null;
    public string $adminResponse = '';
    public string $newStatus = '';
    public string $newPriority = '';

    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPriorityFilter(): void { $this->resetPage(); }

    public function openTicket(int $id): void
    {
        $ticket = SupportTicket::with('user:id,full_name,phone')->findOrFail($id);
        $this->selectedTicket = [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'admin_response' => $ticket->admin_response,
            'user_name' => $ticket->user?->full_name,
            'user_phone' => $ticket->user?->phone,
            'created_at' => $ticket->created_at?->format('Y-m-d H:i'),
            'resolved_at' => $ticket->resolved_at?->format('Y-m-d H:i'),
        ];
        $this->adminResponse = $ticket->admin_response ?? '';
        $this->newStatus = $ticket->status;
        $this->newPriority = $ticket->priority;
    }

    public function updateTicket(): void
    {
        if (! $this->selectedTicket) return;

        $ticket = SupportTicket::findOrFail($this->selectedTicket['id']);
        $ticket->fill([
            'status' => $this->newStatus,
            'priority' => $this->newPriority,
            'admin_response' => $this->adminResponse,
        ]);
        if (in_array($this->newStatus, ['resolved', 'closed'])) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        $this->successMessage = 'تم تحديث التذكرة بنجاح';
        $this->selectedTicket = null;
    }

    public function render()
    {
        $query = SupportTicket::with('user:id,full_name');

        if ($this->statusFilter) $query->where('status', $this->statusFilter);
        if ($this->priorityFilter) $query->where('priority', $this->priorityFilter);

        return view('livewire.admin.tickets.tickets-page', [
            'tickets' => $query->latest('created_at')->paginate(15),
        ]);
    }
}
