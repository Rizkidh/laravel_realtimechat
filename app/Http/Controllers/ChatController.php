<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $chatService
    ) {}

    /**
     * Show the chat page.
     */
    public function index(): View
    {
        $user = auth()->user();
        $this->chatService->setOnlineStatus($user, true);

        return view('chat.index', [
            'authUser' => $user,
        ]);
    }

    /**
     * Get contact list with last message and unread count.
     */
    public function contacts(): JsonResponse
    {
        $contacts = $this->chatService->getContacts(auth()->user());

        return response()->json(['contacts' => $contacts]);
    }

    /**
     * Get paginated messages with a specific contact.
     */
    public function messages(Request $request, User $contact): JsonResponse
    {
        $beforeId = $request->query('before_id');

        $data = $this->chatService->getMessages(
            auth()->id(),
            $contact->id,
            $beforeId ? (int) $beforeId : null
        );

        return response()->json($data);
    }

    /**
     * Send a new message.
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $message = $this->chatService->sendMessage(
            auth()->user(),
            $request->validated('receiver_id'),
            $request->validated('message')
        );

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->toISOString(),
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                ],
            ],
        ], 201);
    }

    /**
     * Mark all messages from a contact as read.
     */
    public function markRead(User $contact): JsonResponse
    {
        $count = $this->chatService->markAsRead(auth()->id(), $contact->id);

        return response()->json(['marked_read' => $count]);
    }

    /**
     * Broadcast typing indicator.
     */
    public function typing(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'is_typing' => ['required', 'boolean'],
        ]);

        $this->chatService->sendTypingIndicator(
            auth()->user(),
            $request->input('receiver_id'),
            $request->boolean('is_typing')
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Set user online.
     */
    public function online(): JsonResponse
    {
        $this->chatService->setOnlineStatus(auth()->user(), true);

        return response()->json(['status' => 'online']);
    }

    /**
     * Set user offline.
     */
    public function offline(): JsonResponse
    {
        $this->chatService->setOnlineStatus(auth()->user(), false);

        return response()->json(['status' => 'offline']);
    }
}
