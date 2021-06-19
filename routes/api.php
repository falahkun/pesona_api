<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HalaqahController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('cors')->middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('cors')->prefix('v1')->group(function() {
    Route::prefix('auth')->group(function () {
        Route::post('signin', [AuthController::class, 'signin']);
        Route::post('register', [AuthController::class, 'create']);
    });
    Route::middleware('auth:api')->group(function () {
        Route::get('auth/myaccount', [AuthController::class, 'myaccount']);
        Route::post('auth/activate', [AuthController::class, 'activate']);
        Route::get('halaqah', [HalaqahController::class, 'get']);
        Route::post('halaqah/create', [HalaqahController::class, 'create']);
        Route::post('halaqah/{id}', [HalaqahController::class, 'update']);
        Route::delete('halaqah/{id}', [HalaqahController::class, 'delete']);
        Route::post('presence', [PresenceController::class, 'absent']);
        Route::post('presence/create', [PresenceController::class, 'create']);
        Route::get('presences', [PresenceController::class, 'getPresence']);
        Route::post('schedule/create', [ScheduleController::class, 'create']);
        Route::post('schedule/update/{id}', [ScheduleController::class, 'update']);
        Route::delete('schedule/delete/{id}', [ScheduleController::class, 'delete']);
        Route::get('schedules', [ScheduleController::class, 'get']);
        Route::get('auth/users', [AuthController::class, 'getAllAccount']);
    
    });

});
