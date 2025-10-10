<template>
    <div class="chat-app">
        <ContactsList :contacts="contacts" @selected="startConversationWith"/>
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    </div>
</template>

<script>
import Conversation from "./Conversation.vue";
import ContactsList from "./ContactsList.vue";

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
        }
    },
    methods: {
        startConversationWith(contact) {
            this.updateUnreadCount(contact, true)
            axios.get(`/conversation/${contact._id}`)
                .then(response => {
                    this.messages = response.data;
                    this.selectedContact = contact;
                })
        },
        saveNewMessage(message) {
            this.messages.push(message);
        },
        handleIncoming(data) {
            const message = data.message;
            const fromContact = data.from_contact;
            
            if (this.selectedContact && message.from === this.selectedContact._id) {
                this.messages.push(message);
                return;
            }

            //unread messages
            this.updateUnreadCount(fromContact, false);
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
        }
    },
    mounted() {
        Echo.private(`messages.${this.user._id}`)
            .listen('NewMessage', (e) => {
                this.handleIncoming(e);
            })

        axios.get('/contacts')
            .then(response => {
                this.contacts = response.data;
            });
    },
    components: {
        Conversation,
        ContactsList
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
