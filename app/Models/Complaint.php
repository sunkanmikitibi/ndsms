<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'status',
        'admin_response',
        'admin_id',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the complaint
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who responded to the complaint
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope: Get new complaints
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope: Get unresolved complaints
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['new', 'in_progress']);
    }

    /**
     * Mark complaint as in progress
     */
    public function markInProgress(User $admin): void
    {
        $this->update([
            'status' => 'in_progress',
            'admin_id' => $admin->id,
        ]);
    }

    /**
     * Respond to complaint
     */
    public function respond(string $response, User $admin): void
    {
        $this->update([
            'status' => 'resolved',
            'admin_response' => $response,
            'admin_id' => $admin->id,
            'responded_at' => now(),
        ]);
    }

    /**
     * Close complaint
     */
    public function close(User $admin): void
    {
        $this->update([
            'status' => 'closed',
            'admin_id' => $admin->id,
        ]);
    }
}
