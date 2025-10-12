<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache TTL constants
     */
    const CONTACTS_TTL = 300; // 5 minutes
    const MESSAGES_TTL = 120; // 2 minutes
    const UNREAD_TTL = 60; // 1 minute
    const USER_TTL = 600; // 10 minutes
    const PRELOADED_CONVERSATIONS_TTL = 120; // 2 minutes

    /**
     * Cache contacts for a user
     */
    public static function cacheContacts($userId, $contacts)
    {
        $key = "contacts_{$userId}";
        return Cache::put($key, $contacts, self::CONTACTS_TTL);
    }

    /**
     * Get cached contacts for a user
     */
    public static function getCachedContacts($userId)
    {
        $key = "contacts_{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache unread messages count for a user
     */
    public static function cacheUnreadMessages($userId, $unreadCount)
    {
        $key = "unread_messages_{$userId}";
        return Cache::put($key, $unreadCount, self::UNREAD_TTL);
    }

    /**
     * Get cached unread messages count for a user
     */
    public static function getCachedUnreadMessages($userId)
    {
        $key = "unread_messages_{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache messages between two users
     */
    public static function cacheMessages($userId1, $userId2, $messages)
    {
        $key = "messages_{$userId1}_{$userId2}";
        return Cache::put($key, $messages, self::MESSAGES_TTL);
    }

    /**
     * Get cached messages between two users
     */
    public static function getCachedMessages($userId1, $userId2)
    {
        $key = "messages_{$userId1}_{$userId2}";
        return Cache::get($key);
    }

    /**
     * Clear all caches for a user
     */
    public static function clearUserCaches($userId)
    {
        $keys = [
            "contacts_{$userId}",
            "unread_messages_{$userId}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear message caches (this is more expensive, so we'll do it selectively)
        Log::info("Cleared caches for user: {$userId}");
    }

    /**
     * Clear message caches between two users
     */
    public static function clearMessageCaches($userId1, $userId2)
    {
        $keys = [
            "messages_{$userId1}_{$userId2}",
            "messages_{$userId2}_{$userId1}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear unread message caches for multiple users
     */
    public static function clearUnreadCaches($userIds)
    {
        foreach ($userIds as $userId) {
            Cache::forget("unread_messages_{$userId}");
        }
    }

    /**
     * Cache preloaded conversations for a user
     */
    public static function cachePreloadedConversations($userId, $conversations)
    {
        $key = "preloaded_conversations_{$userId}";
        return Cache::put($key, $conversations, self::PRELOADED_CONVERSATIONS_TTL);
    }

    /**
     * Get cached preloaded conversations for a user
     */
    public static function getCachedPreloadedConversations($userId)
    {
        $key = "preloaded_conversations_{$userId}";
        return Cache::get($key);
    }

    /**
     * Clear preloaded conversations cache for a user
     */
    public static function clearPreloadedConversations($userId)
    {
        $key = "preloaded_conversations_{$userId}";
        return Cache::forget($key);
    }

    /**
     * Cache user online status
     */
    public static function cacheUserOnlineStatus($userId, $isOnline)
    {
        $key = "user_online_{$userId}";
        return Cache::put($key, $isOnline, 300); // 5 minutes
    }

    /**
     * Get user online status
     */
    public static function getUserOnlineStatus($userId)
    {
        $key = "user_online_{$userId}";
        return Cache::get($key, false);
    }

    /**
     * Clear user online status
     */
    public static function clearUserOnlineStatus($userId)
    {
        $key = "user_online_{$userId}";
        return Cache::forget($key);
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats()
    {
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            return [
                'used_memory' => $redis->info('memory')['used_memory_human'] ?? 'N/A',
                'connected_clients' => $redis->info('clients')['connected_clients'] ?? 'N/A',
                'total_commands_processed' => $redis->info('stats')['total_commands_processed'] ?? 'N/A',
            ];
        }

        return ['driver' => config('cache.default')];
    }
}
