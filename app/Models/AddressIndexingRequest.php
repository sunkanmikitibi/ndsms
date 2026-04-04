<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressIndexingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_line',
        'latitude',
        'longitude',
        'house_number',
        'owner_name',
        'owner_phone',
        'applicant_name',
        'applicant_phone',
        'property_images',
        'description',
        'status',
        'admin_note',
        'reviewed_at',
    ];

    protected $casts = [
        'property_images' => 'json',
        'reviewed_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
