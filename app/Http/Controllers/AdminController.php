<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function add_genre(Request $request)
    {
        $check_json = $this->check($request);
        if ($check_json->getStatusCode() != 200) {
            return $check_json;
        };

        $fields = $request->validate([
            'nama_genre' => 'required|string',
        ]);

        try {
            $genre = Genre::create([
                'nama_genre' => 'required|string',
            ]);

            if (!$genre) {
                throw new \Exception('Failed to create genre');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil menambahkan genre',
            'data' => $genre
        ], 200);
    }

    public function edit_genre(Request $request, $id)
    {
        $fields = $request->validate([
            'nama_genre' => 'required|string',
        ]);

        $genre = Genre::where('id', $id)->first();
        if (!$genre) {
            return response()->json([
                'message' => 'Genre not found'
            ], 404);
        };

        try {
            // update
            $genre->update($fields);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil mengubah genre',
            'data' => $genre
        ], 200);
    }

    public function add_film(Request $request)
    {
        $check_json = $this->check($request);
        if ($check_json->getStatusCode() != 200) {
            return $check_json;
        };

        $fields = $request->validate([
            'judul' => 'required|string',
            'sinopsis' => 'required|string',
            'gambar' => 'array',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'status_penayangan' => 'required|string',
            'total_episode' => 'required|integer',
            'tanggal_rilis' => 'required|date',
            'id_genre' => 'required|exists:genres,id',
        ]);

        try {
            $film = Film::create([
                'judul' => 'required|string',
                'sinopsis' => 'required|string',
                'gambar' => 'array',
                'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'status_penayangan' => 'required|string',
                'total_episode' => 'required|integer',
                'tanggal_rilis' => 'required|date',
                'id_genre' => 'required|exists:genres,id',
            ]);

            if (!$film) {
                throw new \Exception('Failed to create film');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil menambahkan film',
            'data' => $film
        ], 200);
    }

    public function edit_film(Request $request, $id)
    {
        $fields = $request->validate([
            'nama_genre' => 'required|string',
        ]);

        $film = Film::where('id', $id)->first();
        if (!$film) {
            return response()->json([
                'message' => 'Film not found'
            ], 404);
        };

        try {
            // update
            $film->update($fields);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil mengubah film',
            'data' => $film
        ], 200);
    }
}
