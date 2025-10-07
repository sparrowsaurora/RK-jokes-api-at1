<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Joke;
use App\Models\JokeReaction;
class JokeReactionController extends Controller
{
    public function store(Request $request, Joke $joke)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        JokeReaction::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'joke_id' => $joke->id,
            ],
            [
                'type' => $request->type,
            ]
        );

        return back();
    }

    public function destroy(Joke $joke)
    {
        JokeReaction::where('user_id', auth()->id())
            ->where('joke_id', $joke->id)
            ->delete();

        return back();
    }
}
