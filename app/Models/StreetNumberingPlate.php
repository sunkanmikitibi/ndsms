<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StreetNumberingPlate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_id',
        'street_name',
        'ward',
        'quantity_requested',
        'plate_type',
        'material',
        'design_variant',
        'installation_date_requested',
        'installation_address',
        'delivery_address',
        'approx_cost',
        'reference_number',
        'status',
        'admin_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'installation_date_requested' => 'date',
        'approx_cost' => 'decimal:2',
        'quantity_requested' => 'integer',
    ];

    /**
     * Get the user who requested the plates
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the street associated with this plate request
     */
    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    /**
     * Get the payment associated with this numbering plate request (polymorphic)
     */
    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    /**
     * Scope: Get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get in production requests
     */
    public function scopeInProduction($query)
    {
        return $query->where('status', 'in_production');
    }

    /**
     * Scope: Get ready requests
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope: Get installed/completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['installed', 'completed']);
    }

    /**
     * Scope: Get by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get plate type label
     */
    public function getPlateTypeLabel(): string
    {
        return match($this->plate_type) {
            'standard' => 'Standard Metal Plate',
            'reflective' => 'Reflective Plate',
            'illuminated' => 'Illuminated Plate',
            'digital' => 'Digital Display',
            default => 'Unknown Type',
        };
    }

    /**
     * Get material label
     */
    public function getMaterialLabel(): string
    {
        return match($this->material) {
            'aluminum' => 'Aluminum',
            'steel' => 'Galvanized Steel',
            'stainless' => 'Stainless Steel',
            'plastic' => 'High-Impact Plastic',
            'composite' => 'Composite',
            default => 'Unknown Material',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'in_production' => 'In Production',
            'ready' => 'Ready for Delivery',
            'delivered' => 'Delivered',
            'installed' => 'Installed',
            'completed' => 'Completed',
            default => 'Unknown Status',
        };
    }

    /**
     * Calculate total cost
     */
    public function getTotalCost(): float
    {
        return (float) $this->approx_cost * $this->quantity_requested;
    }

    /**
     * Mark request as approved
     */
    public function approve(string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Mark request as in production
     */
    public function startProduction(): void
    {
        $this->update([
            'status' => 'in_production',
        ]);
    }

    /**
     * Mark request as ready
     */
    public function markReady(): void
    {
        $this->update([
            'status' => 'ready',
        ]);
    }

    /**
     * Mark request as delivered
     */
    public function markDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
        ]);
    }

    /**
     * Mark request as installed
     */
    public function markInstalled(): void
    {
        $this->update([
            'status' => 'installed',
        ]);
    }

    /**
     * Mark request as completed
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
        ]);
    }

    /**
     * Reject request
     */
    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }
}
