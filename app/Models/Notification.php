<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'action_url',
        'action_label',
        'metadata',
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return !$this->isRead();
    }

    /**
     * Get unread notifications for a user
     */
    public static function unreadForUser(int $userId)
    {
        return static::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public static function unreadCountForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent notifications for a user (last 10 days)
     */
    public static function recentForUser(int $userId, int $limit = 30)
    {
        return static::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(10))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsReadForUser(int $userId): void
    {
        static::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
