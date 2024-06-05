<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserMetaController,
    UserWorkExperienceController,
    SearchTranslatorsController,
    UserEductionController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/get-languages', [UserMetaController::class, 'getLangauges'])->name('get-language');
Route::get('/get-countries', [UserMetaController::class, 'getCountries'])->name('get-country');
Route::get('/get-timezones', [UserMetaController::class, 'getTimezone'])->name('get-timzone');

Route::get('/translators/search', [SearchTranslatorsController::class, 'searchTranslators'])->name('search.translator.filter');
Route::get('/language/search/suggestion', [SearchTranslatorsController::class, 'searchTranslatorsSuggestions'])->name('search.language.suggestion');






Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::post('/update/{id}', [UserMetaController::class, 'update'])->name('update');
    Route::get('/getProfile/{id}', [UserMetaController::class, 'getUserDetail'])->name('getProfile');

    Route::post('/update/password/{id}', [AuthController::class, 'UpdatePassword'])->name('update.password');

    Route::post('/update/skills/{id}', [UserMetaController::class, 'updateOrCreateSkills'])->name('update.skills');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Work experience Apis started
    Route::get('/experience/get/{user_id}', [UserWorkExperienceController::class, 'getWorkExperience'])->name('experience.get');
    Route::post('/experience/add/{user_id}', [UserWorkExperienceController::class, 'store'])->name('experience.add');
    Route::post('/experience/update/{id}', [UserWorkExperienceController::class, 'update'])->name('experience.update');
    Route::post('/experience/delete/{id}', [UserWorkExperienceController::class, 'destroy'])->name('experience.destroy');
    // Work experience Apis end

    // Education Apis started
    Route::get('/education/get/{user_id}', [UserEductionController::class, 'getWorkExperience'])->name('experience.get');
    Route::post('/education/add/{user_id}', [UserEductionController::class, 'store'])->name('experience.add');
    Route::post('/education/update/{id}', [UserEductionController::class, 'update'])->name('experience.update');
    Route::post('/education/delete/{id}', [UserEductionController::class, 'destroy'])->name('experience.destroy');
    // Education Apis end


});


