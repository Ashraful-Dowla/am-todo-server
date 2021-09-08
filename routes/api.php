<?php

use App\Http\Controllers\Api\AssignedTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\VerificationController;
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

Route::post('register', [AuthController::class, 'register'])->name('register.api');
Route::post('login', [AuthController::class, 'login'])->name('login.api');

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout.api');
    Route::post('profile', [AuthController::class, 'profile'])->name('profile.api');
    Route::get('user_list', [AuthController::class, 'userList'])->name('userlist.api');

    Route::apiResource('task', TaskController::class);
    Route::put('completed/task/{task}', [TaskController::class, 'completed'])->name('task.completed.api');

    Route::apiResource('assigned_task', AssignedTaskController::class);
    Route::put('completed/assigned_task/{assigned_task}', [AssignedTaskController::class, 'completed'])->name('assigned_task.completed.api');
    Route::get('my_assigned_task', [AssignedTaskController::class, 'myAssignedTask'])->name('assgined_task.my_assigned_task.api');
});

Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::post('/forgot-password', [ResetPasswordController::class, 'reset'])->name('password.email');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.api.update');
