<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Message;
use App\Models\MeetingLog;

class MeetingRequests extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'status',
        'approved_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approved()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs()
    {
        return $this->hasMany(MeetingLog::class, 'meeting_id');
    }

    // Relasi ke Message
    public function messages()
    {
        return $this->hasMany(Message::class, 'meeting_request_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'meeting_request_id')->latest();
    }
}