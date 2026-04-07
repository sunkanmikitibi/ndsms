<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsNotificationLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'type',
        'status',
        'message_id',
        'metadata',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'last_retry_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
