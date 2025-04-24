<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/user/register', [AuthController::class, 'register_user']);
Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'login_admin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'get_all_user']);
    Route::get('/user/profile/{id}', [UserController::class, 'get_user_by_id']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/genre', [AdminController::class, 'add_genre']);
    Route::post('/admin/film', [AdminController::class, 'add_film']);
    Route::post('/admin/film/{id}/photos', [AdminController::class, 'add_film_photos']);
    Route::put('/admin/genre/{id}', [AdminController::class, 'edit_genre']);
    Route::put('/admin/film/{id}', [AdminController::class, 'edit_film']);
    Route::delete('/admin/film/{id}', [AdminController::class, 'delete_film']);
    Route::delete('/admin/genre/{id}', [AdminController::class, 'delete_genre']);
    Route::delete('/admin/film/photo/{id}', [AdminController::class, 'delete_film_photo']);
});

// route user
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'get_user_by_id']);
    Route::post('/user/list', [UserController::class, 'add_film_to_list']);
    Route::post('/user/review', [UserController::class, 'add_review']);
    Route::post('/user/reaction', [UserController::class, 'add_reaction_to_list']);
    Route::put('/user/review/{id}', [UserController::class, 'edit_review']);
    Route::put('/user/reaction/{id}', [UserController::class, 'edit_reaction']);
    Route::delete('/user/review/{id}', [UserController::class, 'delete_review']);
});
