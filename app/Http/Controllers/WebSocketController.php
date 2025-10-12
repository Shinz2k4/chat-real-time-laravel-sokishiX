<?php

namespace App\Http\Controllers;

use App\Services\WebSocketService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebSocketController extends Controller
{
    /**
     * Get WebSocket connection info
     */
    public function getConnectionInfo()
    {
        $user = Auth::user();
        
        return response()->json([
            'connection_info' => WebSocketService::getConnectionInfo(),
            'user_channel' => "user.{$user->_id}",
            'presence_channel' => 'presence-chat',
            'user_id' => $user->_id,
            'user_name' => $user->name
        ]);
    }

    /**
     * Test WebSocket connection
     */
    public function testConnection()
    {
        $result = WebSocketService::testConnection();
        
        return response()->json($result);
    }

    /**
     * Send typing indicator
     */
    public function sendTyping(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|string',
            'is_typing' => 'required|boolean'
        ]);

        $fromUserId = Auth::user()->_id;
        $toUserId = $request->to_user_id;
        $isTyping = $request->is_typing;

        $result = WebSocketService::sendTypingIndicator($fromUserId, $toUserId, $isTyping);

        return response()->json([
            'success' => $result !== false,
            'message' => $isTyping ? 'Typing indicator sent' : 'Typing stopped'
        ]);
    }

    /**
     * Send message read status
     */
    public function markMessageRead(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|string',
            'message_id' => 'required|string'
        ]);

        $fromUserId = Auth::user()->_id;
        $toUserId = $request->to_user_id;
        $messageId = $request->message_id;

        $result = WebSocketService::sendMessageRead($fromUserId, $toUserId, $messageId);

        return response()->json([
            'success' => $result !== false,
            'message' => 'Message read status sent'
        ]);
    }

    /**
     * Send user online status
     */
    public function setOnlineStatus(Request $request)
    {
        $request->validate([
            'is_online' => 'required|boolean'
        ]);

        $userId = Auth::user()->_id;
        $isOnline = $request->is_online;

        $result = WebSocketService::sendUserStatus($userId, $isOnline);

        // Update user's online status in cache
        $cacheKey = "user_online_{$userId}";
        if ($isOnline) {
            CacheService::cacheUserOnlineStatus($userId, true);
        } else {
            CacheService::clearUserOnlineStatus($userId);
        }

        return response()->json([
            'success' => $result !== false,
            'message' => $isOnline ? 'User is online' : 'User is offline'
        ]);
    }

    /**
     * Get preloaded conversations with real-time updates
     */
    public function getPreloadedConversationsWithRealtime()
    {
        $userId = Auth::user()->_id;
        
        // Try to get cached preloaded conversations first
        $preloadedData = CacheService::getCachedPreloadedConversations($userId);
        
        if (!$preloadedData) {
            // If not cached, get from database
            $contactsController = new ContactsController();
            $response = $contactsController->preloadConversations();
            $preloadedData = $response->getData(true)['preloaded_conversations'];
        }

        // Add real-time connection info
        $connectionInfo = WebSocketService::getConnectionInfo();

        return response()->json([
            'preloaded_conversations' => $preloadedData,
            'connection_info' => $connectionInfo,
            'user_channel' => "user.{$userId}",
            'timestamp' => now()->toISOString()
        ]);
    }
}

