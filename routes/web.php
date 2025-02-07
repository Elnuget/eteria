<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => true]);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

Route::resource('projects', ProjectController::class)->middleware('auth');
Route::resource('balances', BalanceController::class);
Route::post('/balances/updateBalances', [BalanceController::class, 'updateBalances'])->name('balances.updateBalances');
Route::resource('payments', PaymentController::class)->middleware('auth');
Route::resource('tasks', TaskController::class);
Route::post('/tasks/{task}/tomar', [TaskController::class, 'tomarTarea'])->name('tasks.tomar');
Route::post('/tasks/{task}/completar', [TaskController::class, 'completarTarea'])->name('tasks.completar');
