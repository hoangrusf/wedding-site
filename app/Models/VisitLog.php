<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $fillable = [
        'invite_name',
        'type',
        'ip_address',
        'user_agent',
    ];
}
