<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Review;
use App\Models\UserFilmList;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_profile_data(Request $request)
    {
        $user = $request->user()->load(['filmLists' => function ($query) {
            $query->select('films.id', 'judul')->withPivot('status_list');
        }]);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'username' => $user->username,
                'display_name' => $user->display_name,
                'bio' => $user->bio,
                'film_list' => $user->filmLists->map(function ($film) {
                    return [
                        'id' => $film->id,
                        'judul' => $film->judul,
                        'status_list' => $film->pivot->status_list,
                    ];
                }),
            ]
        ], 200);
    }

    public function add_profile_data(Request $request)
    {
        $request->validate([
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user = $request->user();
        $user->display_name = $request->display_name;
        $user->bio = $request->bio;
        $user->save();

        return response()->json([
            'message' => 'Data profil berhasil ditambahkan',
            'data' => [
                'nama' => $user->nama,
                'username' => $user->username,
                'email' => $user->email,
                'display_name' => $user->display_name,
                'bio' => $user->bio,
            ]
        ], 201);
    }

    public function edit_profile_data(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'username' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user = $request->user();
        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->display_name = $request->display_name;
        $user->bio = $request->bio;
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'nama' => $user->nama,
                'username' => $user->username,
                'display_name' => $user->display_name,
                'bio' => $user->bio,
            ]
        ], 200);
    }

    public function add_film_to_list(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'status_list' => 'required|string|in:plan_to_watch,watching,completed,on_hold,dropped',
        ]);

        try {
            $user = $request->user();
            $film = Film::find($request->film_id);

            // validasi status review sebelum ubah status plan_to_watch
            $alreadyReviewed = Review::where('user_id', $user->id)
                ->where('film_id', $film->id)
                ->exists();

            if ($request->status_list === 'plan_to_watch' && $alreadyReviewed) {
                return response()->json([
                    'message' => 'Perubahan tidak dapat dilakukan'
                ], 409);
            }

            // film not_yet_aired hanya boleh untuk plan_to_watch
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => $alreadyExists ? 'Status film diperbarui' : 'Film ditambahkan ke list'
        ], 200);
    }

    public function add_review(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'rating' => 'required|integer|min:1|max:10',
            'komentar' => 'required|string|max:1000',
        ]);

        try {
            $user = $request->user();
            $film = Film::findOrFail($request->film_id);

            // Cek data di database
            $userFilmList = UserFilmList::where('user_id', $user->id)
                ->where('film_id', $film->id)
                ->first();

            if (!$userFilmList) {
                return response()->json([
                    'message' => 'Film tidak ditemukan dalam list'
                ], 404);
            }

            // Cek status plan_to_watch
            if ($userFilmList->status_list === 'plan_to_watch') {
                return response()->json([
                    'message' => 'Tidak dapat melakukan review'
                ], 403);
            }

            // Cek status penayangan
            if ($film->status_penayangan->value === 'not_yet_aired') {
                return response()->json([
                    'message' => 'Film belum ditayangkan. Tidak dapat melakukan review.'
                ], 403);
            }

            // Sudah pernah direview?
            if ($user->review()->where('film_id', $film->id)->exists()) {
                return response()->json([
                    'message' => 'Film sudah pernah direview.'
                ], 409);
            }

            // Simpan review
            $review = Review::create([
                'film_id' => $film->id,
                'user_id' => $user->id,
                'rating' => $request->rating,
                'komentar' => $request->komentar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Review berhasil ditambahkan',
            'data' => $review
        ], 201);
    }

    public function edit_review(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'komentar' => 'required|string|max:1000',
        ]);

        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'message' => 'Review not found'
                ], 404);
            };

            $review->update([
                'rating' => $request->rating,
                'komentar' => $request->komentar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Review berhasil diperbarui',
            'data' => $review
        ], 200);
    }

    public function delete_review($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Review not found'
            ], 404);
        };

        $review->delete();

        return response()->json([
            'message' => 'Review berhasil dihapus'
        ], 200);
    }
}
