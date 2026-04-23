<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    // user membuat meeting
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date'
        ]);
    }
}
