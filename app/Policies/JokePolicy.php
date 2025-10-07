<?php

namespace App\Policies;

use App\Models\Joke;
use App\Models\User;
use Illuminate\Auth\Access\Response;
class JokePolicy
{
//    /**
//     * Determine whether the user can view any models.
//     */
//    public function viewAny(User $user): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can view the model.
//     */
    public function view(User $user, Joke $joke): bool
    {
        return false;
    }
//
//    /**
//     * Determine whether the user can create models.
//     */
    public function create(User $user): bool
    {
        return false;
    }
//
//    /**
//     * Determine if the given user can update the joke.
//     */
//    public function update(User $user, Joke $joke): bool
//    {
//        // Admin can do anything
//        if ($user->hasRole('Administrator')) {
//            return true;
//        }
//
//        // Clients can only edit their own jokes
//        if ($user->hasRole('Client')) {
//            return $joke->user_id === $user->id;
//        }
//
//        // Staff: edit own jokes or assigned clients' jokes
//        if ($user->hasRole('Staff')) {
//            return $joke->user_id === $user->id
//                || $joke->user && $joke->user->staff_id === $user->id;
//        }
//
//        return false;
//    }
//
//    /**
//     * Determine if the given user can delete the joke.
//     */
//    public function delete(User $user, Joke $joke): bool
//    {
//        if ($user->hasRole('Admin')) {
//            return true;
//        }
//
//        if ($user->hasRole('Staff')) {
//            return $joke->user->staff_id === $user->id;
//        }
//
//        return $user->hasRole('Client') && $joke->user_id === $user->id;
//    }
//
//    /**
//     * Determine whether the user can restore the model.
//     */
    public function restore(User $user, Joke $joke): bool
    {
        return false;
    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     */
    public function forceDelete(User $user, Joke $joke): bool
    {
        return false;
    }

    public function edit(User $user, Joke $joke)
    {
        return $this->canModify($user, $joke);
    }

    public function update(User $user, Joke $joke)
    {
        return $this->canModify($user, $joke);
    }

    public function delete(User $user, Joke $joke)
    {
        return $this->canModify($user, $joke);
    }

    protected function canModify(User $user, Joke $joke)
    {
        // Admins can edit all
        if ($user->hasRole('Administrator')) {
            return true;
        }

        // Clients can only edit their own jokes
        if ($user->hasRole('Client')) {
            return $joke->user_id === $user->id;
        }

        // Staff can edit their own jokes
        if ($user->hasRole('Staff')) {
            // Their own joke
            if ($joke->user_id === $user->id) return true;

            // Joke belongs to one of their assigned clients
            return $user->clients()->where('id', $joke->user_id)->exists();
        }

        return false;
    }
}
