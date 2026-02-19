<?php

use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['resolve.user'])->group(function () {

    Route::post('/preferences', [UserPreferenceController::class, 'store']);
    Route::get('/preferences', [UserPreferenceController::class, 'show']);
    Route::delete('/preferences', [UserPreferenceController::class, 'destroy']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/meta', [ArticleController::class, 'meta']);
});


Route::get('/test', function () {
    return 'ok';
});