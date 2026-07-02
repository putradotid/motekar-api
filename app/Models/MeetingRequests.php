<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Message;
use App\Models\MeetingLog;
use Carbon\Carbon;

class MeetingRequests extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'time_end',
        'status',
        'approved_by',
        'attachment',
    ];

    protected $appends = ['display_status'];

    public function getDisplayStatusAttribute(): string
    {
        if (
            $this->status === 'approved' &&
            Carbon::parse($this->date)->isPast()
        ) {
            return 'not_completed';
        }

        return $this->status;
    }

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