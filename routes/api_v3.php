<?php

use App\Http\Controllers\Api\v3\AuthController as AuthControllerV3;
use App\Http\Controllers\Api\v3\CategoryController as CategoryControllerV3;
use App\Http\Controllers\Api\v3\JokeController as JokeControllerV3;
use App\Http\Controllers\Api\v3\JokeReactionController as JokeReactionControllerV3;
use App\Http\Controllers\Api\v3\UserController as UserControllerV3;
use Illuminate\Support\Facades\Route;

/**
 * API Version 3 Routes
 */

/**
 * User API Routes
 * - Register, Login (no authentication)
 * - Profile, Logout, User details (authentication required)
 */

//routing stuff holder
// Route::middleware('role:Client|Staff|Administrator')->group(function () {
//  Route::middleware('role:Administrator|Staff')->group(function () {

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV3::class, 'register']);
        Route::post('login', [AuthControllerV3::class, 'login']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('profile', [AuthControllerV3::class, 'profile']);
            Route::post('logout', [AuthControllerV3::class, 'logout']);
            Route::put('edit', [AuthControllerV3::class, 'edit']);
            Route::delete('delete', [AuthControllerV3::class, 'delete']);
        });
    });

// TODO: make admin/staff only
Route::prefix('admin')
    ->group(function () {
        Route::get('/', [UserControllerV3::class, 'adminDashboard']);
        Route::resource('users', UserControllerV3::class);
        Route::prefix('users')->group(function () {
            //Route::get('/', [AdminUserControllerV3::class, 'users'])->name('users');
            Route::post('suspend/{userId}', [UserControllerV3::class, 'suspendUser']);
            Route::post('unsuspend/{userId}', [UserControllerV3::class, 'unsuspendUser']);
            Route::post('search', [UserControllerV3::class, 'search']);
        });
    });

/* Categories Routes ------------------------------------------------------ */
Route::resource("categories", CategoryControllerV3::class);
Route::prefix('categories')->group(function () {
    Route::prefix('trash')->group(function () {
        /** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
        Route::get('{id}/{method}', [CategoryControllerV3::class, 'trash']);
        Route::get('/', [CategoryControllerV3::class, 'trash']);
        Route::delete('empty', [CategoryControllerV3::class, 'removeAll']);
        Route::post('recover', [CategoryControllerV3::class, 'recoverAll']);
        Route::delete('{id}/remove', [CategoryControllerV3::class, 'removeOne']);
        Route::post('{id}/recover', [CategoryControllerV3::class, 'recoverOne']);
        /** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
        Route::get('{id}/{method}', [CategoryControllerV3::class, 'trash']);
    });

    Route::post('search', [CategoryControllerV3::class, 'search']);
});

/* Jokes Routes ----------------------------------------------------- */
Route::resource('jokes', JokeControllerV3::class)->middleware(['auth:sanctum',]);
Route::prefix('jokes')->group(function () {
    Route::get('random', [JokeControllerV3::class, 'random']);
    Route::get('{categoryId}/all', [JokeControllerV3::class, 'jokesByCategory']);

    Route::prefix('trash')->group(function () {
        Route::get('/', [JokeControllerV3::class, 'trash'])->name('jokes.trash');
        Route::post('recover', [JokeControllerV3::class, 'recoverAll']);
        Route::delete('empty', [JokeControllerV3::class, 'removeAll']);
        Route::post('{id}/recover', [JokeControllerV3::class, 'recoverOne']);
        Route::delete("{id}/remove", [JokeControllerV3::class, 'removeOne']);
    });

    /* Joke Reaction Route */
    Route::post('{id}/react', [JokeReactionControllerV3::class, 'store'])->middleware(['auth:sanctum',]);
});
