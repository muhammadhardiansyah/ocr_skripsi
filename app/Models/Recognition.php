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
        'vision_text',
        'vision_time'
    ];
}
