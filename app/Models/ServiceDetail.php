<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDetail extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'description',
        'image',
        'order',
        'is_active',
    ];

    public function service()
    {
        return $this->belongsTo(ServiceItem::class);
    }
}