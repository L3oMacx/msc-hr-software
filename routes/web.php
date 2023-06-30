<?php

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\SystemSettings;
use App\Http\Livewire\UserOverview;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Users;
use App\Http\Controllers\UserController;
use App\Http\Livewire\UserTimeRecording;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome'); // Startseite
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () { // Authentifizierung erforderlich

    Route::get('/', Dashboard::class)->name('dashboard'); // Dashboard

    Route::get('/team', Users::class)->name('team'); // Übersicht aller Mitarbeitender

    Route::get('/settings', SystemSettings::class)->name('system-settings'); // Systemeinstellungen (Unternehmen, Abteilungen, Berechtigungen verwalten)

    Route::get('/user/{id}', UserOverview::class)->name('user-overview'); // Digitale Personalakte Übersicht
    Route::get('/user/{id}/timerecording', UserTimeRecording::class)->name('user-timerecording'); // Digitale Personalakte Zeiterfassungen


    Route::get('/user', UserOverview::class)->name('own-file'); // Eigene Digitale Personalakte Übersicht

});


