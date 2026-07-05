<?php

use App\Http\Controllers\Auth\WorkshopRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Workshop\WorkshopPendingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Maintify Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Authenticated & Workshop Approved Routes
Route::middleware(['auth', 'verified', 'workshop.approved'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Vehicle Owner Routes
Route::middleware(['auth', 'verified', 'role:vehicle_owner'])->group(function () {
    Route::resource('vehicles', \App\Http\Controllers\VehicleController::class);
});

// Workshop Registration (guest)
Route::get('/register/workshop', [WorkshopRegistrationController::class, 'create'])->name('register.workshop');
Route::post('/register/workshop', [WorkshopRegistrationController::class, 'store']);

// Workshop Pending Approval (authenticated)
Route::get('/workshop/pending', [WorkshopPendingController::class, 'show'])
    ->middleware('auth')
    ->name('workshop.pending');

require __DIR__.'/auth.php';

