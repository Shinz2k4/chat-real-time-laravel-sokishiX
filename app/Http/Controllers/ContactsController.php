<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function get()
    {
        $userId = auth()->user()->_id;
        
        // Try to get cached contacts first
        $contacts = CacheService::getCachedContacts($userId);
        if (!$contacts) {
            $contacts = User::where('_id', '!=', $userId)
                ->select(['_id', 'name', 'email', 'profile_image'])
                ->get();
            CacheService::cacheContacts($userId, $contacts);
        }

        // Try to get cached unread messages count
        $unreadIds = CacheService::getCachedUnreadMessages($userId);
        if (!$unreadIds) {
            $unreadIds = Message::select(DB::raw('`from` as sender_id, count(`from`) as messages_count'))
                ->where('to', $userId)
                ->where('read', false)
                ->groupBy('from')
                ->get();
            CacheService::cacheUnreadMessages($userId, $unreadIds);
        }

        $contacts = $contacts->map(function ($contact) use ($unreadIds) {
            $contactUnread = $unreadIds->where('sender_id', $contact->_id)->first();
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
        });

        return response()->json($contacts);
    }

    /**
     * Preload conversations for the first 5 contacts
     */
    public function preloadConversations()
    {
        $userId = auth()->user()->_id;
        
        // Get first 5 contacts
        $contacts = User::where('_id', '!=', $userId)
            ->select(['_id', 'name', 'email', 'profile_image'])
            ->limit(5)
            ->get();

        $preloadedData = [];
        
        foreach ($contacts as $contact) {
            // Get last 10 messages for each contact
            $messages = Message::where(function ($q) use ($contact, $userId) {
                $q->where('from', (string) $userId);
                $q->where('to', (string) $contact->_id);
            })->orWhere(function ($q) use ($contact, $userId) {
                $q->where('from', (string) $contact->_id);
                $q->where('to', (string) $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

            // Get unread count for this contact
            $unreadCount = Message::where('from', (string) $contact->_id)
                ->where('to', (string) $userId)
                ->where('read', false)
                ->count();

            $preloadedData[] = [
                'contact' => $contact,
                'messages' => $messages,
                'unread_count' => $unreadCount,
                'last_message' => $messages->last(),
                'last_message_time' => $messages->last() ? $messages->last()->created_at : null
            ];
        }

        // Cache preloaded data for 2 minutes
        $cacheKey = "preloaded_conversations_{$userId}";
        CacheService::cachePreloadedConversations($userId, $preloadedData);

        return response()->json([
            'preloaded_conversations' => $preloadedData,
            'total_contacts' => User::where('_id', '!=', $userId)->count(),
            'preloaded_count' => count($preloadedData)
        ]);
    }

    public function getMessagesFor($id)
    {
        $currentUserId = auth()->user()->_id;
        $id = (string) $id;
        $currentUserId = (string) $currentUserId;
        
        // Use bulk update instead of individual saves
        Message::where('from', $id)
            ->where('to', $currentUserId)
            ->where('read', false)
            ->update(['read' => true]);
        
        // Clear unread cache after marking messages as read
        CacheService::clearUnreadCaches([$currentUserId]);
        
        // Try to get cached messages first
        $messages = CacheService::getCachedMessages($currentUserId, $id);
        if (!$messages) {
            $messages = Message::where(function ($q) use ($id, $currentUserId) {
                $q->where('from', $currentUserId);
                $q->where('to', $id);
            })->orWhere(function ($q) use ($id, $currentUserId) {
                $q->where('from', $id);
                $q->where('to', $currentUserId);
            })->orderBy('created_at', 'asc')->get();
            
            CacheService::cacheMessages($currentUserId, $id, $messages);
        }
        
        return response()->json($messages);
    }

    public function send(Request $request)
    {
        // Validate request
        $request->validate([
            'contact_id' => 'required|string',
            'text' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'from' => (string) auth()->user()->_id,
            'to' => (string) $request->contact_id,
            'text' => $request->text,
            'read' => false
        ]);

        // Load the relationship before broadcasting
        $message->load('fromContact');

        // Clear relevant caches
        $currentUserId = (string) auth()->user()->_id;
        $contactId = (string) $request->contact_id;
        
        CacheService::clearUnreadCaches([$currentUserId, $contactId]);
        CacheService::clearMessageCaches($currentUserId, $contactId);

        broadcast(new NewMessage($message));

        return response()->json($message);
    }
}
