<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingConfig extends Model
{
    protected $fillable = [
        'groom_name',
        'bride_name',
        'wedding_date',
        'groom_parents',
        'bride_parents',
        // Địa điểm nhà trai (type=1)
        'groom_event_location',
        'groom_event_address',
        'groom_map_url',
        'groom_map_iframe_url',
        // Địa điểm nhà gái (type=2)
        'bride_event_location',
        'bride_event_address',
        'bride_map_url',
        'bride_map_iframe_url',
        'bank_account_info',
        'hero_image_url',
        'hero_image_position',
        'groom_image_url',
        'groom_image_position',
        'bride_image_url',
        'bride_image_position',
        'background_music_url',
        'groom_notification_email',
        'bride_notification_email',
        'mail_notifications_enabled',
    ];

    protected function casts(): array
    {
        return [
            'wedding_date'                => 'datetime',
            'mail_notifications_enabled'  => 'boolean',
        ];
    }
}
