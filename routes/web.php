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
use App\Http\Controllers\ContextoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\ChatWebController;
use App\Http\Controllers\ContactoWebController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\FacturaController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas del chat
Route::post('/api/chat', [ChatController::class, 'chat']);
Route::post('/api/chat/admin-reply', [ChatController::class, 'adminReply'])->middleware('auth');
Route::get('/api/chat/history', [ChatController::class, 'getChatHistory']);
Route::post('/api/chat/find-or-create', [ChatController::class, 'findOrCreateChat']);
Route::get('/api/chat/new-id', [ChatController::class, 'generateNewId']);
Route::get('/api/chat/user', [ChatController::class, 'getUserChat']);
Route::get('/get-user-chat', [ChatController::class, 'getUserChat'])->name('get.user.chat');
Route::post('/chat', [ChatController::class, 'chat'])->name('chat.store');

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::post('/whatsapp/send', [WhatsAppController::class, 'send'])->name('whatsapp.send');
    Route::post('/whatsapp/send-bulk', [WhatsAppController::class, 'sendBulk'])->name('whatsapp.send-bulk');
    
    // Rutas para ChatWeb
    Route::get('/chat-web', [ChatWebController::class, 'index'])->name('chat-web.index');
    Route::get('/chat-web/{chat_id}', [ChatWebController::class, 'show'])->name('chat-web.show');
    Route::delete('/chat-web/{chat_id}', [ChatWebController::class, 'destroy'])->name('chat-web.destroy');

    // Ruta para ContactoWeb (dentro de auth middleware)
    Route::resource('contacto-webs', ContactoWebController::class)->except(['create', 'store', 'show', 'edit', 'update']);
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
Route::resource('contextos', ContextoController::class)->middleware('auth');
Route::resource('contactos', ContactoController::class)->middleware('auth');
Route::post('/contactos/import', [ContactoController::class, 'import'])->name('contactos.import')->middleware('auth');
Route::resource('turnos', TurnoController::class)->middleware('auth');
Route::resource('contabilidad', ContabilidadController::class)->middleware('auth');
Route::resource('facturas', FacturaController::class)->middleware('auth');
Route::get('/facturas/generar/xml', [FacturaController::class, 'generarxml'])->name('facturas.generarxml')->middleware('auth');
Route::post('/facturas/guardar/xml', [FacturaController::class, 'guardarxml'])->name('facturas.guardarxml')->middleware('auth');
Route::get('/facturas/{factura}/firmar', [FacturaController::class, 'firmar'])->name('facturas.firmar')->middleware('auth');
Route::post('/facturas/{factura}/procesar-firma', [FacturaController::class, 'procesarFirma'])->name('facturas.procesar-firma')->middleware('auth');
Route::put('/mensajes/update-nombre', [App\Http\Controllers\MensajeController::class, 'updateNombre'])->name('mensajes.updateNombre')->middleware('auth');
Route::resource('mensajes', MensajeController::class)->middleware('auth');
Route::post('/webhook/twilio', [WebhookController::class, 'handleTwilioWebhook'])->name('webhook.twilio');
Route::post('/mensajes/eliminar-conversacion/{contactoId}', [MensajeController::class, 'eliminarConversacion'])->name('mensajes.eliminar-conversacion');
