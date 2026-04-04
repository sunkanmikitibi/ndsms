<?php

namespace App\Services;

use App\Models\FeeSchedule;
use Carbon\Carbon;

class FeeService
{
    /**
     * Get active fee for a service type
     */
    public function getActiveFee(string $serviceType): ?FeeSchedule
    {
        return FeeSchedule::getActiveFeeFor($serviceType);
    }

    /**
     * Get fee amount for a service type
     */
    public function getFeeAmount(string $serviceType): ?float
    {
        return FeeSchedule::getFeeAmount($serviceType);
    }

    /**
     * Check if service type has an active fee
     */
    public function hasActiveFee(string $serviceType): bool
    {
        return FeeSchedule::hasActiveFee($serviceType);
    }

    /**
     * Get all active fees grouped by service type
     */
    public function getActiveFeesByServiceType(): array
    {
        return FeeSchedule::active()
            ->get()
            ->groupBy('service_type')
            ->toArray();
    }

    /**
     * Get formatted fee amount with currency symbol
     */
    public function getFormattedAmount(string $serviceType): ?string
    {
        $fee = $this->getActiveFee($serviceType);
        return $fee?->getFormattedAmount();
    }

    /**
     * Calculate discounted price
     */
    public function calculateDiscount(string $serviceType, float $discountPercent): ?float
    {
        $amount = $this->getFeeAmount($serviceType);
        
        if ($amount === null) {
            return null;
        }

        return $amount * (1 - ($discountPercent / 100));
    }

    /**
     * Apply bulk discount (e.g., per address in bulk verification)
     */
    public function applyBulkDiscount(string $serviceType, int $quantity): ?float
    {
        $amount = $this->getFeeAmount($serviceType);
        
        if ($amount === null) {
            return null;
        }

        // Apply tiered discounts
        if ($quantity >= 100) {
            return $amount * 0.8; // 20% off
        } elseif ($quantity >= 50) {
            return $amount * 0.9; // 10% off
        } elseif ($quantity >= 20) {
            return $amount * 0.95; // 5% off
        }

        return $amount;
    }

    /**
     * Apply expedited processing surcharge
     */
    public function getExpeditedCost(string $serviceType, float $surchargePercent = 50): ?float
    {
        $amount = $this->getFeeAmount($serviceType);
        
        if ($amount === null) {
            return null;
        }

        return $amount * (1 + ($surchargePercent / 100));
    }

    /**
     * Get icon for service type
     */
    public function getIconForServiceType(string $serviceType): string
    {
        return match ($serviceType) {
            'address_registration' => 'map-marker-alt',
            'street_registration' => 'road',
            'address_indexing' => 'globe',
            'street_revalidation' => 'check-circle',
            'qr_code_generation' => 'qrcode',
            'certificate_generation' => 'certificate',
            default => 'receipt',
        };
    }

    /**
     * Get color for service type
     */
    public function getColorForServiceType(string $serviceType): string
    {
        return match ($serviceType) {
            'address_registration' => '#FF6B6B',
            'street_registration' => '#4ECDC4',
            'address_indexing' => '#45B7D1',
            'street_revalidation' => '#9B59B6',
            'qr_code_generation' => '#F39C12',
            'certificate_generation' => '#27AE60',
            default => '#3498DB',
        };
    }

    /**
     * Validate if all services have fees configured
     */
    public function validateAllServicesHaveFees(): array
    {
        $missing = [];
        
        foreach (FeeSchedule::SERVICE_TYPES as $serviceType => $serviceName) {
            if (!$this->hasActiveFee($serviceType)) {
                $missing[$serviceType] = $serviceName;
            }
        }
        
        return $missing;
    }

    /**
     * Get fee history for a service type
     */
    public function getFeeHistory(string $serviceType, int $limit = 10): array
    {
        return FeeSchedule::byServiceType($serviceType)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Check if fee is within effective date range
     */
    public function isFeeWithinValidPeriod(FeeSchedule $fee, Carbon $date = null): bool
    {
        $date = $date ?? now();

        if ($fee->effective_from && $fee->effective_from->isAfter($date)) {
            return false;
        }

        if ($fee->effective_to && $fee->effective_to->isBefore($date)) {
            return false;
        }

        return true;
    }

    /**
     * Get upcoming fee changes
     */
    public function getUpcomingFeeChanges(): array
    {
        return FeeSchedule::where('effective_from', '>', now())
            ->where('status', 'active')
            ->orderBy('effective_from')
            ->get()
            ->toArray();
    }

    /**
     * Get expired fees
     */
    public function getExpiredFees(): array
    {
        return FeeSchedule::where('effective_to', '<', now())
            ->orderByDesc('effective_to')
            ->get()
            ->toArray();
    }
}
