<?php

use App\Http\Controllers\RequestRecordController;
use App\Http\Controllers\RequestRecordPanelController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('request-record-panel', [RequestRecordPanelController::class, 'index'])
        ->name('request-record-panel.index');
    Route::patch('request-record-panel/{requestRecord}/status', [RequestRecordPanelController::class, 'updateStatus'])
        ->name('request-record-panel.update-status');
    Route::post('request-record-panel/{requestRecord}/assign', [RequestRecordPanelController::class, 'assign'])
        ->name('request-record-panel.assign');
    Route::post('request-record-panel/{requestRecord}/start-work', [RequestRecordPanelController::class, 'startWork'])
        ->name('request-record-panel.start-work');
    Route::post('request-record-panel/{requestRecord}/finish', [RequestRecordPanelController::class, 'finish'])
        ->name('request-record-panel.finish');
});

require __DIR__.'/settings.php';
