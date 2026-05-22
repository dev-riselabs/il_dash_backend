<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;



Route::get('/', function (): JsonResponse {
    return response()->json([
        'message' => 'Eventsintel API',
        'data' => [
            'status' => 'ok',
            'app' => config('app.name'),
            'health' => url('/api/v1/health'),
        ],
    ]);
});

// Define login route for redirect fallback (API should use /api/auth/login)
Route::get('/login', function () {
    return response()->json(['error' => 'Unauthenticated'], 401);
})->name('login');