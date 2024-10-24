<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});

Route::get('email/verify/{id}/{hash}', function (Request $request) {
    $request->fulfill();

    return redirect('/home')->with('verified', true);
})->name('verification.verify');

Route::get('/generate-invoice/{id}', [PayoutController::class, 'generateInvoice'])->name('view.invoice');
