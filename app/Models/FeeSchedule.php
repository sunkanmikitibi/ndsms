<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSchedule extends Model
{
    use HasFactory;

    protected $table = 'fee_schedules';

    protected $fillable = [
        'service_type',
        'service_name',
        'description',
        'base_amount',
        'currency',
        'status',
        'effective_from',
        'effective_to',
        'metadata',
    ];

    protected $casts = [
        'base_amount'    => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_to'   => 'datetime',
        'metadata'       => 'json',
    ];

    /**
     * Service types constants
     */
    public const SERVICE_TYPES = [
        'address_registration' => 'Address Registration',
        'street_registration' => 'Street Registration',
        'address_indexing' => 'Address Indexing (Google Maps)',
        'street_revalidation' => 'Street Revalidation',
        'qr_code_generation' => 'QR Code Generation',
        'certificate_generation' => 'Certificate Generation',
    ];

    /**
     * Scope to get active fees
     */
    public function scopeActive($query)
    {
        return $query
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            });
    }

    /**
     * Scope to get fees by service type
     */
    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Get active fee for a specific service
     */
    public static function getActiveFeeFor(string $serviceType): ?self
    {
        return self::byServiceType($serviceType)
            ->active()
            ->latest('effective_from')
            ->first();
    }

    /**
     * Get fee amount for service
     */
    public static function getFeeAmount(string $serviceType): ?float
    {
        $fee = self::getActiveFeeFor($serviceType);
        return $fee?->base_amount;
    }

    /**
     * Check if service has active fee
     */
    public static function hasActiveFee(string $serviceType): bool
    {
        return self::getActiveFeeFor($serviceType) !== null;
    }

    /**
     * Get service display name
     */
    public function getServiceDisplayName(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_name;
    }

    /**
     * Check if fee is currently active
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->effective_from && $this->effective_from->isFuture()) {
            return false;
        }

        if ($this->effective_to && $this->effective_to->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted amount for display
     */
    public function getFormattedAmount(): string
    {
        $symbol = $this->currency === 'NGN' ? '₦' : $this->currency;
        return $symbol . number_format($this->base_amount, 2);
    }
}
