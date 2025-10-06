<?php

namespace App\Models;

use Database\Factories\JokeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Joke extends Model
{
    /** @use HasFactory<JokeFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'jokes';

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'published_at',
    ];

    protected $appends = ['positive_count', 'negative_count'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function reactions()
    {
        return$this->hasMany(JokeReaction::class);
    }

    public function positiveReactions()
    {
        return $this->reactions()->where('is_positive', true);
    }

    public function negativeReactions()
    {
        return $this->reactions()->where('is_positive', false);
    }

    public function getPositiveCountAttribute()
    {
        return $this->positiveReactions()->count();
    }

    public function getNegativeCountAttribute()
    {
        return $this->negativeReactions()->count();
    }
}
