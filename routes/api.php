<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes defined by version in separate files
 *
 * Version  Route File Location
 * V1       routes/api_v1.php
 */

/**
 * Include API Version 1 Route
 */
require __DIR__ . '/api_v1.php';

/**
 * Fallback route for any routes that are not defined
 * Result 404
 */
Route::fallback(static function () {
    return Response::json([
        ['error' => "OOPS!"]
    ], 404);
});
