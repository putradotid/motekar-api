<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request){
        // validasi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        };

        if ($user->status === 'suspend') {
            return response()->json([
                'message' => 'Akun anda telah disuspend. Hubungi administrator.'
            ], 403);
        }

        // generate token baru
        $token = Str::random(60);

        // simpan token ke database
        $user->api_token = $token;
        $user->save();

        // log aktivitas login
        ActivityLogger::log(
            $user->id,
            'login',
            'auth',
            $user->name . ' berhasil login',
            ['role' => $user->role]
        );

        return response()->json([
            'message' => 'login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'address' => $user->address,
                'role' => $user->role
            ]
        ]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user
        ]);
    }

    public function createdAdmin(Request $request) {
        $authUser = $request->attributes->get('user');
        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        ActivityLogger::log(
            $authUser->id,
            'create_user',
            'user',
            'Membuat akun baru: ' . $user->name . ' dengan role ' . $user->role,
            ['new_user_id' => $user->id, 'role' => $user->role]
        );

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->attributes->get('user');

        $user->api_token = null;
        $user->save();

        // log aktivitas logout
        ActivityLogger::log(
            $user->id,
            'logout',
            'auth',
            $user->name . ' berhasil logout',
            ['role' => $user->role]
        );
        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
