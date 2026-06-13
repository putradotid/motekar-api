<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\MeetingRequests;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    // user membuat meeting
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time_end'   => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $user = $request->attributes->get('user');
        $datetime = $request->date;
        $tanggal   = substr($datetime, 0, 10);
        $timeStart = substr($request->date, 11, 5);
        $timeEnd   = substr($request->time_end, 0, 5);

        // validasi jam meeting
        if ($timeStart < '08:00' || $timeStart > '17:00') {
            return response()->json([
                'message' => 'Waktu meeting harus antara pukul 08:00 hingga 17:00.'
            ], 422);
        }
        if ($timeEnd < '08:00' || $timeEnd > '17:00') {
            return response()->json([
                'message' => 'Jam selesai harus antara 08:00 - 17:00.'
            ], 422);
        }

        // validasi konflik jadwal meeting
        $conflict = MeetingRequests::whereDate('date', $tanggal)
            ->whereNotIn('status', ['rejected', 'done'])
            ->where(function ($query) use ($timeStart, $timeEnd) {
                $query
                    // Case 1: jam mulai baru ada di antara meeting yang sudah ada
                    ->whereRaw("TIME(date) < ? AND SUBSTR(time_end, 1, 5) > ?", [$timeEnd, $timeStart])
                    // Case 2: jam mulai sama persis
                    ->orWhereRaw("TIME(date) = ?", [$timeStart]);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Tidak bisa membuat meeting — sudah ada jadwal lain pada waktu tersebut. Pilih jam yang berbeda.'
            ], 422);
        }

        // handle file upload jika ada
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('attachments', 'public');
        }

        $meeting = MeetingRequests::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time_end'    => $request->time_end,
            'attachment' => $attachmentPath,
        ]);

        return response()->json([
            'message' => 'Meeting berhasil dibuat dan menunggu persetujuan admin.',
            'data' => $meeting
        ], 201);
    }

    // list stats pada user
    public function stats(Request $request) {
        $user = $request->attributes->get('user');

        return response()->json([
            'pending'   => MeetingRequests::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved'  => MeetingRequests::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected'  => MeetingRequests::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'done'      => MeetingRequests::where('user_id', $user->id)->where('status', 'done')->count(),
        ]);
    }

    // user melihat list milik sendiri
    public function myMeetings(Request $request) {
        $user = $request->attributes->get('user');

        // Search & paginate
        $search  = $request->get('search', '');
        $perPage = $request->get('per_page', 5);

        $data = MeetingRequests::where('user_id', $user->id, )
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate($perPage);

        return response()->json($data);
    }

    // user cancel meeting request
    public function cancel(Request $request, int $id) {
        $user = $request->attributes->get('user');
        $meeting = MeetingRequests::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        if ($meeting->status == 'done') {
            return response()->json([
                'message' => 'Meeting yang sudah selesai tidak dapat dibatalkan.'
            ], 422);
        }

        $meeting->delete();

        return response()->json([
            'message' => 'Meeting berhasil dibatalkan.'
        ]);
    }
    
    // admin melihat semua list request
    public function index(Request $request) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $search  = $request->get('search', '');
        $status = $request->get('status', '');
        $date = $request->get('date', '');
        $perPage = $request->get('per_page', 10);

        $data = MeetingRequests::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->latest()
            ->paginate($perPage);

        return response()->json($data);
    }

    // admin melihat detail meeting request
    public function show(Request $request, int $id) 
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::with('user')->findOrFail($id);

        return response()->json($meeting);
    }

    // admin statistics
    public function statistics(Request $request)
    {
        $user = $request->attributes->get('user');

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbiden'], 403);
        }

        return response()->json([
            'total' => MeetingRequests::count(),
            'pending' => MeetingRequests::where('status', 'pending')->count(),
            'approved' => MeetingRequests::where('status', 'approved')->count(),
            'rejected' => MeetingRequests::where('status', 'rejected')->count(),
            'done' => MeetingRequests::where('status', 'done')->count(),
        ]);
    }

    // admin approved
    public function approved(Request $request, int $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);
        $meeting->update([
            'status' => 'approved',
            'approved_by' => $user->id
        ]);

        // Catat activity
        ActivityLogger::log(
            $user->id,
            'approve_meeting',
            'meeting',
            'Menyetujui meeting request: ' . $meeting->title,
            ['meeting_id' => $meeting->id, 'user_id' => $meeting->user_id]
        );

        return response()->json($meeting);
    }

    // admin reject
    public function reject(Request $request, int $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);
        $meeting->update([
            'status' => 'rejected',
            'approved_by' => $user->id
        ]);

        // Catat activity
        ActivityLogger::log(
            $user->id,
            'reject_meeting',
            'meeting',
            'Menolak meeting request: ' . $meeting->title,
            ['meeting_id' => $meeting->id, 'user_id' => $meeting->user_id]
        );

        return response()->json($meeting);
    }

    // admin meeting request done
    public function done(Request $request, int $id) {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meeting = MeetingRequests::findOrFail($id);

        $meeting->update(['status' => 'done']);

        // Catat activity
        ActivityLogger::log(
            $user->id,
            'done_meeting',
            'meeting',
            'Menyelesaikan meeting: ' . $meeting->title,
            ['meeting_id' => $meeting->id]
        );
        
        return response()->json($meeting);
    }

    // admin mendapatkan meeting 5 terbaru
    public function recentMeeting(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbiden'], 403);
        }

        $data = MeetingRequests::with('user')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($data);
    }

    // stat perbulan pada dashboard
    public function monthlyStats(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbiden'], 403);
        }

        $data = MeetingRequests::selectRaw('MONTH(date) as month, YEAR(date) as year, COUNT(*) as total')
            ->whereRaw('date >= ?', [now()->subMonths(6)->startOfMonth()->format('Y-m-d')])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return response()->json($data);
    }

    // Ambil semua meeting approved untuk calendar
    public function calendarEvents(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $meetings = MeetingRequests::with('user')
            ->where('status', 'approved')
            ->get()
            ->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'title' => $item->user->name . ' — ' . $item->title,
                    'start' => $item->date,
                    'color' => '#FF8C00',
                ];
            });

        return response()->json($meetings);
    }
}
