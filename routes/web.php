<?php

use App\Http\Controllers\ManualActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepetitiveActivityController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WRController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('uploads.index')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/uploads', [UploadController::class,'index'])->name('uploads.index');
    Route::get('/uploads/create', [UploadController::class,'create'])->name('uploads.create');
    Route::post('/uploads', [UploadController::class,'store'])->name('uploads.store');
    Route::delete('/uploads/{upload}', [UploadController::class,'destroy'])->name('uploads.destroy');

    Route::get('/activities', [ManualActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities', [ManualActivityController::class, 'store'])->name('activities.store');
    Route::patch('/activities/{activity}', [ManualActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ManualActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('/repetitives', [RepetitiveActivityController::class, 'index'])->name('repetitives.index');
    Route::post('/repetitives', [RepetitiveActivityController::class, 'store'])->name('repetitives.store');
    Route::patch('/repetitives/{repetitive}', [RepetitiveActivityController::class, 'update'])->name('repetitives.update');
    Route::delete('/repetitives/{repetitive}', [RepetitiveActivityController::class, 'destroy'])->name('repetitives.destroy');

    Route::post('/wr/generate', [WRController::class,'generate'])->name('wr.generate');
});

require __DIR__.'/auth.php';






