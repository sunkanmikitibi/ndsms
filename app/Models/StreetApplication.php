<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreetApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_name',
        'town',
        'type',
        'description',
        'status',
        'admin_note',
        'user_note',
        'reviewed_at',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'distance',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
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
