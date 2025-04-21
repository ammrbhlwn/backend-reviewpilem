<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmPhoto extends Model
{
    protected $fillable = [
        'film_id',
        'photo'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
