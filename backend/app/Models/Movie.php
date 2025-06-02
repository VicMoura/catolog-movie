<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'poster_path',
        'release_date',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_movies')->withTimestamps();
    }
}
