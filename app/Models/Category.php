<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function jokes()
    {
        return $this->belongsToMany(Joke::class, 'category_joke');
    }

}
