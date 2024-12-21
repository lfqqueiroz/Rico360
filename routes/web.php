<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\UserStatusController;

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

// Redirect to login page when accessing the root URL
Route::get('/', function () {
    return view('auth.login');
});

// Display the main dashboard page (requires authentication)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Group of routes that require authentication
Route::middleware('auth')->group(function () {
    // Display user profile edit page
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Update user profile information
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Delete user account
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Update user's online/offline/in_call status
Route::post('/update-status', [UserStatusController::class, 'update'])
    ->middleware(['auth']);

// Initiate a new call to another user
Route::post('/make-call', [CallController::class, 'makeCall']);
// Generate Twilio access token for client
Route::get('/access-token', [CallController::class, 'grantToken'])->name('twilio.token');

// Include authentication routes
require __DIR__.'/auth.php';
