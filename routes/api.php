<?php

use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Support\ApiRoutes;
use App\Support\Middleware;

Route::middleware([Middleware::RESOLVE_USER])->group(function () {
    Route::post(ApiRoutes::PREFERENCES, [UserPreferenceController::class, 'store']);
    Route::get(ApiRoutes::PREFERENCES, [UserPreferenceController::class, 'show']);
    Route::delete(ApiRoutes::PREFERENCES, [UserPreferenceController::class, 'destroy']);

    Route::get(ApiRoutes::ARTICLES, [ArticleController::class, 'index']);
    Route::get(ApiRoutes::ARTICLES_META, [ArticleController::class, 'meta']);
});


Route::get('/test', function () {
    return 'ok';
});