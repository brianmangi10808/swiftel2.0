<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LiveTrafficController;
use App\Livewire\Auth\OtpVerification;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/live-traffic/{pppoe}', [LiveTrafficController::class, 'live']);
Route::get('/live-traffic/daily/{pppoe}', [LiveTrafficController::class, 'daily']);
Route::get('/live-traffic/monthly/{pppoe}', [LiveTrafficController::class, 'monthly']);


Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');


Route::get('/admin/otp-verify', OtpVerification::class)
    ->name('auth.otp.verify')
    ->middleware('web');


Route::get('/admin/password-reset/reset', \App\Filament\Pages\Auth\ResetPassword::class)
    ->name('filament.admin.auth.password-reset.reset')
    ->middleware([
        \Filament\Http\Middleware\SetUpPanel::class . ':admin',
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Filament\Http\Middleware\AuthenticateSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Filament\Http\Middleware\DisableBladeIconComponents::class,
        \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
        \App\Http\Middleware\LogUserActivity::class,
        // ValidateSignature is intentionally removed
    ]);