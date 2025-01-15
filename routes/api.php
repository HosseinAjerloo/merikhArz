<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('verify-fastPayment', [App\Http\Controllers\Panel\TransmissionController::class, 'VerifyFastPayment'])->name('panel.verify.fast.payment');
