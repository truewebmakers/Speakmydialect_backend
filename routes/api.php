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
    PayoutController,
    UserDocuemntController,
    TranslatorBankDetailsController,
    TranslatorAvailabilityController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/signup', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/send-email', [AuthController::class, 'sendEmail'])->name('send-email');
Route::post('/request-otp', [AuthController::class, 'requestOtp'])->name('request-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('email');
Route::post('/send-reset-link', [AuthController::class, 'sendResetLink'])->name('send-reset-link'); // Password Reset
Route::post('/resend-email-verification', [AuthController::class, 'resendVerificationEmail'])->name('resend-email-verification');



Route::post('/upload/temp-documents', [AuthController::class, 'uploadDocumentTemp'])->name('upload.temp.document');

Route::get('/get-languages', [UserMetaController::class, 'getLangauges'])->name('get-language');
Route::get('/get-countries', [UserMetaController::class, 'getCountries'])->name('get-country');
Route::get('/get-timezones', [UserMetaController::class, 'getTimezone'])->name('get-timzone');

Route::get('/translators/search', [SearchTranslatorsController::class, 'searchTranslators'])->name('search.translator.filter');
Route::get('/translators/profile-incomplete/{id}', [SearchTranslatorsController::class, 'ProfileIncomplete'])->name('search.translator.profile');

Route::get('/language/search/suggestion', [SearchTranslatorsController::class, 'searchTranslatorsSuggestions'])->name('search.language.suggestion');
Route::get('/get-profile/{uuid}', [SearchTranslatorsController::class, 'getUserProfile'])->name('get.user.profile');
Route::get('/get/skillsall/{column}', [UserMetaController::class, 'getSkillsAll'])->name('get.skills.all');
Route::get('/get/dailect/{language_id}', [UserMetaController::class, 'getDailectByLanguageId'])->name('get.skills.all');

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/update/lock/{id}', [AuthController::class, 'UpdateProfileLock'])->name('update.lock');

    Route::get('/get-all-users', [AuthController::class, 'getAllUsers'])->name('get.contact.form.entry');

    // Route::get('/get-profile-lock/{id}', [AuthController::class, 'GetProfileLockStatus'])->name('get.profile.lock.status');


    Route::get('/get-profile-admin/{id}', [SearchTranslatorsController::class, 'getUserProfile'])->name('get.user.admin.profile');
    Route::get('/users/get/list', [UserDocuemntController::class, 'getNewUserList'])->name('user.get.new.list');
    Route::post('/user/update/status/{id}', [UserDocuemntController::class, 'UpdateUserStatus'])->name('user.update.new.list');
    Route::post('/user/delete/{id}', [UserDocuemntController::class, 'destroy'])->name('user.delete');

    Route::get('/get-contactform-entries', [AuthController::class, 'FetchContactFormEntires'])->name('get.contact.form.entry');

    Route::get('/user/get/docuemnts/{id}', [UserDocuemntController::class, 'getdocumentsOfUser'])->name('user.get.document');
    Route::get('/user/get/dashboard/count', [BookingController::class, 'BookingCounts'])->name('user.get.document.count');
    Route::get('/user/get/approved/bookings', [BookingController::class, 'getApprovedBookings'])->name('user.get.approved');
    Route::post('/user/status/approved/bookings/{id}', [BookingController::class, 'ApprovedBookingPaidStatus'])->name('user.status.paid');

    Route::post('/update/{id}', [UserMetaController::class, 'update'])->name('update');
    Route::get('/getProfile/{id}', [UserMetaController::class, 'getUserDetail'])->name('getProfile');

    Route::post('/update/password/{id}', [AuthController::class, 'UpdatePassword'])->name('update.password');
    Route::post('/update/skills/{id}', [UserMetaController::class, 'updateOrCreateSkills'])->name('update.skills');
    Route::get('/get/skills/{id}', [UserMetaController::class, 'getSkills'])->name('get.skills');
    Route::post('/delete/skills/{id}', [UserMetaController::class, 'DeleteSkill'])->name('delete.skills');


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
    Route::post('/booking/update/translator/{booking_id}/{status}', [BookingController::class, 'updateTranslatorStatus'])->name('booking.update.status');
    Route::post('/booking/add', [BookingController::class, 'store'])->name('booking.add');
    Route::post('/booking/delete/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
    // Booking Apis end

    Route::post('/payout/charge', [PayoutController::class, 'createCharge'])->name('payout.crete');
    Route::get('/payout/invoices/{id}', [PayoutController::class, 'getInvoice'])->name('payout.invoice');

    Route::get('/translator/get/bank/{user_id}', [TranslatorBankDetailsController::class, 'index'])->name('translator.get');
    Route::post('/translator/bank/store', [TranslatorBankDetailsController::class, 'store'])->name('translator.store');
    Route::post('/translator/bank/update/{id}', [TranslatorBankDetailsController::class, 'update'])->name('translator.update');
    Route::post('/translator/bank/delete/{id}', [TranslatorBankDetailsController::class, 'destroy'])->name('translator.delete');


    Route::post('/translator/availability', [TranslatorAvailabilityController::class, 'store']);
    Route::get('/translator/availability/{translatorId}', [TranslatorAvailabilityController::class, 'index']);
    Route::post('/translator/availability/get-slots', [TranslatorAvailabilityController::class, 'getSlots']);

});

