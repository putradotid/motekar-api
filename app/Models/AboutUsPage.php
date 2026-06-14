<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUsPage extends Model
{
    protected $fillable = [
        'title',
        'description',
        'vision',
        'mission',
        'visi_misi_image',
        'founder_title',
        'founder_description',
        'founder_name',
        'founder_position',
        'founder_image',
    ];
}
