<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // ==================== GET PROFILE ====================
    public function show(Request $request)
    {
        $user = $request->attributes->get('user');

        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'address' => $user->address,
            'photo'   => $user->photo
                ? asset('storage/' . $user->photo)
                : null,
            'role'    => $user->role,
        ]);
    }

    // ==================== UPDATE PROFILE ====================
    public function update(Request $request)
{
    try {

        $user = $request->attributes->get('user');

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('profile_photos', 'public');
        }

        $user->update($data);

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ],500);

    }
}
}