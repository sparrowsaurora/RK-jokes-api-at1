<?php

use App\Http\Controllers\Api\v3\AuthController as AuthControllerV3;
use App\Http\Controllers\Api\v3\CategoryController as CategoryControllerV3;
use App\Http\Controllers\Api\v3\JokeController as JokeControllerV3;
use App\Http\Controllers\Api\v3\JokeReactionController as JokeReactionControllerV3;
use App\Http\Controllers\Api\v3\UserController as UserControllerV3;
use App\Http\Controllers\Api\v3\RoleController as RoleControllerV3;
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
//// $roles = ['super-user', 'admin', 'staff', 'client'];
// Route::middleware('role:admin|staff|client')->group(function () {});
// Route::middleware('role:super-user|admin|staff')->group(function () {});
// ->middleware(['auth:sanctum',]);
// ->middleware(['auth:sanctum', 'role:super-user|admin|staff']);
// ->middleware('role:super-user|admin|staff');

/* Auth Controller Routes ------------------------------------------------------ */
Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV3::class, 'register']);
        Route::post('login', [AuthControllerV3::class, 'login']);

        Route::middleware(['role:super-user|admin|staff|client', 'auth:sanctum'])->group(function () {
            Route::get('profile', [AuthControllerV3::class, 'profile']);
            Route::post('logout', [AuthControllerV3::class, 'logout']);
            Route::put('edit', [AuthControllerV3::class, 'edit']);
            Route::delete('delete', [AuthControllerV3::class, 'delete']);
        });
    });

/* Admin - User Controller Routes ---------------------------------------------------- */
Route::middleware('role:super-user|admin|staff')->group(function () {
    Route::prefix('admin')
        ->group(function () {
            Route::get('/', [UserControllerV3::class, 'adminDashboard']);
            Route::prefix('users')
                ->group(function () {
                    Route::get('/', [UserControllerV3::class, 'index'])->name('users.index');
                    // no store method needed
                    Route::get('{user}', [UserControllerV3::class, 'show'])->name('users.show');
                    Route::put('{user}', [UserControllerV3::class, 'update'])->name('users.update');
                    Route::delete('{user}', [UserControllerV3::class, 'destroy'])->name('users.destroy');

                    //Route::get('/', [AdminUserControllerV3::class, 'users'])->name('users');
                    Route::delete('{user}/jokes', [UserControllerV3::class, 'removeJokes']);
                    Route::post('suspend/{userId}', [UserControllerV3::class, 'suspendUser']);
                    Route::post('unsuspend/{userId}', [UserControllerV3::class, 'unsuspendUser']);
                    Route::post('search', [UserControllerV3::class, 'search']);
                    Route::get('logout/{id}', [UserControllerV3::class, 'logoutUser']);
                    Route::post('assign-role/{userId}', [UserControllerV3::class, 'assignRole']);

                    Route::delete('{id}/votes', [UserControllerV3::class, 'removeVotes'])->middleware('role:super-user|admin');
                });
            Route::prefix('roles')
                ->group(function () {
                    Route::middleware('role:super-user|admin')->group(function () {
                        Route::get('/', [RoleControllerV3::class, 'index'])->name('roles.index');
                        Route::post('/', [RoleControllerV3::class, 'store'])->name('roles.store');
                        Route::get('{role}', [RoleControllerV3::class, 'show'])->name('roles.show');
                        Route::put('{role}', [RoleControllerV3::class, 'update'])->name('roles.update');
                        Route::delete('{role}', [RoleControllerV3::class, 'destroy'])->name('roles.destroy');
                        Route::get('logout/{id}', [RoleControllerV3::class, 'logoutRole']);
                    });
                });
        });
});


/* Categories Controller Routes ------------------------------------------------------ */
Route::middleware('role:super-user|admin|staff')->group(function () {
//    Route::resource("categories", CategoryControllerV3::class);
    Route::prefix('categories')
        ->group(function () {
            Route::post('search', [CategoryControllerV3::class, 'search']);
            Route::get('/', [CategoryControllerV3::class, 'index'])->name('category.index');
            Route::get('{category}', [CategoryControllerV3::class, 'show'])->name('category.show');
            Route::middleware('role:super-user|admin')
                ->group(function () {
                    Route::post('/', [CategoryControllerV3::class, 'store'])->name('category.store');
                    Route::put('{category}', [CategoryControllerV3::class, 'update'])->name('category.update');
                    Route::delete('{category}', [CategoryControllerV3::class, 'destroy'])->name('category.destroy');

                    Route::prefix('trash')
                        ->group(function () {
                            Route::get('/', [CategoryControllerV3::class, 'trash']);
                            Route::delete('empty', [CategoryControllerV3::class, 'removeAll']);
                            Route::post('recover', [CategoryControllerV3::class, 'recoverAll']);
                            Route::delete('{id}/remove', [CategoryControllerV3::class, 'removeOne']);
                            Route::post('{id}/recover', [CategoryControllerV3::class, 'recoverOne']);
                            /** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
                            Route::get('{id}/{method}', [CategoryControllerV3::class, 'trash']);
                        });
                });
        });
});

/* Jokes Controller Routes ----------------------------------------------------- */

/* TODO: Joke Perms                                        */

Route::get('jokes/random', [JokeControllerV3::class, 'random']);

Route::middleware(['role:super-user|admin|staff|client', 'auth:sanctum',])->group(function () {
    Route::prefix('jokes')
        ->group(function () {
            /* Joke Reaction Route */
            Route::post('{id}/react', [JokeReactionControllerV3::class, 'store']);

            Route::get('{categoryId}/all', [JokeControllerV3::class, 'jokesByCategory']);

            // Replacement of Route::resource('jokes', JokeControllerV3::class);
            Route::get('/', [JokeControllerV3::class, 'index'])->name('jokes.index');
            Route::get('{joke}', [JokeControllerV3::class, 'show'])->name('jokes.show');

            Route::post('/', [JokeControllerV3::class, 'store'])->name('jokes.store');

            Route::put('{joke}', [JokeControllerV3::class, 'update'])->name('jokes.update');
            Route::delete('{joke}', [JokeControllerV3::class, 'destroy'])->name('jokes.destroy');

            Route::middleware('role:super-user|admin|staff')
                ->group(function () {
                    Route::prefix('trash')
                        ->group(function () {
                            Route::get('/', [JokeControllerV3::class, 'trash'])->name('jokes.trash');
                            Route::post('recover', [JokeControllerV3::class, 'recoverAll']);
                            Route::delete('empty', [JokeControllerV3::class, 'removeAll']);
                            Route::post('{id}/recover', [JokeControllerV3::class, 'recoverOne']);
                            Route::delete("{id}/remove", [JokeControllerV3::class, 'removeOne']);
                        });
                });
        });
});
