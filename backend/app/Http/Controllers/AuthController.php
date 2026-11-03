<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            ]);

            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                    'errors' => [
                        'email' => ['Email atau password salah.'],
                        ],
                    ], 422
                );
            }

    return response()->json([
        'success' => true,
        'message' => 'OK',
        'data' => [
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ],
    ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout berhasil']);
    }



}
