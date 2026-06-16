<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedCustomer extends Model
{
    protected $fillable = ['photo', 'name', 'designation', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
