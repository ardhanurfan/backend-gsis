<?php

use App\Http\Controllers\API\BccController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::get('bcc-user', [BccController::class, 'all']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'get']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('edit-profile', [UserController::class, 'editProfile']);
    
    Route::post('register-bcc-user', [BccController::class, 'register']);
    Route::post('edit-bcc-user', [BccController::class, 'editFromUser']);
    Route::post('edit-bcc-user-from-admin', [BccController::class, 'editFromAdmin']);
    Route::post('bcc-user-submission', [BccController::class, 'submitUser']);
    Route::post('bcc-team-submission', [BccController::class, 'submitTeam']);
    Route::post('create-bcc-team', [BccController::class, 'createTeam']);
});