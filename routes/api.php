<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwimlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Returns authenticated user data for Sanctum authentication
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Generates TwiML response for incoming/outgoing calls
Route::post('/twiml-response', [TwimlController::class, 'generateTwimlResponse'])->name('twiml.response');

// Stores call data in the database (duration, status, etc.)
Route::post('/save-call-data', [TwimlController::class, 'saveCallData']);
