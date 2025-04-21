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
            'nama' => 'required|string',
        ]);

        try {
            $genre = Genre::create($fields);

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

        $genre = Genre::find($id);

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
            'status_penayangan' => 'required|string',
            'total_episode' => 'required|integer',
            'tanggal_rilis' => 'required|date',
            'id_genre' => 'required|exists:genres,id',
        ]);

        try {
            $film = Film::create(
                [
                    'judul' => $fields['judul'],
                    'sinopsis' => $fields['sinopsis'],
                    'status_penayangan' => $fields['status_penayangan'],
                    'total_episode' => $fields['total_episode'],
                    'tanggal_rilis' => $fields['tanggal_rilis'],
                ]
            );

            $film->genres()->attach($fields['id_genre']);

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
            'data' => $film->load('genres')
        ], 200);
    }

    public function edit_film(Request $request, $id)
    {
        $fields = $request->validate([
            'judul' => 'nullable|string',
            'sinopsis' => 'nullable|string',
            'status_penayangan' => 'nullable|string',
            'total_episode' => 'nullable|integer',
            'tanggal_rilis' => 'nullable|date',
            'id_genre' => 'nullable|exists:genres,id',
        ]);

        $film = Film::find($id);
        if (!$film) {
            return response()->json([
                'message' => 'Film not found'
            ], 404);
        };

        try {
            // update
            $film->update($fields);

            if (isset($fields['id_genre'])) {
                $film->genres()->sync([$fields['id_genre']]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil mengubah film',
            'data' => $film->load('genres')
        ], 200);
    }
}
