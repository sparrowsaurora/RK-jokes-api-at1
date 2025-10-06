<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JokeReaction extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'joke_id',
        'is_positive',
    ];

    public function joke() {
        return $this->belongsTo(Joke::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
