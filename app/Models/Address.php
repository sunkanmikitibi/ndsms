<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_name',
        'applicant_phone',
        'house_number',
        'street_id',
        'town',
        'owner_name',
        'owner_phone',
        'payment_method',
        'reference_code',
        'status',
        'admin_note',
        'user_note',
        'reviewed_at',
        'latitude',
        'longitude',
        'qr_code',
        'code',
        'last_verified_at',
        'verified_by_id',
        'description',
        'approval_status',
        'user_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    /**
     * Get the user who verified this address
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    /**
     * Get the user who created this address
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
