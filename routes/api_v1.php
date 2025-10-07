<?php

use App\Http\Controllers\Api\v1\AuthController as AuthControllerV1;
use App\Http\Controllers\Api\v1\CategoryController as CategoryControllerV1;
use App\Http\Controllers\Api\v1\JokeController as JokeControllerV1;
use App\Http\Controllers\Api\v1\JokeReactionController as JokeReactionControllerV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Version 1 Routes
 */

/**
 * User API Routes
 * - Register, Login (no authentication)
 * - Profile, Logout, User details (authentication required)
 */

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV1::class, 'register']);
        Route::post('login', [AuthControllerV1::class, 'login']);

        Route::get('profile', [AuthControllerV1::class, 'profile'])
            ->middleware(['auth:sanctum',]);
        Route::post('logout', [AuthControllerV1::class, 'logout'])
            ->middleware(['auth:sanctum',]);

    });

/* Categories Routes ------------------------------------------------------ */
Route::get('categories/trash', [CategoryControllerV1::class, 'trash'])
    ->name('categories.trash');

Route::delete('categories/trash/empty', [CategoryControllerV1::class, 'removeAll'])
    ->name('categories.trash.remove.all');

Route::post('categories/trash/recover', [CategoryControllerV1::class, 'recoverAll'])
    ->name('categories.trash.recover.all');

Route::delete('categories/trash/{id}/remove', [CategoryControllerV1::class, 'removeOne'])
    ->name('categories.trash.remove.one');

Route::post('categories/trash/{id}/recover', [CategoryControllerV1::class, 'recoverOne'])
    ->name('categories.trash.recover.one');

/** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
Route::get('categories/trash/{id}/{method}', [CategoryControllerV1::class, 'trash']);

Route::resource("categories", CategoryControllerV1::class);

Route::post('categories/{category}/delete', [CategoryControllerV1::class, 'delete'])
    ->name('categories.delete');

Route::get('categories/{category}/delete', function () {
    return redirect()->route('admin.categories.index');
});

/* Jokes Routes ----------------------------------------------------- */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('jokes', JokeControllerV1::class);
});
Route::get('jokes/trash/all', [JokeControllerV1::class, 'trash'])->name('jokes.trash');
Route::post('jokes/trash/recover', [JokeControllerV1::class, 'recoverAll']);
Route::delete('jokes/trash/empty', [JokeControllerV1::class, 'removeAll']);
Route::post('jokes/trash/{id}/recover', [JokeControllerV1::class, 'recoverOne']);
Route::delete("jokes/trash/{id}/remove", [JokeControllerV1::class, 'removeOne']);



/* Joke Reaction Routes ---------------------------------------------- */

Route::post('jokes/{id}/react', [JokeReactionControllerV1::class, 'store'])->middleware(['auth:sanctum',]);
