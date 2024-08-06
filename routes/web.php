<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-invoice', [PayoutController::class, 'generateInvoice'])->name('view.invoice');
