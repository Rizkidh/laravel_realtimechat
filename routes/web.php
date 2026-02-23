<?php

use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (auth only)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Todo routes
    Route::get('/', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::delete('/todos/batch-delete', [TodoController::class, 'batchDestroy'])->name('todos.batchDestroy');
    Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');

    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/contacts', [ChatController::class, 'contacts'])->name('chat.contacts');
    Route::get('/chat/messages/{contact}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/read/{contact}', [ChatController::class, 'markRead'])->name('chat.read');
    Route::post('/chat/typing', [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat/online', [ChatController::class, 'online'])->name('chat.online');
    Route::post('/chat/offline', [ChatController::class, 'offline'])->name('chat.offline');

    // AI Agent routes
    Route::get('/ai-agent', [AiAgentController::class, 'index'])->name('ai.index');
    Route::post('/ai-agent/ask', [AiAgentController::class, 'ask'])
        ->name('ai.ask')
        ->middleware('throttle:10,1'); // 10 requests per minute
});
Route::fallback( function (){
    return "Halaman Yang kamu cari tidak ada";
});
