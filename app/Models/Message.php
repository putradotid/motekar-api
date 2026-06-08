<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'meeting_request_id',
        'sender_id',
        'message',
        'attachment',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function meeting()
    {
        return $this->belongsTo(MeetingRequests::class, 'meeting_request_id');
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}