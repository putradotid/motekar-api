<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'vision',
        'mission',
    ];
}
