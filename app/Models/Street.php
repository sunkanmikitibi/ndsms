<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Street extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'ward',
        'type',
        'description',
        'status',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'distance',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
