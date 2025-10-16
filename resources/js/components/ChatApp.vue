<template>
    <div class="chat-app">
        <PerformanceIndicator ref="performanceIndicator"/>

        <aside class="sidebar">
            <button class="tab-btn" :class="{active: currentTab==='chats'}" @click="switchTab('chats')">
                <i class="fas fa-comments"></i>
                <span>Chats</span>
            </button>
            <button class="tab-btn" :class="{active: currentTab==='friends'}" @click="switchTab('friends')">
                <i class="fas fa-user-friends"></i>
                <span>Friends</span>
                <span v-if="incomingCount>0" class="badge bg-danger">{{ incomingCount }}</span>
            </button>
            <!-- Future tabs can be added here -->
        </aside>

        <section class="content">
            <div v-show="currentTab==='friends'" class="panel-wrapper">
                <FriendsPanel ref="friendsPanel" :user="user" @friends-changed="reloadContacts" @incoming-count="incomingCount=$event"/>
            </div>
            <div v-show="currentTab==='chats'" class="panel-wrapper chats-wrapper">
                <ContactsList :contacts="contacts" @selected="startConversationWith"/>
                <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
            </div>
        </section>
    </div>
</template>

<script>
import Conversation from "./Conversation.vue";
import ContactsList from "./ContactsList.vue";
import PerformanceIndicator from "./PerformanceIndicator.vue";
import FriendsPanel from "./FriendsPanel.vue";

export default {
    props: {
        user: {
            Object,
            require: true
        }
    },
    name: "ChatApp",
    data() {
        return {
            selectedContact: null,
            contacts: [],
            messages: [],
            preloadedConversations: new Map(), // Cache preloaded conversations
            isLoading: false,
            currentTab: 'chats',
            incomingCount: 0,
        }
    },
    methods: {
        switchTab(tab){
            this.currentTab = tab;
            try { localStorage.setItem('sx_current_tab', tab); } catch(e) {}
        },
        getIdString(value) {
            if (!value) return '';
            return (value.$oid ? value.$oid : value).toString();
        },
        startConversationWith(contact) {
            const startTime = performance.now();
            this.updateUnreadCount(contact, true);
            
            // Check if conversation is in cache
            const contactId = this.getIdString(contact._id);
            const cachedConversation = window.conversationCache?.get(contactId);
            
            if (cachedConversation) {
                // Use cached data - instant switch
                this.messages = cachedConversation.messages || [];
                this.selectedContact = contact;
                
                const endTime = performance.now();
                const switchTime = Math.round(endTime - startTime);
                
                console.log(`✅ Using cached conversation - ${switchTime}ms`);
                this.$refs.performanceIndicator?.showSwitchTime(switchTime, true);
            } else {
                // Fallback to API call for non-cached conversations
                this.isLoading = true;
                this.$refs.performanceIndicator?.showLoading();
                
                axios.get(`/conversation/${contact._id}`)
                    .then(response => {
                        this.messages = response.data;
                        this.selectedContact = contact;
                        this.isLoading = false;
                        
                        // Cache the conversation for future use
                        if (window.conversationCache) {
                            window.conversationCache.set(contactId, {
                                contact,
                                messages: response.data,
                                unread_count: 0,
                                last_message: response.data[response.data.length - 1],
                                last_message_time: response.data[response.data.length - 1]?.created_at,
                                cached_at: Date.now()
                            });
                        }
                        
                        const endTime = performance.now();
                        const switchTime = Math.round(endTime - startTime);
                        
                        console.log(`⚠️ Using API call - ${switchTime}ms`);
                        this.$refs.performanceIndicator?.showSwitchTime(switchTime, false);
                    })
                    .catch(error => {
                        console.error('Error loading conversation:', error);
                        this.isLoading = false;
                        this.$refs.performanceIndicator?.hide();
                    });
            }
        },
        saveNewMessage(message) {
            const selectedId = this.getIdString(this.selectedContact && this.selectedContact._id);
            const toId = this.getIdString(message && message.to);
            if (selectedId && toId === selectedId) {
                this.messages.push(message);
            }
        },
        handleIncoming(data) {
            const message = data.message;
            const fromContact = data.from_contact || {};
            const selectedId = this.getIdString(this.selectedContact && this.selectedContact._id);
            const fromId = this.getIdString(message && message.from);

            if (this.selectedContact && fromId === selectedId) {
                this.messages.push(message);
                return;
            }

            // Update cached conversation with new message
            if (fromContact && fromContact._id) {
                const contactId = this.getIdString(fromContact._id);
                
                // Update conversation cache
                if (window.conversationCache) {
                    window.conversationCache.updateWithMessage(contactId, message);
                }
                
                this.updateUnreadCount(fromContact, false);
            }
        },
        updateUnreadCount(contact, reset) {
            this.contacts = this.contacts.map((single) => {
                if (single._id !== contact._id) {
                    return single;
                }
                if (reset) {
                    single.unread = 0;
                } else {
                    single.unread += 1;
                }
                return single;
            })
        },
        async loadContactsAndPreload() {
            try {
                // Load contacts first
                const contactsResponse = await axios.get('/contacts');
                this.contacts = contactsResponse.data;
                
                // Initialize conversation cache
                if (window.conversationCache) {
                    await window.conversationCache.initialize();
                }
                
                // Auto-select first contact if available
                if (this.contacts.length > 0) {
                    this.startConversationWith(this.contacts[0]);
                }
                
            } catch (error) {
                console.error('Error loading contacts and preloaded conversations:', error);
            }
        },
        async reloadContacts(){
            try{
                const contactsResponse = await axios.get('/contacts');
                this.contacts = contactsResponse.data;
            }catch(e){ console.error(e); }
        },
        // Update preloaded conversation when new message arrives
        updatePreloadedConversation(contactId, message) {
            if (this.preloadedConversations.has(contactId)) {
                const conv = this.preloadedConversations.get(contactId);
                conv.messages.push(message);
                conv.last_message = message;
                conv.last_message_time = message.created_at;
                this.preloadedConversations.set(contactId, conv);
            }
        },
        // Refresh preloaded conversations periodically
        async refreshPreloadedConversations() {
            try {
                if (window.conversationCache) {
                    await window.conversationCache.refresh();
                    console.log('🔄 Refreshed conversation cache');
                }
            } catch (error) {
                console.error('Error refreshing conversation cache:', error);
            }
        },
        // Check if conversation is preloaded
        isConversationPreloaded(contactId) {
            return this.preloadedConversations.has(contactId);
        },
        
        // Handle friend request sent event
        handleFriendRequestSent(data) {
            console.log('🔔 New friend request received:', data);
            
            // Show notification
            if (data.message) {
                this.showNotification(data.message, 'info');
            }
            
            // Refresh friends panel if it's visible
            if (this.currentTab === 'friends') {
                this.$refs.friendsPanel?.refreshFromWebSocket();
            }
            
            // Update incoming count
            this.incomingCount++;
        },
        
        // Handle friend request responded event
        handleFriendRequestResponded(data) {
            console.log('🔔 Friend request response received:', data);
            
            // Show notification
            if (data.message) {
                this.showNotification(data.message, data.action === 'accept' ? 'success' : 'warning');
            }
            
            // Refresh friends panel if it's visible
            if (this.currentTab === 'friends') {
                this.$refs.friendsPanel?.refreshFromWebSocket();
            }
            
            // Reload contacts if accepted
            if (data.action === 'accept') {
                this.reloadContacts();
            }
        },
        
        // Show notification
        showNotification(message, type = 'info') {
            // Simple notification - you can replace with your preferred notification library
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    },
    mounted() {
        try { const saved = localStorage.getItem('sx_current_tab'); if (saved) this.currentTab = saved; } catch(e) {}
        const userId = this.getIdString(this.user && this.user._id);
        Echo.private(`messages.${userId}`)
            .listen('NewMessage', (e) => {
                this.handleIncoming(e);
            })
            
        // Listen for friend request events
        Echo.private(`friend-requests.${userId}`)
            .listen('FriendRequestSent', (e) => {
                this.handleFriendRequestSent(e);
            })
            .listen('FriendRequestResponded', (e) => {
                this.handleFriendRequestResponded(e);
            })

        // Load contacts and preloaded conversations
        this.loadContactsAndPreload();
        
        // Set up periodic refresh of preloaded conversations (every 2 minutes)
        this.refreshInterval = setInterval(() => {
            this.refreshPreloadedConversations();
        }, 120000); // 2 minutes
    },
    beforeUnmount() {
        // Clean up interval
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    },
    components: {
        Conversation,
        ContactsList,
        PerformanceIndicator,
        FriendsPanel
    }
}
</script>

<style lang="scss" scoped>

.chat-app {
    display: flex;
    gap: 20px;
    flex-direction: row;
    min-height: 70vh;
    padding: 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sidebar {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 200px;
    max-width: 220px;
}
.tab-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    padding: 12px 14px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    text-align: left;
}
.tab-btn i { color: #4b5563; }
.tab-btn.active {
    background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
    color: #fff;
    border-color: #6366f1;
    box-shadow: 0 4px 14px rgba(99,102,241,0.35);
}
.content {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.panel-wrapper {
    display: flex;
    gap: 20px;
    width: 100%;
}
.chats-wrapper {
    align-items: flex-start;
}

@media screen and (max-width: 1016px) {
    .chat-app {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }
    .sidebar { flex-direction: row; min-width: 100%; max-width: 100%; }
}
</style>
