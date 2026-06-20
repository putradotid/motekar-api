<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageServiceSection extends Model
{
    protected $table = 'homepage_service_section';
    
    protected $fillable = [
        'title', 'description',
        'image_1', 'image_2', 'image_3', 'image_4'
    ];
}
