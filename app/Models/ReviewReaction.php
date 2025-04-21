<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'review_id',
        'user_id',
        'is_like',
        'is_dislike',
    ];

    protected $casts = [
        'is_like' => 'boolean',
        'is_dislike' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
