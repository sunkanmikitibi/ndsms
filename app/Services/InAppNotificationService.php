<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function create(
        User|int $user,
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $metadata = null,
        ?\DateTime $expiresAt = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon ?? $this->getDefaultIcon($type),
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'metadata' => $metadata,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Create notifications for multiple users
     */
    public function createForMultiple(
        array|Collection $users,
        string $type,
        string $title,
        string $message,
        ?string $icon = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $metadata = null,
        ?\DateTime $expiresAt = null
    ): Collection {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push(
                $this->create(
                    $user,
                    $type,
                    $title,
                    $message,
                    $icon,
                    $actionUrl,
                    $actionLabel,
                    $metadata,
                    $expiresAt
                )
            );
        }

        return $notifications;
    }

    /**
     * Create approval notification
     */
    public function notifyApproval(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'approval',
            $title,
            $message,
            'fas fa-check-circle',
            $actionUrl,
            'View Details',
            $metadata
        );
    }

    /**
     * Create rejection notification
     */
    public function notifyRejection(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'rejection',
            $title,
            $message,
            'fas fa-times-circle',
            $actionUrl,
            'View Details',
            $metadata
        );
    }

    /**
     * Create success notification
     */
    public function notifySuccess(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'success',
            $title,
            $message,
            'fas fa-check',
            $actionUrl,
            null,
            $metadata
        );
    }

    /**
     * Create error notification
     */
    public function notifyError(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'error',
            $title,
            $message,
            'fas fa-exclamation-circle',
            $actionUrl,
            null,
            $metadata
        );
    }

    /**
     * Create warning notification
     */
    public function notifyWarning(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'warning',
            $title,
            $message,
            'fas fa-exclamation-triangle',
            $actionUrl,
            'View Details',
            $metadata
        );
    }

    /**
     * Create info notification
     */
    public function notifyInfo(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'info',
            $title,
            $message,
            'fas fa-info-circle',
            $actionUrl,
            'Learn More',
            $metadata
        );
    }

    /**
     * Create payment notification
     */
    public function notifyPayment(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'payment',
            $title,
            $message,
            'fas fa-credit-card',
            $actionUrl,
            'View Invoice',
            $metadata
        );
    }

    /**
     * Create delivery notification
     */
    public function notifyDelivery(
        User|int $user,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return $this->create(
            $user,
            'delivery',
            $title,
            $message,
            'fas fa-truck',
            $actionUrl,
            'Track Delivery',
            $metadata
        );
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadForUser(User|int $user, int $limit = 10): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCountForUser(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get all recent notifications for a user
     */
    public function getRecentForUser(User|int $user, int $limit = 30): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;
        return Notification::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(10))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        Notification::markAllAsReadForUser($userId);
    }

    /**
     * Delete a notification
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Delete all read notifications for a user
     */
    public function deleteReadNotificationsForUser(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;
        return Notification::where('user_id', $userId)
            ->whereNotNull('read_at')
            ->delete();
    }

    /**
     * Delete expired notifications
     */
    public function deleteExpiredNotifications(): int
    {
        return Notification::where('expires_at', '<=', now())
            ->delete();
    }

    /**
     * Get default icon for notification type
     */
    private function getDefaultIcon(string $type): string
    {
        return match ($type) {
            'approval' => 'fas fa-check-circle',
            'rejection' => 'fas fa-times-circle',
            'success' => 'fas fa-check',
            'error' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'payment' => 'fas fa-credit-card',
            'delivery' => 'fas fa-truck',
            default => 'fas fa-bell',
        };
    }
}
