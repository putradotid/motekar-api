<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['photo', 'title', 'description', 'name', 'social_handle', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
