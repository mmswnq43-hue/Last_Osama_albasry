<?php

namespace App\Livewire\Customer;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.customer')]
#[Title('الإشعارات - غازي')]
class NotificationsPage extends Component
{
    use WithPagination;

    public function markAllRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markRead(int $id): void
    {
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('livewire.customer.notifications-page', compact('notifications', 'unreadCount'))
            ->layoutData(['showNav' => true]);
    }
}
