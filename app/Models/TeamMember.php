<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = ['photo', 'name', 'designation', 'division', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
