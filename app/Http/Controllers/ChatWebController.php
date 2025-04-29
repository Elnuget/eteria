<?php

namespace App\Http\Controllers;

use App\Models\ChatWeb;
use App\Models\ContactoWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los mensajes y agruparlos por chat_id
        $allMessages = ChatWeb::with('contactoWeb')
                               ->orderBy('created_at', 'asc')
                               ->get(); 

        // Agrupar mensajes por chat_id en una colección
        $chatsGrouped = $allMessages->groupBy('chat_id');

        // Ordenar los grupos por la fecha del último mensaje de cada grupo (descendente)
        $chats = $chatsGrouped->sortByDesc(function ($messages, $chat_id) {
            return $messages->last()->created_at;
        });

        return view('chat-web.index', compact('chats'));
    }

    /**
     * Display the specified resource.
     * (Mantener la lógica original si se usa para ver un chat específico por ID)
     */
    public function show(string $chat_id)
    {
        $messages = ChatWeb::where('chat_id', $chat_id)
                          ->with('contactoWeb')
                          ->orderBy('created_at', 'asc')
                          ->get();

        if ($messages->isEmpty()) {
            abort(404, 'Chat no encontrado.');
        }

        // Podrías pasar los mensajes a una vista específica si es necesario
        // return view('chat-web.show', compact('messages')); 
        // O redirigir al index y resaltar/abrir ese chat (más complejo)
        return redirect()->route('chat-web.index')->with('highlight_chat', $chat_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $chat_id)
    {
        // Eliminar todos los mensajes asociados a este chat_id
        $deletedCount = ChatWeb::where('chat_id', $chat_id)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('chat-web.index')
                         ->with('success', "Conversación ({$chat_id}) eliminada correctamente.");
        } else {
            return redirect()->route('chat-web.index')
                         ->with('error', "No se encontró o no se pudo eliminar la conversación ({$chat_id}).");
        }
    }
} 