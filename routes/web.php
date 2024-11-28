<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\EmailVerificationNotification;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\{  AuthController};

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');


Route::get('/generate-invoice/{id}', [PayoutController::class, 'generateInvoice'])->name('view.invoice');


// Auth::routes(['reset' => true]);

Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');


