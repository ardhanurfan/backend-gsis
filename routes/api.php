<?php

use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\UniversityController;
use App\Http\Controllers\API\BccController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CeremonyController;
use App\Http\Controllers\API\GsicController;
use App\Http\Controllers\API\ExhibitionController;
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

Route::get('universities', [UniversityController::class, 'all']);

Route::get('announcement',[AnnouncementController::class, 'all']);
Route::post('add-announcement',[AnnouncementController::class, 'add']);
Route::post('edit-announcement',[AnnouncementController::class, 'edit']);
Route::post('delete-announcement',[AnnouncementController::class, 'delete']);

Route::get('bcc-user', [BccController::class, 'all']);
Route::get('bcc-team', [BccController::class, 'allTeam']);
Route::post('edit-bcc-user-from-admin', [BccController::class, 'editFromAdmin']);
Route::post('edit-bcc-team-from-admin', [BccController::class, 'editFromAdminTeam']);

Route::get('gsic', [GsicController::class, 'all']);
Route::post('edit-gsic-user', [GsicController::class,'editFromUser']);
Route::post('edit-gsic-admin', [GsicController::class,'editFromAdmin']);

Route::post('edit-ceremony-admin', [CeremonyController::class,'adminEdit']);

Route::get('exhibition-user',[ExhibitionController::class, 'all']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'get']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('edit-profile', [UserController::class, 'editProfile']);
    
    Route::post('register-ceremony', [CeremonyController::class,'register']);
    Route::post('edit-ceremony-user', [CeremonyController::class,'userEdit']);

    Route::get('announcement-by-user',[AnnouncementController::class, 'getByUser']);

    Route::post('register-bcc-user', [BccController::class, 'register']);
    Route::post('edit-bcc-user', [BccController::class, 'editFromUser']);
    Route::post('bcc-user-submission', [BccController::class, 'submitUser']);
    Route::post('create-bcc-team', [BccController::class, 'createTeam']);
    Route::post('bcc-team-submission', [BccController::class, 'submitTeam']);
    Route::post('edit-bcc-team', [BccController::class, 'editFromTeam']);
    Route::post('edit-bcc-team-submission', [BccController::class, 'editSubmitTeam']);

    Route::post('register-gsic', [GsicController::class,'register']);
    Route::post('gsic-submission', [GsicController::class,'submitTeam']);
    Route::post('edit-gsic-submission', [GsicController::class,'editSubmitTeam']);

    Route::post('register-exhibition', [ExhibitionController::class,'register']);
    Route::post('edit-exhibition-user', [ExhibitionController::class,'editFromUser']);
});