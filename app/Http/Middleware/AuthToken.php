<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthToken
{
    public function handle(Request $request, Closure $next)
    {
        // ambil header Authorization
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json([
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        // ambil token dari Bearer
        $token = str_replace('Bearer ', '', $authHeader);

        // cari user berdasarkan token
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token tidak valid'
            ], 401);
        }

        // simpan user ke request (biar bisa dipakai di controller)
        $request->attributes->set('user', $user);

        return $next($request);
    }
}
