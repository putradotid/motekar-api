<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeetingRequests;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function notificationCount(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Hitung meeting pending
        $pendingMeetings = MeetingRequests::where('status', 'pending')->count();

        // Meeting hari ini
        $meetingsToday = MeetingRequests::whereDate('date', today())
            ->whereIn('status', ['approved', 'pending'])
            ->count();

        // Hitung pesan belum dibaca (meeting yang ada pesan tapi belum dibalas admin)
        $unreadMessages = Message::whereHas('sender', function ($q) {
            $q->where('role', 'user');
        })
        ->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->count();

        // ambil 5 pesan terbaru yang belum dibaca
        $latestUnread = Message::with(['sender', 'meeting'])
        ->whereHas('sender', function ($q) {
            $q->where('role', 'user');
        })
        ->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($msg) {
            return [
                'id'          => $msg->id,
                'message'     => Str::limit($msg->message, 50),
                'sender_name' => $msg->sender->name,
                'meeting_id'  => $msg->meeting_request_id,
                'meeting_title' => $msg->meeting->title ?? '-',
                'time'        => $msg->created_at->setTimezone('Asia/Jakarta')->format('H:i'),
            ];
        });

        return response()->json([
            'pending_meetings' => $pendingMeetings,
            'unread_messages'  => $unreadMessages,
            'latest_unread'    => $latestUnread,
        ]);
    }
}
