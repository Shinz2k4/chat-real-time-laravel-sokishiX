<template>
    <div class="chat-app">
        <PerformanceIndicator ref="performanceIndicator"/>
        <ContactsList :contacts="contacts" @selected="startConversationWith"/>
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    </div>
</template>

<script>
import Conversation from "./Conversation.vue";
import ContactsList from "./ContactsList.vue";
import PerformanceIndicator from "./PerformanceIndicator.vue";

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
        }
    },
    methods: {
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
        }
    },
    mounted() {
        const userId = this.getIdString(this.user && this.user._id);
        Echo.private(`messages.${userId}`)
            .listen('NewMessage', (e) => {
                this.handleIncoming(e);
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
        PerformanceIndicator
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

@media screen and (max-width: 1016px) {
    .chat-app {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }
}
</style>
