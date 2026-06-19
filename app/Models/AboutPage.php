<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'image_6',
    ];
}
