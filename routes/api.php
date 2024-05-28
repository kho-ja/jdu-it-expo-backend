<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookCodesController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentBookController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::get('/test', [MainController::class, 'test']);
Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->name('unauthorized');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/homepage', [MainController::class, 'homepage']);
    Route::get('get-profile-info', [ProfileController::class, 'getInfo']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('reset-password', [ProfileController::class, 'resetPassword']);
    Route::post('reset-image', [ProfileController::class, 'updateImage']);

    Route::middleware(['role:librarian'])->group(function () {
        Route::group(['prefix' => 'library'], function() {
            Route::group(['prefix' => 'rents'], function () {
//                Route::get('all', [RentBookController::class, 'index']);
//                Route::post('info', [RentBookController::class, 'rentInfo']);
                Route::post('create', [RentBookController::class, 'rentBook']);
                Route::post('close', [RentBookController::class, 'returnBook']);
                Route::post('expires', [RentBookController::class, 'expireRents']);
//                Route::post('return/info', [RentBookController::class, 'returnInfo']);
//                Route::post('lost/{id}', [RentBookController::class, 'lostBook']);
//                Route::get('archive', [RentBookController::class, 'archive']);
//                Route::get('lost-books', [RentBookController::class, 'lostBooks']);
            });
            Route::resource('books', BookController::class);
//            Route::post('add-book', [BookController::class, 'addBook']);
//            Route::post('single-book/lost/{id}', [BookCodesController::class, 'lost']);
//            Route::post('single-book/delete/{id}', [BookCodesController::class, 'delete']);
            Route::resource('categories', BookCategoryController::class);
//            Route::get('stats' , [StatsController::class, 'statsBook']);
        });
        Route::group(['prefix' => 'users'], function () {
            Route::resource('students', StudentController::class);
        });
    });

    Route::middleware(['role:student'])->group(function () {
        Route::get('student/rents', [StudentController::class, 'getRents']);
        Route::post('student/return', [StudentController::class, 'return']);
    });
});

Route::fallback(function () {
    return response()->json([
        'error' => 'Route not found.'
    ], 404);
});
