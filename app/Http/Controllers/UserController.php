<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_all_user()
    {
        $users = User::select('id', 'username', 'display_name')
            ->get();

        return response()->json([
            'data' => $users
        ], 200);
    }

    public function get_user_by_id($id)
    {
        $user = User::with('watchList')
            ->select('id', 'username', 'display_name', 'bio')
            ->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function add_film_to_list(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'status_list' => 'required|string|in:plan_to_watch,watching,completed,on_hold,dropped',
        ]);

        $user = $request->user();
        $film = Film::find($request->film_id);

        // film not_yet_aired hanya boleh untuk status plan_to_watch
        if (
            $film->status_penayangan->value === 'not_yet_aired' &&
            $request->status_list !== 'plan_to_watch'
        ) {
            return response()->json([
                'message' => 'Film yang belum tayang hanya dapat dimasukkan ke dalam plan_to_watch'
            ], 422);
        }

        // Cek apakah film sudah ada
        $alreadyExists = $user->filmLists()->where('film_id', $film->id)->exists();

        if ($alreadyExists) {
            // update status_list saja
            $user->filmLists()->updateExistingPivot($film->id, [
                'status_list' => $request->status_list,
            ]);
        } else {
            // tambahkan film ke list
            $user->filmLists()->attach($film->id, [
                'status_list' => $request->status_list,
            ]);
        }

        return response()->json([
            'message' => $alreadyExists ? 'Status film diperbarui' : 'Film ditambahkan ke list'
        ], 200);
    }
}
