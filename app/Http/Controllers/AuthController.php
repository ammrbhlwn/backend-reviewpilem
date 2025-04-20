<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register_user(Request $request)
    {
        /*
        - validasi request
        - cek nomor (pake validate())
        - cek role (pake validate())
        - cek user apakah sudah ada atau belum berdasarkan email
        - buat user
        - kirim response
        */

        $fields = $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'role' => 'user',
            'password' => 'required|string|confirmed'
        ]);

        // cek email
        if (User::where('email', $fields['email'])->exists()) {
            return response()->json([
                'message' => 'Email already exists',
            ], 409);
        }

        // buat user
        $user = User::create($fields);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        /*
            1. validate request
            2. cek apakah ada user dengan email tersebut
            3. jika ada user, buat token
            4. return token as response
        */

        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email not registered',
            ], 404);
        }

        if ($user->role == 'admin') {
            return response()->json([
                'message' => 'unauthorized',
            ], 401);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Wrong credentials',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'message' => 'User Login Successful',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login_admin(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email not registered',
            ], 404);
        }

        if ($user->role != 'admin') {
            return response()->json([
                'message' => 'unauthorized',
            ], 401);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Wrong credentials',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'message' => 'Admin Login Successful',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
