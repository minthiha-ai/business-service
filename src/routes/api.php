<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewCommentController;
use App\Http\Controllers\Api\BusinessReviewController;
use App\Http\Controllers\Api\BusinessProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile',     [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->group(function () {
    Route::resource('business-profiles', BusinessProfileController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('reviews-ratings', BusinessReviewController::class);
    Route::get('/reviews/{reviewId}/comments', [ReviewCommentController::class, 'index']);
    Route::post('/review-comments', [ReviewCommentController::class, 'store']);
    Route::put('/review-comments/{id}', [ReviewCommentController::class, 'update']);
    Route::delete('/review-comments/{id}', [ReviewCommentController::class, 'destroy']);
});
