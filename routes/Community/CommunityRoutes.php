<?php

use App\Http\Controllers\Community\CommentController;
use App\Http\Controllers\Community\PostController;
use App\Http\Controllers\Community\ReactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('community')->group(function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);

    Route::get('posts/{postId}', [PostController::class, 'show'])
        ->where('postId', '[a-f0-9]{24}');
    Route::put('posts/{postId}', [PostController::class, 'update'])
        ->where('postId', '[a-f0-9]{24}');
    Route::delete('posts/{postId}', [PostController::class, 'destroy'])
        ->where('postId', '[a-f0-9]{24}');

    Route::get('posts/{postId}/comments', [CommentController::class, 'index'])
        ->where('postId', '[a-f0-9]{24}');
    Route::post('posts/{postId}/comments', [CommentController::class, 'store'])
        ->where('postId', '[a-f0-9]{24}');

    Route::put('comments/{commentId}', [CommentController::class, 'update'])
        ->where('commentId', '[a-f0-9]{24}');
    Route::delete('comments/{commentId}', [CommentController::class, 'destroy'])
        ->where('commentId', '[a-f0-9]{24}');

    Route::post('posts/{postId}/reactions', [ReactionController::class, 'store'])
        ->where('postId', '[a-f0-9]{24}');
    Route::delete('posts/{postId}/reactions', [ReactionController::class, 'destroy'])
        ->where('postId', '[a-f0-9]{24}');
});
