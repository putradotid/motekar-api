<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    private function checkAdmin(Request $request)
    {
        $user = $request->attributes->get('user');

        if (!$user || $user->role !== 'admin') {
            return null;
        }
        return $user;
    }

    // manage list user
    public function ListUser(Request $request)
    {
        $authUser = $this->checkAdmin($request);
        if (!$authUser) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $role = $request->get('role', '');
        $perPage = $request->get('perPage', 10);

        $users = User::where('id', '!=', $authUser->id)
            ->when($search, function ($query) use ($search){
            $query->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status){
                $query->where('status', $status);
            })
            ->when($role, function ($query) use ($role){
                $query->where('role', $role);
            })
            ->latest()
            ->paginate();
        
        return response()->json($users);
    }

    // suspend user
    public function suspend(Request $request, int $id)
    {
        $authUser = $this->checkAdmin($request);
        if (!$authUser) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::findOrFail($id);

        // tidak bisa suspend akun sendiri
        if ($user->id === $authUser->id) {
            return response()->json([
                'message' => 'Tidak dapat suspend akun sendiri.'
            ], 422);
        }

        $user->update(['status' =>'suspend']);

        // Catat activity
        ActivityLogger::log(
            $authUser->id,
            'suspend_user',
            'user',
            'Mensuspend user: ' . $user->name . ' (' . $user->email . ')',
            ['target_user_id' => $user->id]
        );
        
        return response()->json(['message' => 'User berhasil disuspend.']);
    }

    // Unsuspend
    public function active(Request $request, int $id)
    {
        $authUser = $this->checkAdmin($request);
        if (!$authUser) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::findOrFail($id);

        // tidak bisa suspend akun sendiri
        if ($user->id === $authUser->id) {
            return response()->json([
                'message' => 'Tidak dapat suspend akun sendiri.'
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->update(['status' =>'active']);

        // Catat activity
        ActivityLogger::log(
            $authUser->id,
            'activate_user',
            'user',
            'Mengaktifkan user: ' . $user->name . ' (' . $user->email . ')',
            ['target_user_id' => $user->id]
        );

        return response()->json(['message' => 'User berhasil diaktifkan.']);
    }
}
