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
        'ward',
        'owner_name',
        'owner_phone',
        'payment_method',
        'reference_code',
        'status',
        'admin_note',
        'reviewed_at',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }
}
