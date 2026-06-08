<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\MeetingRequests;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // List meeting yang punya akses chat
    public function meetings(Request $request)
    {
        $user = $request->attributes->get('user');

        $meetings = MeetingRequests::with(['user', 'latestMessage'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereIn('status', ['approved', 'done'])
            ->latest()
            ->get()
            ->map(function ($item) {
                $lastMessage = $item->latestMessage;

                return [
                    'id'           => $item->id,
                    'title'        => $item->title,
                    'user'         => $item->user,
                    'status'       => $item->status,
                    'last_message' => $lastMessage ? $lastMessage->message : '-',
                    'last_time'    => $lastMessage ? $lastMessage->created_at->format('H:i') : '',
                ];
            });

        return response()->json($meetings);
    }

    // Ambil pesan per meeting
    public function index(Request $request, int $meetingId)
    {
        $user    = $request->attributes->get('user');
        $meeting = MeetingRequests::findOrFail($meetingId);

        // Pastikan user hanya bisa akses meeting miliknya
        if ($user->role !== 'admin' && $meeting->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // menandai pesan sudah dibaca
        $messages = Message::where('meeting_request_id', $meetingId)
            ->where('sender_id', '!=', $user->id)
            ->get();

        foreach ($messages as $msg) {
            MessageRead::firstOrCreate([
                'message_id' => $msg->id,
                'user_id'    => $user->id,
            ], [
                'read_at' => now(),
            ]);
        }

        $messages = Message::with('sender')
            ->where('meeting_request_id', $meetingId)
            ->oldest()
            ->get()
            ->map(function ($msg) use ($user) {
                return [
                    'id'         => $msg->id,
                    'message'    => $msg->message,
                    'attachment' => $msg->attachment,
                    'is_read'    => $msg->reads()->where('user_id', $user->id)->exists(),
                    'sender'     => [
                        'id'   => $msg->sender->id,
                        'name' => $msg->sender->name,
                        'role' => $msg->sender->role,
                    ],
                    'time'       => $msg->created_at->format('H:i'),
                    'date'       => $msg->created_at->format('Y-m-d'),
                ];
            });

        return response()->json($messages);
    }

    // Kirim pesan
    public function store(Request $request, int $meetingId)
    {
        $user    = $request->attributes->get('user');
        $meeting = MeetingRequests::findOrFail($meetingId);

        if ($user->role !== 'admin' && $meeting->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'message'    => 'required_without:attachment|nullable|string',
            'attachment' => 'required_without:message|nullable|file|max:3072',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $message = Message::create([
            'meeting_request_id' => $meetingId,
            'sender_id'          => $user->id,
            'message'            => $request->message,
            'attachment'         => $attachmentPath,
        ]);

        $message->load('sender');

        return response()->json([
            'id'         => $message->id,
            'message'    => $message->message,
            'attachment' => $message->attachment,
            'sender'     => [
                'id'   => $message->sender->id,
                'name' => $message->sender->name,
                'role' => $message->sender->role,
            ],
            'time'       => $message->created_at->format('H:i'),
            'date'       => $message->created_at->format('Y-m-d'),
        ], 201);
    }

    // Tandai pesan sudah dibaca
    public function markAsRead(Request $request, int $meetingId)
    {
        $user = $request->attributes->get('user');

        // Ambil semua pesan di meeting ini yang belum dibaca user
        $messages = Message::where('meeting_request_id', $meetingId)
            ->where('sender_id', '!=', $user->id) // hanya pesan dari orang lain
            ->get();

        foreach ($messages as $message) {
            MessageRead::firstOrCreate([
                'message_id' => $message->id,
                'user_id'    => $user->id,
            ], [
                'read_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Pesan ditandai sudah dibaca.']);
    }
}
