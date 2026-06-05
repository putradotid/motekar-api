<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $module  = $request->get('module', '');
        $perPage = $request->get('per_page', 15);

        $logs = ActivityLog::with('user')
            ->when($module, function ($query) use ($module) {
                $query->where('module', $module);
            })
            ->latest()
            ->paginate($perPage);

        return response()->json($logs);
    }
}
