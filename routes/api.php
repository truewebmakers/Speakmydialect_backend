<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserMetaController,
    UserWorkExperienceController,
    SearchTranslatorsController,
    UserEductionController,
    BookingController,
    UserDocuemntController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/upload/temp-documents', [AuthController::class, 'uploadDocumentTemp'])->name('upload.temp.document');

Route::get('/get-languages', [UserMetaController::class, 'getLangauges'])->name('get-language');
Route::get('/get-countries', [UserMetaController::class, 'getCountries'])->name('get-country');
Route::get('/get-timezones', [UserMetaController::class, 'getTimezone'])->name('get-timzone');

Route::get('/translators/search', [SearchTranslatorsController::class, 'searchTranslators'])->name('search.translator.filter');
Route::get('/language/search/suggestion', [SearchTranslatorsController::class, 'searchTranslatorsSuggestions'])->name('search.language.suggestion');
Route::get('/get-profile/{uuid}', [SearchTranslatorsController::class, 'getUserProfile'])->name('get.user.profile');






Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::get('/get-profile-admin/{id}', [SearchTranslatorsController::class, 'getUserProfile'])->name('get.user.admin.profile');


    Route::get('/users/get/list', [UserDocuemntController::class, 'getNewUserList'])->name('user.get.new.list');
    Route::post('/user/update/status/{id}', [UserDocuemntController::class, 'UpdateUserStatus'])->name('user.update.new.list');
    Route::get('/user/get/docuemnts/{id}', [UserDocuemntController::class, 'getdocumentsOfUser'])->name('user.get.document');




    Route::post('/update/{id}', [UserMetaController::class, 'update'])->name('update');
    Route::get('/getProfile/{id}', [UserMetaController::class, 'getUserDetail'])->name('getProfile');

    Route::post('/update/password/{id}', [AuthController::class, 'UpdatePassword'])->name('update.password');



    Route::post('/update/skills/{id}', [UserMetaController::class, 'updateOrCreateSkills'])->name('update.skills');
    Route::get('/get/skills/{id}', [UserMetaController::class, 'getSkills'])->name('get.skills');
    Route::post('/delete/skills/{id}', [UserMetaController::class, 'DeleteSkill'])->name('delete.skills');


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Work experience Apis started
    Route::get('/experience/get/{user_id}', [UserWorkExperienceController::class, 'getWorkExperience'])->name('experience.get');
    Route::post('/experience/add/{user_id}', [UserWorkExperienceController::class, 'store'])->name('experience.add');
    Route::post('/experience/update/{id}', [UserWorkExperienceController::class, 'update'])->name('experience.update');
    Route::post('/experience/delete/{id}', [UserWorkExperienceController::class, 'destroy'])->name('experience.destroy');
    // Work experience Apis end

    // Education Apis started
    Route::get('/education/get/{user_id}', [UserEductionController::class, 'getWorkExperience'])->name('education.get');
    Route::post('/education/add/{user_id}', [UserEductionController::class, 'store'])->name('education.add');
    Route::post('/education/update/{id}', [UserEductionController::class, 'update'])->name('education.update');
    Route::post('/education/delete/{id}', [UserEductionController::class, 'destroy'])->name('education.destroy');
    // Education Apis end

    // Booking Apis started
    Route::get('/payout/get/translator/{translator_id}', [BookingController::class, 'getPayoutBooking'])->name('booking.get');
    Route::get('/booking/get/client/{client_id}/{status}', [BookingController::class, 'getBookingForClient'])->name('booking.get.client');
    Route::get('/booking/get/translator/{translator_id}/{status}', [BookingController::class, 'getBookingForTranslator'])->name('booking.get.translator');
    Route::post('/booking/update/client/{booking_id}/{status}', [BookingController::class, 'updateClientStatus'])->name('booking.update');
    Route::post('/booking/update/translator/{booking_id}/{status}', [BookingController::class, 'updateTranslatorStatus'])->name('booking.update');
    Route::post('/booking/add', [BookingController::class, 'store'])->name('booking.add');
    Route::post('/booking/delete/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
    // Booking Apis end



});
