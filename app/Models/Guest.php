<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'guest_code',
        'display_name',
        'guest_type',
    ];

    protected function casts(): array
    {
        return [
            'guest_type' => 'integer',
        ];
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function latestRsvp(): HasMany
    {
        return $this->hasMany(Rsvp::class)->latest()->limit(1);
    }
}
