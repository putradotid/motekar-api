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

        $meeting = MeetingRequests::create([
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
    public function myMeetings(Request $request) {
        $user = $request->attributes->get('user');

        $data = MeetingRequests::where('user_id', $user->id, )->latest()->get();

        return response()->json($data);
    }
    
    // admin melihat semua list request
    public function index(Request $request) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return MeetingRequests::latest()->get();
    }

    // admin approved
    public function approved(Request $request, $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);
        $meeting->update([
            'status' => 'approved',
            'approved_by' => $user->id
        ]);
    }

    // admin reject
    public function reject(Request $request, $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);
        $meeting->update([
            'status' => 'rejected',
            'approved_by' => $user->id
        ]);

        return response()->json($meeting);
    }

    // admin meeting request done
    public function done(Request $request, $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);

        $meeting->update(['status' => 'done']);

        return response()->json($meeting);
    }
}
