<template>
    <div class="contacts-list">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-gradient-dark text-white gap-5">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users me-2"></i>
                    <span class="fw-bold">Contacts</span>
                </div>
                <button class="btn btn-outline-light btn-sm" data-bs-toggle="collapse"
                      data-bs-target="#collapseExample" @click="toggleCollapse">
                    <i class="fas" :class="is_collapsed ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </div>
            <div class="card-body c-list collapse-horizontal" id="collapseExample">
                <div
                    :class="`contact-item m-3 p-3 d-flex align-items-center gap-3 ${contact === selected ? 'selected-contact' : 'contact-card'}`"
                    role="button"
                    v-for="contact in sortedContacts" :key="contact._id" @click="selectContact(contact)">
                    <div class="contact-avatar">
                        <img :src="'storage/profile_images/' + contact.profile_image" :alt="contact.name"
                             class="rounded-circle contact-image" v-if="contact.profile_image">
                        <div class="avatar-placeholder bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" v-else>
                            {{ contact.name ? contact.name.charAt(0).toUpperCase() : 'U' }}
                        </div>
                    </div>
                    <div class="contact-details d-flex flex-column flex-grow-1">
                        <span class="contact-name fw-bold">
                            {{ makeTextShort(contact.name, 20) }}
                        </span>
                        <span class="contact-email text-muted small">
                            {{ makeTextShort(contact.email, 20) }}
                        </span>
                    </div>

                    <!-- Unread Badge -->
                    <div class="unread-badge" v-if="contact.unread">
                        <span class="badge rounded-pill bg-danger">{{ contact.unread }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'boxicons'

export default {
    props: {
        contacts: {
            type: Array,
            default: [],
        }
    },
    name: "ContactsList",
    data() {
        return {
            selected: this.contacts.length > 0 ? this.contacts[0] : null,
            max_text_length: 30,
            is_collapsed: false,
        }
    },
    methods: {
        selectContact(contact) {
            this.selected = contact;
            this.$emit('selected', contact);
        },
        makeTextShort(text, length) {
            return text.length > this.max_text_length ? text.substring(0, length) + '...' : text;
        },
        toggleCollapse() {
            this.is_collapsed = !this.is_collapsed;
        }
    },
    computed: {
        sortedContacts() {
            return _.sortBy(this.contacts, [(contact) => {
                if (contact == this.selected) {
                    return Infinity;
                }
                return contact.unread;
            }]).reverse();
        }
    },

}
</script>

<style lang="scss" scoped>
.contacts-list {
    min-width: 300px;
    max-width: 350px;
}

.c-list {
    height: 100%;
    max-height: 75vh;
    overflow-y: auto;
    padding: 0;
    background: white;
    border-radius: 0 0 15px 15px;
}

.contact-item {
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 12px;
    margin: 8px 12px;
    padding: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.contact-card:hover {
    background: #f1f5f9;
    border-color: #6366f1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}

.selected-contact {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border-color: #6366f1;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.contact-avatar .contact-image,
.contact-avatar .avatar-placeholder {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.contact-avatar .avatar-placeholder {
    font-weight: 700;
    font-size: 1.2rem;
    background: #6366f1;
    color: white;
}

.contact-name {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 4px;
}

.contact-email {
    font-size: 0.875rem;
    opacity: 0.8;
}

.unread-badge .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.card-header {
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
    padding: 15px 20px;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #6366f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #4f46e5;
}

@media screen and (max-width: 1016px) {
    .c-list {
        max-height: 42vh;
    }
    
    .contacts-list {
        min-width: 100%;
        max-width: 100%;
    }
}
</style>
