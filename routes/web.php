<?php

use App\Http\Controllers\Auth\WorkshopRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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

// Workshop Registration (guest)
Route::get('/register/workshop', [WorkshopRegistrationController::class, 'create'])->name('register.workshop');
Route::post('/register/workshop', [WorkshopRegistrationController::class, 'store']);

// Workshop Pending Approval (authenticated)
Route::get('/workshop/pending', function () {
    return view('workshop.pending');
})->middleware('auth')->name('workshop.pending');

require __DIR__.'/auth.php';
