<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Api\v3\AuthController as AuthControllerV3;
use App\Http\Controllers\Api\v3\CategoryController as CategoryControllerV3;
use App\Http\Controllers\Api\v3\JokeController as JokeControllerV3;
use App\Http\Controllers\Api\v3\JokeReactionController as JokeReactionControllerV3;
use App\Http\Controllers\Api\v3\Admin\CategoryController as AdminCategoryControllerV3;
use App\Http\Controllers\Api\v3\Admin\UserController as AdminUserControllerV3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Version 3 Routes
 */

/**
 * User API Routes
 * - Register, Login (no authentication)
 * - Profile, Logout, User details (authentication required)
 */

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV3::class, 'register']);
        Route::post('login', [AuthControllerV3::class, 'login']);

        Route::get('profile', [AuthControllerV3::class, 'profile'])
            ->middleware(['auth:sanctum',]);
        Route::post('logout', [AuthControllerV3::class, 'logout'])
            ->middleware(['auth:sanctum',]);
        Route::put('edit', [AuthControllerV3::class, 'edit'])
            ->middleware(['auth:sanctum',]);
        Route::delete('delete', [AuthControllerV3::class, 'delete'])
            ->middleware(['auth:sanctum',]);

    });

// TODO: make admin/staff only
Route::prefix('admin')
    ->group(function () {
        Route::get('/', [AdminUserControllerV3::class, 'index']);
        Route::get('users', [AdminUserControllerV3::class, 'users'])->name('users');

        Route::prefix('category')
            ->group(function () {
                Route::resource("categories", AdminCategoryControllerV3::class);
            });
    });

/* Categories Routes ------------------------------------------------------ */
Route::get('categories/trash', [CategoryControllerV3::class, 'trash'])
    ->name('categories.trash');

Route::delete('categories/trash/empty', [CategoryControllerV3::class, 'removeAll'])
    ->name('categories.trash.remove.all');

Route::post('categories/trash/recover', [CategoryControllerV3::class, 'recoverAll'])
    ->name('categories.trash.recover.all');

Route::delete('categories/trash/{id}/remove', [CategoryControllerV3::class, 'removeOne'])
    ->name('categories.trash.remove.one');

Route::post('categories/trash/{id}/recover', [CategoryControllerV3::class, 'recoverOne'])
    ->name('categories.trash.recover.one');

/** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
Route::get('categories/trash/{id}/{method}', [CategoryControllerV3::class, 'trash']);

Route::resource("categories", CategoryControllerV3::class);

Route::post('categories/{category}/delete', [CategoryControllerV3::class, 'delete'])
    ->name('categories.delete');

Route::get('categories/{category}/delete', function () {
    return redirect()->route('admin.categories.index');
});

/* Jokes Routes ----------------------------------------------------- */

route::get('jokes/random', [JokeControllerV3::class, 'random']);
route::get('jokes/{categoryId}/all', [JokeControllerV3::class, 'jokesByCategory']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('jokes', JokeControllerV3::class);
});

Route::get('jokes/trash/all', [JokeControllerV3::class, 'trash'])->name('jokes.trash');
Route::post('jokes/trash/recover', [JokeControllerV3::class, 'recoverAll']);
Route::delete('jokes/trash/empty', [JokeControllerV3::class, 'removeAll']);
Route::post('jokes/trash/{id}/recover', [JokeControllerV3::class, 'recoverOne']);
Route::delete("jokes/trash/{id}/remove", [JokeControllerV3::class, 'removeOne']);



/* Joke Reaction Routes ---------------------------------------------- */

Route::post('jokes/{id}/react', [JokeReactionControllerV3::class, 'store'])->middleware(['auth:sanctum',]);
