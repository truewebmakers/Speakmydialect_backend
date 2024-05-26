<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserMetaController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/get-languages', [UserMetaController::class, 'getLangauges'])->name('get-language');
Route::get('/get-countries', [UserMetaController::class, 'getCountries'])->name('get-country');
Route::get('/get-timezones', [UserMetaController::class, 'getTimezone'])->name('get-timzone');

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::put('/update/{id}', [UserMetaController::class, 'update'])->name('update');
    Route::get('/getProfile/{id}', [UserMetaController::class, 'getUserDetail'])->name('getProfile');

    Route::put('/update/skills/{id}', [UserMetaController::class, 'updateOrCreateSkills'])->name('update.skills');



});


