<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\EmailVerificationNotification;
use App\Http\Controllers\Auth\VerificationController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');


// Route::get('email/verify/{id}/{hash}', function (Request $request) {
//     $request->fulfill();

//     return redirect('/home')->with('verified', true);
// })->name('verification.verify');

Route::get('/generate-invoice/{id}', [PayoutController::class, 'generateInvoice'])->name('view.invoice');
