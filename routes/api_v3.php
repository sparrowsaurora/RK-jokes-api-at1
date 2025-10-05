<?php

use App\Http\Controllers\Api\v3\AuthController as AuthControllerV3;
use App\Http\Controllers\Api\v3\CategoryController as CategoryControllerV3;
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
