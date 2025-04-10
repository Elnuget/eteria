<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WhatsAppController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta del chat
Route::post('/api/chat', [ChatController::class, 'chat']);

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::post('/whatsapp/send', [WhatsAppController::class, 'send'])->name('whatsapp.send');
});

Route::resource('projects', ProjectController::class)->middleware('auth');
Route::resource('balances', BalanceController::class);
Route::post('/balances/updateBalances', [BalanceController::class, 'updateBalances'])->name('balances.updateBalances');
Route::resource('payments', PaymentController::class)->middleware('auth');
Route::resource('tasks', TaskController::class);
Route::post('/tasks/{task}/tomar', [TaskController::class, 'tomarTarea'])->name('tasks.tomar');
Route::post('/tasks/{task}/completar', [TaskController::class, 'completarTarea'])->name('tasks.completar');
Route::resource('clientes', ClienteController::class)->middleware('auth');
Route::post('/clientes/{cliente}/attach-project', [ClienteController::class, 'attachProject'])->name('clientes.attach-project')->middleware('auth');
Route::post('/clientes/{cliente}/detach-project', [ClienteController::class, 'detachProject'])->name('clientes.detach-project')->middleware('auth');
Route::get('/projects/{project}/clients', [ProjectController::class, 'getClients'])->name('projects.clients.index');
Route::post('/projects/{project}/clients/{client}', [ProjectController::class, 'attachClient'])->name('projects.clients.attach');
Route::delete('/projects/{project}/clients/{client}', [ProjectController::class, 'detachClient'])->name('projects.clients.detach');
