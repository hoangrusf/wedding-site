<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    protected $fillable = [
        'image_url',
        'alt_text',
        'layout',
        'object_fit',
        'object_position',
        'sort_order',
    ];
}
