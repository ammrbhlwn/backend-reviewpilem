<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'film';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'judul',
        'sinopsis',
        'gambar',
        'status_penayangan',
        'total_episode',
        'tanggal_rilis',
        'id_genre',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(FilmPhoto::class);
    }
}
