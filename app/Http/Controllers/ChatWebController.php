<?php

namespace App\Http\Controllers;

use App\Models\ChatWeb;
use Illuminate\Http\Request;

class ChatWebController extends Controller
{
    public function index()
    {
        $chats = ChatWeb::orderBy('created_at', 'desc')
            ->get()
            ->groupBy('chat_id');

        return view('chat-web.index', compact('chats'));
    }

    public function show($chat_id)
    {
        $messages = ChatWeb::where('chat_id', $chat_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat-web.show', compact('messages', 'chat_id'));
    }

    public function destroy($chat_id)
    {
        ChatWeb::where('chat_id', $chat_id)->delete();
        return redirect()->route('chat-web.index')
            ->with('success', 'Conversación eliminada correctamente');
    }
} 