<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteMovie extends Pivot
{
    use HasFactory;

    protected $table = 'favorite_movies';

    protected $fillable = [
        'user_id',
        'movie_id',
    ];
}
