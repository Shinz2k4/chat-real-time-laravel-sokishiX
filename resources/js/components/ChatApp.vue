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
        getIdString(value) {
            if (!value) return '';
            return (value.$oid ? value.$oid : value).toString();
        },
        startConversationWith(contact) {
            this.updateUnreadCount(contact, true)
            axios.get(`/conversation/${contact._id}`)
                .then(response => {
                    this.messages = response.data;
                    this.selectedContact = contact;
                })
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

            //unread messages
            if (fromContact && fromContact._id) {
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
        }
    },
    mounted() {
        const userId = this.getIdString(this.user && this.user._id);
        Echo.private(`messages.${userId}`)
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
