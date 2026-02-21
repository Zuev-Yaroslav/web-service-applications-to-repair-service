<?php

use App\Http\Controllers\RequestRecordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('request-record/create', [RequestRecordController::class, 'create'])
    ->name('request-record.create');
Route::post('request-record', [RequestRecordController::class, 'store'])
    ->name('request-record.store');

require __DIR__.'/settings.php';
