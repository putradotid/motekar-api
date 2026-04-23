<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeetingRequests;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    // user membuat meeting
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        $user = $request->attributes->get('user');

        $meeting = MeetingRequests::created([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json([
            'message' => 'Meeting dibuat',
            'data' => $meeting
        ], 201);
    }

    // user melihat list milih sendiri
    public function MyMeetings(Request $request) {
        $user = $request->attributes->get('user');

        $data = MeetingRequests::where('user_id', $user->id, )->latest()->get();

        return response()->json($data);
    }
    

}
