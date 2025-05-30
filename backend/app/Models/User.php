<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{

    use HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favoriteMovies()
    {
        return $this->belongsToMany(Movie::class, 'favorite_movies')->withTimestamps();
    }

    public static function register($campos){

        $user = User::create([
            'name' => $campos['name'],
            'email' => $campos['email'],
            'password' => Hash::make($campos['password']),
        ]);

         return $user;
    }

    public static function login($campos){
        
        $user = User::where('email', $campos['email'])->first();

        if (! $user || ! Hash::check($campos['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Os dados informados estão incorretos.'],
            ]);
        }

        return $user; 
    }
}
