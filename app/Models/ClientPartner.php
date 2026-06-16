<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPartner extends Model
{
    protected $fillable = ['name', 'logo_image', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
