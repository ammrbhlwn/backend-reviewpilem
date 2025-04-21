<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatusList;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserFilmList extends Pivot
{
    protected $table = 'user_film_list';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'film_id',
        'status_list',
    ];

    protected $casts = [
        'status_list' => StatusList::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
