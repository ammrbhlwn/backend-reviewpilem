<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        // get by id
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not Found'
            ], 404);
        }


        // Validate the request data
        $fields = $request->validate([
            'nama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . auth()->id(), // Optional, unique except for the current user
            'password' => 'nullable|string|min:6',
            'nomor' => 'nullable|string|max:20'
        ]);

        // Hash the password
        if (isset($fields['password'])) {
            $fields['password'] = bcrypt($fields['password']);
        }

        $user->update($fields);

        // Return a success response
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    // self delete
    public function delete(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'Authenticated User not Found',
            ], 404);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json([
            'message' => 'user deleted',
        ], 200);
    }
}
