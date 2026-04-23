<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        };

        // generate token baru
        $token = Str::random(60);

        // simpan token ke database
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'message' => 'login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = \App\Models\User::create([
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

    public function logout(Request $request)
    {
        $user = $request->attributes->get('user');

        $user->api_token = null;
        $user->save();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
