<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
        'meeting_id',
        'photo',
        'title',
        'description',
        'name',
        'position',
        'social_handle',
        'rating',
        'status',
        'admin_notes',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(MeetingRequests::class, 'meeting_id');
    }
}