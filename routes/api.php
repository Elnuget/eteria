<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PruebaTecnicaFarmaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/chat', [PruebaTecnicaFarmaController::class, 'chat']);
Route::post('/chat/find-or-create', [PruebaTecnicaFarmaController::class, 'findOrCreateChat']);
Route::get('/chat/history', [PruebaTecnicaFarmaController::class, 'getChatHistory']); 

// Rutas para la prueba técnica farmacéutica
Route::prefix('prueba-tecnica-farma')->group(function () {
    Route::get('/datos-ventas', [PruebaTecnicaFarmaController::class, 'getDatosVentas']);
}); 