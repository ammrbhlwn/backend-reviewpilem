<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\FilmPhoto;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function add_genre(Request $request)
    {
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
            'nama' => 'required|string',
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
        $fields = $request->validate([
            'judul' => 'required|string',
            'sinopsis' => 'required|string',
            'status_penayangan' => 'required|string|in:not_yet_aired,airing,finished_airing',
            'total_episode' => 'required|integer',
            'tanggal_rilis' => 'required|date',
            'id_genre' => 'required|array',
            'id_genre.*' => 'exists:genres,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $film = Film::create($fields);
            $film->genres()->attach($fields['id_genre']);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('film_photos', 'public');
                    $film->photos()->create(['photo' => $path]);
                }
            }

            //get url photo
            $photos = $film->photos->map(function ($photo) {
                return Storage::url($photo->photo);
            });

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
            'data' => [
                'id' => $film->id,
                'judul' => $film->judul,
                'sinopsis' => $film->sinopsis,
                'status_penayangan' => $film->status_penayangan,
                'total_episode' => $film->total_episode,
                'tanggal_rilis' => $film->tanggal_rilis,
                'genre_nama' => $film->genres->pluck('nama')->join(', '),
                'photos' => $photos,
            ]
        ], 200);
    }

    public function add_film_photos(Request $request, $id)
    {
        $film = Film::find($id);

        if (!$film) {
            return response()->json([
                'message' => 'Film not found'
            ], 404);
        }

        $validated = $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('film_photos', 'public');
                $film->photos()->create(['photo' => $path]);
                $photos[] = Storage::url($path);
            }

            return response()->json([
                'message' => 'Berhasil menambahkan foto',
                'data' => [
                    'id' => $film->id,
                    'judul' => $film->judul,
                    'film_photos' => $photos,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete_film_photo($photoId)
    {
        $photo = FilmPhoto::find($photoId);

        if (!$photo) {
            return response()->json([
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }

        try {
            if (Storage::disk('public')->exists($photo->photo)) {
                Storage::disk('public')->delete($photo->photo);
            }

            $photo->delete();
            return response()->json(['message' => 'Photo deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus foto',
                'error' => $e->getMessage()
            ], 500);
        }

        $photo->delete();
    }

    public function edit_film(Request $request, $id)
    {
        $fields = $request->validate([
            'judul' => 'nullable|string',
            'sinopsis' => 'nullable|string',
            'status_penayangan' => 'nullable|string',
            'total_episode' => 'nullable|integer',
            'tanggal_rilis' => 'nullable|date',
            'id_genre' => 'nullable|array',
            'id_genre.*' => 'exists:genres,id',
        ]);

        $film = Film::find($id);
        if (!$film) {
            return response()->json([
                'message' => 'Film not found'
            ], 404);
        };

        try {
            $updateData = collect($fields)->except(['id_genre'])->toArray();
            $film->update($updateData);

            if (isset($fields['id_genre'])) {
                $film->genres()->sync($fields['id_genre']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil mengubah film',
            'data' => [
                'id' => $film->id,
                'judul' => $film->judul,
                'sinopsis' => $film->sinopsis,
                'status_penayangan' => $film->status_penayangan,
                'total_episode' => $film->total_episode,
                'tanggal_rilis' => $film->tanggal_rilis,
                'genre_nama' => $film->genres->pluck('nama')->join(', '),
            ]
        ], 200);
    }

    public function delete_film($id)
    {
        $film = Film::find($id);

        if (!$film) {
            return response()->json([
                'message' => 'film not found'
            ], 404);
        };

        try {
            foreach ($film->photos as $photo) {
                if (Storage::disk('public')->exists($photo->photo)) {
                    Storage::disk('public')->delete($photo->photo);
                }
            }
            // delete
            $film->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Berhasil menghapus film',
        ], 200);
    }
}
