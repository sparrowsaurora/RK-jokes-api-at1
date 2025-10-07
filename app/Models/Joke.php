<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Joke extends Model
{
    /** @use HasFactory<\Database\Factories\JokeFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions() {
        return $this->hasMany(JokeReaction::class);
    }

    public function likes() {
        return $this->hasMany(JokeReaction::class)->where('type', 'like');
    }

    public function dislikes() {
        return $this->hasMany(JokeReaction::class)->where('type', 'dislike');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_joke');
    }
}
