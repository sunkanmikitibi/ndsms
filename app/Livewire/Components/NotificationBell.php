<?php

namespace App\Livewire\Components;

use App\Models\Notification;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $unreadNotifications = [];
    public $allNotifications = [];
    public $showPanel = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('notification-created')]
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (auth()->check()) {
            $this->unreadNotifications = Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->toArray();

            $this->allNotifications = Notification::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(10))
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
                ->toArray();

            $this->unreadCount = count($this->unreadNotifications);
        }
    }

    public function togglePanel()
    {
        $this->showPanel = !$this->showPanel;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('notification-read', notificationId: $notificationId);
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function deleteAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNotNull('read_at')
            ->delete();
        
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
