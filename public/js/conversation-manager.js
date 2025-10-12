/**
 * Conversation Manager for Laravel Chat App
 * Handles preloaded conversations and real-time updates
 */
class ConversationManager {
    constructor() {
        this.preloadedConversations = [];
        this.currentConversation = null;
        this.isLoading = false;
        this.cache = new Map();
        
        this.init();
    }

    /**
     * Initialize conversation manager
     */
    async init() {
        // Load preloaded conversations
        await this.loadPreloadedConversations();
        
        // Set up WebSocket event listeners
        this.setupWebSocketListeners();
        
        // Set up periodic cache refresh
        this.setupCacheRefresh();
    }

    /**
     * Load preloaded conversations
     */
    async loadPreloadedConversations() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        
        try {
            const response = await fetch('/websocket/preload-realtime');
            const data = await response.json();
            
            if (data.preloaded_conversations) {
                this.preloadedConversations = data.preloaded_conversations;
                this.updateConversationList();
                console.log(`✅ Loaded ${data.preloaded_count} preloaded conversations`);
            }
        } catch (error) {
            console.error('❌ Failed to load preloaded conversations:', error);
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Set up WebSocket event listeners
     */
    setupWebSocketListeners() {
        // Listen for new messages
        window.addEventListener('websocket:new-message', (event) => {
            this.handleNewMessage(event.detail);
        });

        // Listen for typing indicators
        window.addEventListener('websocket:typing', (event) => {
            this.handleTypingIndicator(event.detail);
        });

        // Listen for message read status
        window.addEventListener('websocket:message-read', (event) => {
            this.handleMessageRead(event.detail);
        });

        // Listen for user status changes
        window.addEventListener('websocket:user-status', (event) => {
            this.handleUserStatus(event.detail);
        });
    }

    /**
     * Set up periodic cache refresh
     */
    setupCacheRefresh() {
        // Refresh preloaded conversations every 2 minutes
        setInterval(() => {
            this.loadPreloadedConversations();
        }, 120000); // 2 minutes
    }

    /**
     * Handle new message from WebSocket
     */
    handleNewMessage(data) {
        const message = data.message;
        const fromUserId = message.from;
        const toUserId = message.to;
        
        // Update conversation in preloaded data
        this.updateConversationWithMessage(fromUserId, toUserId, message);
        
        // Update current conversation if it matches
        if (this.currentConversation && 
            (this.currentConversation.contact._id === fromUserId || 
             this.currentConversation.contact._id === toUserId)) {
            this.addMessageToCurrentConversation(message);
        }
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('conversation:new-message', {
            detail: { message, fromUserId, toUserId }
        }));
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        const { from_user_id, is_typing } = data;
        
        // Update typing status in UI
        this.updateTypingStatus(from_user_id, is_typing);
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('conversation:typing', {
            detail: data
        }));
    }

    /**
     * Handle message read status
     */
    handleMessageRead(data) {
        const { from_user_id, message_id } = data;
        
        // Update message read status
        this.updateMessageReadStatus(from_user_id, message_id);
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('conversation:message-read', {
            detail: data
        }));
    }

    /**
     * Handle user status change
     */
    handleUserStatus(data) {
        const { user_id, is_online } = data;
        
        // Update user online status
        this.updateUserOnlineStatus(user_id, is_online);
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('conversation:user-status', {
            detail: data
        }));
    }

    /**
     * Update conversation with new message
     */
    updateConversationWithMessage(fromUserId, toUserId, message) {
        const conversation = this.preloadedConversations.find(conv => 
            conv.contact._id === fromUserId || conv.contact._id === toUserId
        );
        
        if (conversation) {
            // Add message to conversation
            conversation.messages.push(message);
            
            // Update last message
            conversation.last_message = message;
            conversation.last_message_time = message.created_at;
            
            // Update unread count if message is from other user
            if (fromUserId !== window.websocketService?.connectionInfo?.user_id) {
                conversation.unread_count++;
            }
            
            // Sort messages by created_at
            conversation.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        }
    }

    /**
     * Add message to current conversation
     */
    addMessageToCurrentConversation(message) {
        if (this.currentConversation) {
            this.currentConversation.messages.push(message);
            this.currentConversation.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        }
    }

    /**
     * Update typing status
     */
    updateTypingStatus(userId, isTyping) {
        // Find conversation with this user
        const conversation = this.preloadedConversations.find(conv => 
            conv.contact._id === userId
        );
        
        if (conversation) {
            conversation.is_typing = isTyping;
        }
        
        // Update current conversation if it matches
        if (this.currentConversation && this.currentConversation.contact._id === userId) {
            this.currentConversation.is_typing = isTyping;
        }
    }

    /**
     * Update message read status
     */
    updateMessageReadStatus(fromUserId, messageId) {
        // This would update the read status of a specific message
        // Implementation depends on your message structure
        console.log(`Message ${messageId} from ${fromUserId} was read`);
    }

    /**
     * Update user online status
     */
    updateUserOnlineStatus(userId, isOnline) {
        // Find conversation with this user
        const conversation = this.preloadedConversations.find(conv => 
            conv.contact._id === userId
        );
        
        if (conversation) {
            conversation.contact.is_online = isOnline;
        }
        
        // Update current conversation if it matches
        if (this.currentConversation && this.currentConversation.contact._id === userId) {
            this.currentConversation.contact.is_online = isOnline;
        }
    }

    /**
     * Update conversation list in UI
     */
    updateConversationList() {
        // Sort conversations by last message time
        this.preloadedConversations.sort((a, b) => {
            const timeA = new Date(a.last_message_time || 0);
            const timeB = new Date(b.last_message_time || 0);
            return timeB - timeA;
        });
        
        // Trigger custom event for UI update
        window.dispatchEvent(new CustomEvent('conversation:list-updated', {
            detail: { conversations: this.preloadedConversations }
        }));
    }

    /**
     * Get conversation by user ID
     */
    getConversation(userId) {
        return this.preloadedConversations.find(conv => conv.contact._id === userId);
    }

    /**
     * Set current conversation
     */
    setCurrentConversation(userId) {
        this.currentConversation = this.getConversation(userId);
        
        // If not in preloaded data, load it
        if (!this.currentConversation) {
            this.loadConversation(userId);
        }
        
        return this.currentConversation;
    }

    /**
     * Load specific conversation
     */
    async loadConversation(userId) {
        try {
            const response = await fetch(`/conversation/${userId}`);
            const messages = await response.json();
            
            // Get contact info
            const contact = this.preloadedConversations.find(conv => 
                conv.contact._id === userId
            )?.contact;
            
            if (contact) {
                this.currentConversation = {
                    contact,
                    messages,
                    unread_count: 0,
                    last_message: messages[messages.length - 1],
                    last_message_time: messages[messages.length - 1]?.created_at
                };
            }
        } catch (error) {
            console.error('Failed to load conversation:', error);
        }
    }

    /**
     * Send message
     */
    async sendMessage(toUserId, text) {
        try {
            const response = await fetch('/conversation/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    contact_id: toUserId,
                    text: text
                })
            });
            
            const message = await response.json();
            
            // Add message to current conversation
            if (this.currentConversation) {
                this.addMessageToCurrentConversation(message);
            }
            
            return message;
        } catch (error) {
            console.error('Failed to send message:', error);
            throw error;
        }
    }

    /**
     * Mark conversation as read
     */
    async markAsRead(userId) {
        try {
            const response = await fetch(`/conversation/${userId}`);
            
            // Update unread count
            const conversation = this.getConversation(userId);
            if (conversation) {
                conversation.unread_count = 0;
            }
            
            return await response.json();
        } catch (error) {
            console.error('Failed to mark as read:', error);
        }
    }

    /**
     * Get all conversations
     */
    getConversations() {
        return this.preloadedConversations;
    }

    /**
     * Get current conversation
     */
    getCurrentConversation() {
        return this.currentConversation;
    }

    /**
     * Refresh conversations
     */
    async refresh() {
        await this.loadPreloadedConversations();
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.conversationManager = new ConversationManager();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConversationManager;
}

