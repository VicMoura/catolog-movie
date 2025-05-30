<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FavoriteMovie extends Pivot
{
    protected $table = 'favorite_movies';

    protected $fillable = [
        'user_id',
        'movie_id',
    ];
}
