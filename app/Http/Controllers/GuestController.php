<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function get_all_film()
    {
        $films = Film::select('id', 'judul', 'status_penayangan', 'total_episode', 'tanggal_rilis')
            ->withAvg('review', 'rating')
            ->get()
            ->map(function ($film) {
                $film->average_rating = number_format($film->review_avg_rating, 2, '.', '');
                unset($film->review_avg_rating);
                return $film;
            });

        return response()->json($films);
    }

    public function get_film_by_id($id)
    {
        $film = Film::find($id);

        if (!$film) {
            return response()->json([
                'message' => 'Film tidak ditemukan'
            ], 404);
        }

        $film->loadAvg('review', 'rating');
        $film->average_rating = number_format($film->review_avg_rating, 2, '.', '');
        $film->makeHidden(['created_at', 'updated_at', 'review_avg_rating']);

        return response()->json($film);
    }

    public function get_user_list()
    {
        $users = User::select('id', 'nama', 'username')
            ->get();
        return response()->json($users);
    }

    public function get_user_detail($id)
    {
        $user = User::select('id', 'nama', 'username', 'bio')
            ->where('id', $id)
            ->with(['review' => function ($query) {
                $query->select('id', 'user_id', 'film_id', 'rating', 'komentar')
                    ->orderBy('created_at', 'asc');
            }])
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json($user);
    }

    public function search_film(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
        ]);

        $title = $request->query('judul');

        $films = Film::where('judul', 'like', '%' . $title . '%')
            ->select('id', 'judul', 'status_penayangan', 'total_episode', 'tanggal_rilis')
            ->withAvg('review', 'rating')
            ->get()
            ->map(function ($film) {
                $film->average_rating = round($film->review_avg_rating, 2);
                unset($film->review_avg_rating);
                return $film;
            });

        return response()->json($films);
    }
}
