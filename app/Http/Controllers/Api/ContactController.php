<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // public kirim pesan
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactSubmission::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'unread',
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim. Kami akan segera menghubungi Anda.',
            'data'    => $contact,
        ], 201);
    }

    // admin melihat semua pesan
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $status  = $request->get('status', '');
        $search  = $request->get('search', '');
        $perPage = $request->get('per_page', 10);

        $contacts = ContactSubmission::when($status, function ($q) use ($status) {
                        $q->where('status', $status);
                    })
                    ->when($search, function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('subject', 'like', '%' . $search . '%');
                    })
                    ->latest()
                    ->paginate($perPage);

        return response()->json($contacts);
    }

    // admin melihat detail pesan
    public function show(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $contact = ContactSubmission::findOrFail($id);

        // auto tandai read saat dibuka
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        return response()->json($contact);
    }

    // admin update status pesan
    public function updateStatus(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'status' => 'required|in:unread,read,replied',
        ]);

        $contact = ContactSubmission::findOrFail($id);
        $contact->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status berhasil diupdate.',
            'data'    => $contact,
        ]);
    }

    // admin hapus pesan
    public function destroy(Request $request, int $id)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        ContactSubmission::findOrFail($id)->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus.']);
    }
}
