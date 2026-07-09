<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    protected $fillable = ['title', 'icon_url', 'description', 'detail_description', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function details()
{
    return $this->hasMany(ServiceDetail::class, 'service_id')
                ->orderBy('order');
}
}
