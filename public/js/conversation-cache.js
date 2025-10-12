/**
 * Conversation Cache Service
 * Manages preloaded conversations with advanced caching strategies
 */
class ConversationCache {
    constructor() {
        this.cache = new Map();
        this.maxCacheSize = 10; // Maximum number of conversations to cache
        this.cacheTimeout = 120000; // 2 minutes
        this.accessTimes = new Map(); // Track last access time for LRU
        this.isInitialized = false;
    }

    /**
     * Initialize cache with preloaded data
     */
    async initialize() {
        if (this.isInitialized) return;

        try {
            const response = await fetch('/contacts/preload');
            const data = await response.json();
            
            if (data.preloaded_conversations) {
                data.preloaded_conversations.forEach(conv => {
                    this.set(conv.contact._id, {
                        contact: conv.contact,
                        messages: conv.messages || [],
                        unread_count: conv.unread_count || 0,
                        last_message: conv.last_message,
                        last_message_time: conv.last_message_time,
                        cached_at: Date.now()
                    });
                });
                
                this.isInitialized = true;
                console.log(`✅ Conversation cache initialized with ${data.preloaded_conversations.length} conversations`);
            }
        } catch (error) {
            console.error('❌ Failed to initialize conversation cache:', error);
        }
    }

    /**
     * Set conversation in cache
     */
    set(contactId, conversation) {
        // Check cache size and remove oldest if needed
        if (this.cache.size >= this.maxCacheSize) {
            this.evictOldest();
        }

        this.cache.set(contactId, conversation);
        this.accessTimes.set(contactId, Date.now());
    }

    /**
     * Get conversation from cache
     */
    get(contactId) {
        const conversation = this.cache.get(contactId);
        
        if (conversation) {
            // Update access time
            this.accessTimes.set(contactId, Date.now());
            
            // Check if cache is expired
            if (Date.now() - conversation.cached_at > this.cacheTimeout) {
                this.cache.delete(contactId);
                this.accessTimes.delete(contactId);
                return null;
            }
            
            return conversation;
        }
        
        return null;
    }

    /**
     * Check if conversation is cached
     */
    has(contactId) {
        return this.cache.has(contactId) && this.get(contactId) !== null;
    }

    /**
     * Update conversation with new message
     */
    updateWithMessage(contactId, message) {
        const conversation = this.get(contactId);
        if (conversation) {
            conversation.messages.push(message);
            conversation.last_message = message;
            conversation.last_message_time = message.created_at;
            conversation.cached_at = Date.now();
            
            this.set(contactId, conversation);
        }
    }

    /**
     * Update unread count
     */
    updateUnreadCount(contactId, count) {
        const conversation = this.get(contactId);
        if (conversation) {
            conversation.unread_count = count;
            this.set(contactId, conversation);
        }
    }

    /**
     * Evict oldest conversation (LRU)
     */
    evictOldest() {
        let oldestTime = Infinity;
        let oldestContactId = null;

        for (const [contactId, accessTime] of this.accessTimes) {
            if (accessTime < oldestTime) {
                oldestTime = accessTime;
                oldestContactId = contactId;
            }
        }

        if (oldestContactId) {
            this.cache.delete(oldestContactId);
            this.accessTimes.delete(oldestContactId);
            console.log(`🗑️ Evicted oldest conversation: ${oldestContactId}`);
        }
    }

    /**
     * Clear all cache
     */
    clear() {
        this.cache.clear();
        this.accessTimes.clear();
        this.isInitialized = false;
        console.log('🧹 Conversation cache cleared');
    }

    /**
     * Get cache statistics
     */
    getStats() {
        return {
            size: this.cache.size,
            maxSize: this.maxCacheSize,
            isInitialized: this.isInitialized,
            cacheTimeout: this.cacheTimeout,
            conversations: Array.from(this.cache.keys())
        };
    }

    /**
     * Refresh cache with fresh data
     */
    async refresh() {
        try {
            const response = await fetch('/contacts/preload');
            const data = await response.json();
            
            if (data.preloaded_conversations) {
                // Clear existing cache
                this.clear();
                
                // Reinitialize with fresh data
                await this.initialize();
                
                console.log('🔄 Conversation cache refreshed');
                return true;
            }
        } catch (error) {
            console.error('❌ Failed to refresh conversation cache:', error);
            return false;
        }
    }

    /**
     * Get all cached conversations
     */
    getAll() {
        return Array.from(this.cache.values());
    }

    /**
     * Get conversations sorted by last message time
     */
    getSortedByLastMessage() {
        return this.getAll().sort((a, b) => {
            const timeA = new Date(a.last_message_time || 0).getTime();
            const timeB = new Date(b.last_message_time || 0).getTime();
            return timeB - timeA;
        });
    }

    /**
     * Preload additional conversations
     */
    async preloadAdditional(contactIds) {
        try {
            const promises = contactIds.map(async (contactId) => {
                if (!this.has(contactId)) {
                    const response = await fetch(`/conversation/${contactId}`);
                    const messages = await response.json();
                    
                    // Get contact info from existing cache or contacts list
                    const existingConv = this.cache.get(contactId);
                    const contact = existingConv?.contact || { _id: contactId };
                    
                    this.set(contactId, {
                        contact,
                        messages,
                        unread_count: 0,
                        last_message: messages[messages.length - 1],
                        last_message_time: messages[messages.length - 1]?.created_at,
                        cached_at: Date.now()
                    });
                }
            });
            
            await Promise.all(promises);
            console.log(`✅ Preloaded ${contactIds.length} additional conversations`);
        } catch (error) {
            console.error('❌ Failed to preload additional conversations:', error);
        }
    }
}

// Create global instance
window.conversationCache = new ConversationCache();

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.conversationCache.initialize();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConversationCache;
}

