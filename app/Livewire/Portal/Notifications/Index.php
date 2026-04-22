<?php

namespace App\Livewire\Portal\Notifications;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.portal')]
#[Title('Notifications')]
class Index extends Component
{
    use WithPagination;

    public function markAsRead(int $id, bool $redirect = false): mixed
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        
        $this->dispatch('toast', type: 'success', message: 'Notification marked as read.');

        if ($redirect && $notification->action_url) {
            return redirect($notification->action_url);
        }
    }

    public function markAllAsRead(): void
    {
        Notification::markAllAsReadForUser(auth()->id());
        $this->dispatch('toast', type: 'success', message: 'All notifications marked as read.');
    }

    public function clearAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->whereNotNull('read_at')
            ->delete();
            
        $this->dispatch('toast', type: 'success', message: 'Read notifications cleared.');
    }

    public function deleteNotification(int $id): void
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();

        $this->dispatch('toast', type: 'success', message: 'Notification deleted.');
    }

    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.portal.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
