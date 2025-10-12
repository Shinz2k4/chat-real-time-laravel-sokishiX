<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class WebSocketService
{
    private static $pusher = null;

    /**
     * Get Pusher instance
     */
    public static function getPusher()
    {
        if (self::$pusher === null) {
            self::$pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'useTLS' => true,
                    'encrypted' => true,
                ]
            );
        }

        return self::$pusher;
    }

    /**
     * Subscribe user to their personal channel
     */
    public static function subscribeUser($userId)
    {
        $channel = "user.{$userId}";
        
        try {
            // This would be handled by the frontend
            Log::info("User {$userId} subscribed to channel: {$channel}");
            return $channel;
        } catch (\Exception $e) {
            Log::error("Failed to subscribe user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe user from their personal channel
     */
    public static function unsubscribeUser($userId)
    {
        $channel = "user.{$userId}";
        
        try {
            Log::info("User {$userId} unsubscribed from channel: {$channel}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to unsubscribe user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message to specific user
     */
    public static function sendToUser($userId, $event, $data)
    {
        $channel = "user.{$userId}";
        
        try {
            $pusher = self::getPusher();
            $result = $pusher->trigger($channel, $event, $data);
            
            Log::info("Message sent to user {$userId} on channel {$channel}");
            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to send message to user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send typing indicator
     */
    public static function sendTypingIndicator($fromUserId, $toUserId, $isTyping = true)
    {
        $data = [
            'from_user_id' => $fromUserId,
            'is_typing' => $isTyping,
            'timestamp' => now()->toISOString()
        ];

        return self::sendToUser($toUserId, 'typing', $data);
    }

    /**
     * Send message read status
     */
    public static function sendMessageRead($fromUserId, $toUserId, $messageId)
    {
        $data = [
            'from_user_id' => $fromUserId,
            'message_id' => $messageId,
            'timestamp' => now()->toISOString()
        ];

        return self::sendToUser($toUserId, 'message_read', $data);
    }

    /**
     * Send user online/offline status
     */
    public static function sendUserStatus($userId, $isOnline = true)
    {
        $data = [
            'user_id' => $userId,
            'is_online' => $isOnline,
            'timestamp' => now()->toISOString()
        ];

        // Broadcast to all users (you might want to limit this)
        try {
            $pusher = self::getPusher();
            return $pusher->trigger('presence-chat', 'user_status', $data);
        } catch (\Exception $e) {
            Log::error("Failed to send user status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get connection info for frontend
     */
    public static function getConnectionInfo()
    {
        return [
            'pusher_key' => config('broadcasting.connections.pusher.key'),
            'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'pusher_host' => config('broadcasting.connections.pusher.options.host'),
            'pusher_port' => config('broadcasting.connections.pusher.options.port'),
            'pusher_scheme' => config('broadcasting.connections.pusher.options.scheme'),
        ];
    }

    /**
     * Test WebSocket connection
     */
    public static function testConnection()
    {
        try {
            $pusher = self::getPusher();
            $result = $pusher->trigger('test-channel', 'test-event', ['message' => 'Test connection']);
            return ['success' => true, 'result' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

