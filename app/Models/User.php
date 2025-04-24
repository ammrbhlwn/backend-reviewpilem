<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'username',
        'display_name',
        'bio',
    ];

    protected $casts = [
        'role' => UserRole::class,
    ];

    public function filmLists()
    {
        return $this->belongsToMany(Film::class, 'user_film_lists')
            ->using(UserFilmList::class)
            ->withPivot('status_list')
            ->withTimestamps();
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewReactions()
    {
        return $this->hasMany(ReviewReaction::class);
    }
}
