<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tmdb_genre_id'
    ];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }
}
