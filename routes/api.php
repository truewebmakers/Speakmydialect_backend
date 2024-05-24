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

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::patch('/update/{id}', [UserMetaController::class, 'update'])->name('update');
    Route::patch('/update/skills/{id}', [UserMetaController::class, 'updateOrCreateSkills'])->name('update.skills');
    Route::post('/get-languages', [UserMetaController::class, 'getLangauges'])->name('get-language');
    Route::post('/get-countries', [UserMetaController::class, 'getCountries'])->name('get-language');
    Route::post('/get-timezones', [UserMetaController::class, 'getTimezone'])->name('get-language');


});


