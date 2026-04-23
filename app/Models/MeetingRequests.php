<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRequests extends Model
{
    protected $fillable = [
        'user_id','title','description','date','status','approved_by'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function approved() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs() {
        return $this->hasMany(MeetingLog::class, 'meeting_id');
    }
}
