<?php

namespace App\Services;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\UserOnlineStatus;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Get contact list with last message and unread count for a user.
     */
    public function getContacts(User $user): array
    {
        $userId = $user->id;

        // Get all users except current, with last message and unread count
        $contacts = User::where('id', '!=', $userId)
            ->select('id', 'name', 'email', 'is_online', 'last_seen')
            ->get()
            ->map(function (User $contact) use ($userId) {
                $lastMessage = Message::where(function ($q) use ($userId, $contact) {
                    $q->where('sender_id', $userId)->where('receiver_id', $contact->id);
                })->orWhere(function ($q) use ($userId, $contact) {
                    $q->where('sender_id', $contact->id)->where('receiver_id', $userId);
                })
                ->latest()
                ->first();

                $unreadCount = Message::where('sender_id', $contact->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'is_online' => $contact->is_online,
                    'last_seen' => $contact->last_seen?->toISOString(),
                    'last_message' => $lastMessage ? [
                        'message' => $lastMessage->message,
                        'created_at' => $lastMessage->created_at->toISOString(),
                        'is_mine' => $lastMessage->sender_id === $userId,
                    ] : null,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(function ($contact) {
                return $contact['last_message']['created_at'] ?? '0';
            })
            ->values()
            ->toArray();

        return $contacts;
    }

    /**
     * Get paginated messages between two users.
     */
    public function getMessages(int $userId, int $contactId, ?int $beforeId = null, int $limit = 30): array
    {
        $query = Message::where(function ($q) use ($userId, $contactId) {
            $q->where('sender_id', $userId)->where('receiver_id', $contactId);
        })->orWhere(function ($q) use ($userId, $contactId) {
            $q->where('sender_id', $contactId)->where('receiver_id', $userId);
        });

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->with('sender:id,name')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        return [
            'messages' => $messages->map(fn(Message $msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at->toISOString(),
                'sender' => [
                    'id' => $msg->sender->id,
                    'name' => $msg->sender->name,
                ],
            ])->toArray(),
            'has_more' => $messages->count() === $limit,
        ];
    }

    /**
     * Send a message from sender to receiver.
     */
    public function sendMessage(User $sender, int $receiverId, string $messageText): Message
    {
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'message' => $messageText,
            'is_read' => false,
        ]);

        $message->load('sender:id,name');

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Mark all messages from contact as read.
     */
    public function markAsRead(int $userId, int $contactId): int
    {
        $count = Message::where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($count > 0) {
            broadcast(new MessageRead($contactId, $userId))->toOthers();
        }

        return $count;
    }

    /**
     * Broadcast typing indicator.
     */
    public function sendTypingIndicator(User $user, int $receiverId, bool $isTyping): void
    {
        broadcast(new UserTyping(
            senderId: $user->id,
            senderName: $user->name,
            receiverId: $receiverId,
            isTyping: $isTyping
        ))->toOthers();
    }

    /**
     * Set user online/offline status.
     */
    public function setOnlineStatus(User $user, bool $isOnline): void
    {
        $user->update([
            'is_online' => $isOnline,
            'last_seen' => $isOnline ? null : now(),
        ]);

        broadcast(new UserOnlineStatus(
            userId: $user->id,
            isOnline: $isOnline,
            lastSeen: $isOnline ? null : now()->toISOString()
        ));
    }

    /**
     * Get total unread message count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
