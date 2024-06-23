<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recognition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'tesseract_text',
        'tesseract_time',
        'tesseract_percentage',
        'vision_text',
        'vision_time',
        'vision_percentage',
    ];
}
