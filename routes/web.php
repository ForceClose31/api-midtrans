<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkout', [MidtransController::class, 'checkout']);
Route::post('/process', [MidtransController::class, 'process']);
