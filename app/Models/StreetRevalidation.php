<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreetRevalidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_id',
        'street_name',
        'town',
        'reason',
        'supporting_documents',
        'current_status',
        'status',
        'admin_note',
        'user_note',
        'reviewed_at',
    ];

    protected $casts = [
        'supporting_documents' => 'json',
        'reviewed_at'          => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class)->nullable();
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
