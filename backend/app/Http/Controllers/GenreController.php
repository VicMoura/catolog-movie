<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all(['id', 'name', 'tmdb_genre_id']);
        return response()->json([
            'data' => $genres
        ]);
    }
}
