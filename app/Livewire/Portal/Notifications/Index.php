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

    public function markAsRead(int $id): void
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        
        $this->dispatch('toast', type: 'success', message: 'Notification marked as read.');
    }

    public function markAllAsRead(): void
    {
        Notification::markAllAsReadForUser(auth()->id());
        
        $this->dispatch('toast', type: 'success', message: 'All notifications marked as read.');
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
            ->paginate(20);

        return view('livewire.portal.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
