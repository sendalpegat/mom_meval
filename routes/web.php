<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::get('user', [UserController::class, 'index'])->name('user')->middleware('auth');
Route::get('user/sync-odoo', [UserController::class, 'syncOdoo'])->name('user/sync-odoo')->middleware('auth');

Route::post('meeting/add', [MeetingController::class, 'store'])->name('meeting/add')->middleware('auth');
// updates a meeting
Route::post('/meeting/edit', [MeetingController::class ,'update'])->name('meeting/edit')->middleware('auth');
// deletes a meeting
Route::post('/meeting/delete', [MeetingController::class ,'delete'])->name('meeting/delete')->middleware('auth');

Route::get('meeting/{post}/update', MeetingController::class .'@show')->name('meeting/update')->middleware('auth');
Route::get('meeting', [MeetingController::class, 'index'])->name('meeting')->middleware('auth');
Route::get('meeting/tasks', [TaskController::class, 'index'])->name('meeting/tasks')->middleware('auth');
Route::post('/meeting/tasks/update', [TaskController::class ,'updateStatus'])->name('meeting/tasks/update')->middleware('auth');
Route::get('meeting/create', [MeetingController::class, 'create'])->name('meeting/create')->middleware('auth');

Route::get('meeting/calendar',[MeetingController::class, 'calendar'])->name('meeting/calendar')->middleware('auth');
