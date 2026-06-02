<?php

namespace App\Livewire\Admin\Notifications;

use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('الإشعارات - غازي')]
class BroadcastPage extends Component
{
    public string $title = '';
    public string $message = '';
    public string $targetRole = 'all';
    public bool $isImportant = false;
    public string $successMessage = '';
    public string $errorMessage = '';
    public int $sentCount = 0;

    public function send(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'title.required' => 'العنوان مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
        ]);

        $query = User::where('is_active', true);
        if ($this->targetRole !== 'all') {
            $query->where('user_role', $this->targetRole);
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => auth()->id(),
                'title' => $this->title,
                'message' => $this->message,
                'notification_type' => 'broadcast',
                'is_important' => $this->isImportant,
            ]);
            $count++;
        }

        $this->sentCount = $count;
        $this->successMessage = "تم إرسال الإشعار إلى {$count} مستخدم بنجاح";
        $this->reset(['title', 'message', 'isImportant']);
        $this->targetRole = 'all';
    }

    public function render()
    {
        $recentNotifications = Notification::where('notification_type', 'broadcast')
            ->latest('created_at')
            ->limit(10)
            ->get(['id', 'title', 'message', 'created_at', 'is_important']);

        return view('livewire.admin.notifications.broadcast-page', [
            'recentNotifications' => $recentNotifications,
        ]);
    }
}
