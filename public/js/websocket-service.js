/**
 * WebSocket Service for Laravel Chat App
 * Handles persistent connections and real-time features
 */
class WebSocketService {
    constructor() {
        this.pusher = null;
        this.channel = null;
        this.presenceChannel = null;
        this.isConnected = false;
        this.connectionInfo = null;
        this.typingTimeout = null;
        this.typingUsers = new Set();
        
        this.init();
    }

    /**
     * Initialize WebSocket connection
     */
    async init() {
        try {
            // Get connection info from server
            const response = await fetch('/websocket/info');
            this.connectionInfo = await response.json();
            
            // Initialize Pusher
            this.pusher = new Pusher(this.connectionInfo.connection_info.pusher_key, {
                cluster: this.connectionInfo.connection_info.pusher_cluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            // Subscribe to user's personal channel
            this.channel = this.pusher.subscribe(this.connectionInfo.user_channel);
            
            // Subscribe to presence channel for online status
            this.presenceChannel = this.pusher.subscribe('presence-chat');
            
            // Set up event listeners
            this.setupEventListeners();
            
            // Mark user as online
            await this.setOnlineStatus(true);
            
            this.isConnected = true;
            console.log('✅ WebSocket connected successfully');
            
        } catch (error) {
            console.error('❌ WebSocket connection failed:', error);
            this.isConnected = false;
        }
    }

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Listen for new messages
        this.channel.bind('new-message', (data) => {
            this.handleNewMessage(data);
        });

        // Listen for typing indicators
        this.channel.bind('typing', (data) => {
            this.handleTypingIndicator(data);
        });

        // Listen for message read status
        this.channel.bind('message_read', (data) => {
            this.handleMessageRead(data);
        });

        // Listen for user status changes
        this.presenceChannel.bind('user_status', (data) => {
            this.handleUserStatus(data);
        });

        // Listen for connection state changes
        this.pusher.connection.bind('state_change', (states) => {
            console.log('WebSocket state changed:', states.previous, '->', states.current);
            this.isConnected = states.current === 'connected';
        });
    }

    /**
     * Handle new message
     */
    handleNewMessage(data) {
        // Trigger custom event for the app to handle
        window.dispatchEvent(new CustomEvent('websocket:new-message', {
            detail: data
        }));
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        if (data.is_typing) {
            this.typingUsers.add(data.from_user_id);
        } else {
            this.typingUsers.delete(data.from_user_id);
        }

        // Trigger custom event
        window.dispatchEvent(new CustomEvent('websocket:typing', {
            detail: data
        }));
    }

    /**
     * Handle message read status
     */
    handleMessageRead(data) {
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('websocket:message-read', {
            detail: data
        }));
    }

    /**
     * Handle user status change
     */
    handleUserStatus(data) {
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('websocket:user-status', {
            detail: data
        }));
    }

    /**
     * Send typing indicator
     */
    async sendTyping(toUserId, isTyping = true) {
        try {
            const response = await fetch('/websocket/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_user_id: toUserId,
                    is_typing: isTyping
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Failed to send typing indicator:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Send message read status
     */
    async markMessageRead(toUserId, messageId) {
        try {
            const response = await fetch('/websocket/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_user_id: toUserId,
                    message_id: messageId
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Failed to mark message as read:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Set user online status
     */
    async setOnlineStatus(isOnline) {
        try {
            const response = await fetch('/websocket/online', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_online: isOnline
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Failed to set online status:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Get preloaded conversations with real-time updates
     */
    async getPreloadedConversations() {
        try {
            const response = await fetch('/websocket/preload-realtime');
            return await response.json();
        } catch (error) {
            console.error('Failed to get preloaded conversations:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Start typing indicator with debounce
     */
    startTyping(toUserId) {
        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        // Send typing indicator
        this.sendTyping(toUserId, true);

        // Set timeout to stop typing
        this.typingTimeout = setTimeout(() => {
            this.stopTyping(toUserId);
        }, 3000); // Stop typing after 3 seconds of inactivity
    }

    /**
     * Stop typing indicator
     */
    stopTyping(toUserId) {
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
            this.typingTimeout = null;
        }
        this.sendTyping(toUserId, false);
    }

    /**
     * Disconnect WebSocket
     */
    async disconnect() {
        if (this.pusher) {
            await this.setOnlineStatus(false);
            this.pusher.disconnect();
            this.isConnected = false;
            console.log('🔌 WebSocket disconnected');
        }
    }

    /**
     * Get connection status
     */
    getConnectionStatus() {
        return {
            isConnected: this.isConnected,
            connectionInfo: this.connectionInfo,
            typingUsers: Array.from(this.typingUsers)
        };
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.websocketService = new WebSocketService();
    
    // Handle page unload
    window.addEventListener('beforeunload', () => {
        if (window.websocketService) {
            window.websocketService.disconnect();
        }
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = WebSocketService;
}

