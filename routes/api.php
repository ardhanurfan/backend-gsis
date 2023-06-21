<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CeremonyController;
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
Route::get('ceremony',[CeremonyController::class, 'all']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'get']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('edit-profile', [UserController::class, 'editProfile']);
    
    Route::post('register-ceremony', [CeremonyController::class,'register']);
    Route::post('edit-ceremony-user', [CeremonyController::class,'userEdit']);
    Route::post('edit-ceremony-admin', [CeremonyController::class,'adminEdit']);
});