<template>
    <div class="conversation w-100">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white d-flex align-items-center gap-4">
                <div class="contact-avatar d-flex align-items-center justify-content-center" v-if="contact">
                    <img :src="'storage/profile_images/' + contact.profile_image" :alt="contact.name"
                         class="rounded-circle contact-image" v-if="contact.profile_image">
                    <div class="avatar-placeholder bg-white text-primary d-flex align-items-center justify-content-center rounded-circle" v-else>
                        {{ contact.name ? contact.name.charAt(0).toUpperCase() : 'U' }}
                    </div>
                </div>
                <div class="contact-details d-flex flex-column">
                    <span class="contact-name fw-bold">{{ contact ? contact.name : "SokishiX Chat" }}</span>
                    <span class="contact-status text-light opacity-75">
                        <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                        {{ contact ? 'Online' : 'Select a contact' }}
                    </span>
                </div>
            </div>
            <div class="card-body pb-0">
                <MessagesFeed :contact="contact" :messages="messages"/>
                <MessageComposer @send="sendMessage"/>
            </div>
        </div>
    </div>
</template>

<script>
import MessagesFeed from "./MessagesFeed.vue";
import MessageComposer from "./MessageComposer.vue";

export default {
    props: {
        contact: {
            type: Object,
            default: null,
        },
        messages: {
            type: Array,
            default: [],
        }
    },
    data() {
        return {}
    },
    methods: {
        sendMessage(text) {
            if (!this.contact) {
                return;
            }
            axios.post('/conversation/send', {
                contact_id: this.contact._id,
                text: text,
            }).then(response => {
                this.$emit('new', response.data);
            })
        }
    },
    components: {
        MessagesFeed,
        MessageComposer
    },
    mounted() {
        console.log(this.contact);
        console.log('Component mounted.')
    }
}
</script>

<style lang="scss" scoped>
.conversation {
    flex: 1;
    min-width: 0;
}

.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.contact-avatar .contact-image,
.contact-avatar .avatar-placeholder {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.contact-avatar .avatar-placeholder {
    font-weight: 700;
    font-size: 1.2rem;
    background: white;
    color: #6366f1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-name {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 4px;
}

.contact-status {
    font-size: 0.875rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}

.contact-status i {
    color: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.card-body {
    padding: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

@media screen and (max-width: 768px) {
    .card-header {
        padding: 15px;
        gap: 12px;
    }
    
    .contact-avatar .contact-image,
    .contact-avatar .avatar-placeholder {
        width: 40px;
        height: 40px;
    }
    
    .contact-name {
        font-size: 1rem;
    }
}
</style>
