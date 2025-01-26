<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('verify-fastPayment', [App\Http\Controllers\Panel\TransmissionController::class, 'VerifyFastPayment'])->name('panel.verify.fast.payment');
Route::post('find-user', [App\Http\Controllers\Panel\UserController::class, 'findUser'])->name('find.user.api');
Route::post('send-message', [App\Http\Controllers\Panel\UserController::class, 'sendMessage'])->name('find.user.api');
