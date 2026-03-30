<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    protected $fillable = [
        'guest_id',
        'guest_name',
        'phone_number',
        'is_attending',
        'companion_count',
        'wishes_message',
    ];

    protected function casts(): array
    {
        return [
            'is_attending'    => 'boolean',
            'companion_count' => 'integer',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
