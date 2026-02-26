<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'header_menu',
        'footer_menu',
    ];

    protected $casts = [
        'header_menu' => 'array',
        'footer_menu' => 'array',
    ];
}
