<?php

use App\Http\Controllers\Api\AssignedTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register'])->name('register.api');
Route::post('login', [AuthController::class, 'login'])->name('login.api');
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout'])->name('logout.api');
Route::middleware('auth:api')->post('profile', [AuthController::class, 'profile'])->name('profile.api');
Route::middleware('auth:api')->get('user_list', [AuthController::class, 'userList'])->name('userlist.api');

Route::middleware('auth:api')->apiResource('task', TaskController::class);
Route::middleware('auth:api')->put('completed/task/{task}', [TaskController::class, 'completed'])->name('task.completed.api');

Route::middleware('auth:api')->apiResource('assigned_task', AssignedTaskController::class);
Route::middleware('auth:api')->put('completed/assigned_task/{assigned_task}', [AssignedTaskController::class, 'completed'])->name('assigned_task.completed.api');
Route::middleware('auth:api')->get('my_assigned_task', [AssignedTaskController::class, 'myAssignedTask'])->name('assgined_task.my_assigned_task.api');


