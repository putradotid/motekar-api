<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'icon_url', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
