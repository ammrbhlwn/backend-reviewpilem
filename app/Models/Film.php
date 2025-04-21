<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusPenayangan;

class Film extends Model
{
    protected $fillable = [
        'judul',
        'sinopsis',
        'status_penayangan',
        'total_episode',
        'tanggal_rilis',
    ];

    protected $casts = [
        'status_penayangan' => StatusPenayangan::class,
        'tanggal_rilis' => 'date',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'film_genre');
    }

    public function photos()
    {
        return $this->hasMany(FilmPhoto::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function filmLists()
    {
        return $this->hasMany(UserFilmList::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_film_list')
            ->using(UserFilmList::class)
            ->withPivot('status_list')
            ->withTimestamps();
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
}
