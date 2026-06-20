<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'icon_url', 'order', 'is_active', 'image_1', 'image_2', 'image_3', 'image_4'];
    protected $casts = ['is_active' => 'boolean'];
}
