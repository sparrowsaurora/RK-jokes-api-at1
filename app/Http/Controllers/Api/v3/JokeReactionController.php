<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\JokeReaction;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;

class JokeReactionController extends Controller
{
    public function store(Request $request, string $id)
    {
        $validated = $request->validate([
            'is_positive' => ['required', 'boolean'],
        ]);

        $userId = $request->user()->id;
        $isPositive = $validated['is_positive'];

        $existing = JokeReaction::where('user_id', $userId)
            ->where('joke_id', $id)
            ->first();

        if (!$existing) {
            // No reaction yet — create one
            $reaction = JokeReaction::create([
                'user_id' => $userId,
                'joke_id' => $id,
                'is_positive' => $isPositive,
            ]);

            return ApiResponse::success($reaction, 'Reaction added');
        }

        if ($existing->is_positive === $isPositive) {
            // Same reaction → remove it (toggle off)
            $existing->delete();
            return ApiResponse::success([], 'Reaction removed');
        }

        // Different reaction → update
        $existing->update(['is_positive' => $isPositive]);
        return ApiResponse::success($existing, 'Reaction updated');

    }
}
