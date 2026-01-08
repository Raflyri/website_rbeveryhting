<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class LandingSetting extends Model
{
    use HasFactory;
    use HasTranslations;

    // Tambahkan ini agar Laravel mengizinkan input data ke kolom-kolom ini
    protected $fillable = [
        'hero_title',
        'vision_desc',
        'hero_image',
        'hero_video',
    ];

    public $translatable = [
        'hero_title',
        'vision_desc',
    ];
}
