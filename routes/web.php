<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LiveTrafficController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/live-traffic/{pppoe}', [LiveTrafficController::class, 'live']);
Route::get('/live-traffic/daily/{pppoe}', [LiveTrafficController::class, 'daily']);
Route::get('/live-traffic/monthly/{pppoe}', [LiveTrafficController::class, 'monthly']);