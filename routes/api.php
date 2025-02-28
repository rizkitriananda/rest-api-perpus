<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function(){
    // auth
    Route::prefix('auth')->group(function(){
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware(['auth:sanctum'])->group(function(){
        Route::get('/auth/logout', [AuthController::class, 'logout']);

        // user
        Route::apiResource('/user', UserController::class);

        // books
        Route::apiResource('/books', BookController::class);

        // borrowed & retuend book
        Route::get('/borrowing/book', [BorrowingController::class, 'index']);
        Route::post('/borrowing/book', [BorrowingController::class, 'borrowing']);
        Route::put('/returned/book/{book_id}', [BorrowingController::class, 'returned']);
    });
});
 
