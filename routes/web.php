<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

Route::view('/', 'checkout');
Route::post('/process', [MidtransController::class, 'process']);
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
Route::post('/core-api/charge', [MidtransController::class, 'chargeViaCoreApi']);
