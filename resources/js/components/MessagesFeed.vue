<template>
    <div class="feed" ref="feed">
        <div v-if="contact">
            <div
                :class="`message-bubble ${message.to === contact._id ? 'message-sent' : 'message-received'}`"
                v-for="message in messages" :key="message._id" v-if="messages.length > 0">

                <div class="message-content">
                    <div class="message-text">{{ message.text }}</div>
                    <div class="message-meta">
                        <span class="message-time">{{ timeAgo(message.created_at) }}</span>
                        <span class="message-status" v-if="message.to === contact._id">
                            <i class="fas fa-check-double text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center" v-else>
                <div class="empty-state text-center">
                    <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted">Select a contact to start chatting</p>
                </div>
            </div>
        </div>
        <div class="h-100 d-flex justify-content-center align-items-center" v-else>
            <div class="empty-state text-center">
                <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted">Select a contact to start chatting</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        contact: {
            type: Object,
        },
        messages: {
            type: Array,
            require: true,
        }
    },
    name: "MessagesFeed",
    data() {
        return {
            MONTH_NAMES: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
        }
    },
    methods: {
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.feed) {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight;
                }
            });
        },
        formatTime(time) {
            return moment(time).format('h:mm a');
        },
        getFormattedDate(date, prefomattedDate = false, hideYear = false) {
            const day = date.getDate();
            const month = this.MONTH_NAMES[date.getMonth()];
            const year = date.getFullYear();
            const hours = date.getHours();
            let minutes = date.getMinutes();

            if (minutes < 10) {
                // Adding leading zero to minutes
                minutes = `0${minutes}`;
            }

            if (prefomattedDate) {
                // Today at 10:20
                // Yesterday at 10:20
                return `${prefomattedDate} at ${hours}:${minutes}`;
            }

            if (hideYear) {
                // 10. January at 10:20
                return `${day}. ${month} at ${hours}:${minutes}`;
            }

            // 10. January 2017. at 10:20
            return `${day}. ${month} ${year}. at ${hours}:${minutes}`;
        },
        timeAgo(dateParam) {
            if (!dateParam) {
                return null;
            }

            const date = typeof dateParam === 'object' ? dateParam : new Date(dateParam);
            const DAY_IN_MS = 86400000; // 24 * 60 * 60 * 1000
            const today = new Date();
            const yesterday = new Date(today - DAY_IN_MS);
            const seconds = Math.round((today - date) / 1000);
            const minutes = Math.round(seconds / 60);
            const isToday = today.toDateString() === date.toDateString();
            const isYesterday = yesterday.toDateString() === date.toDateString();
            const isThisYear = today.getFullYear() === date.getFullYear();


            if (seconds < 5) {
                return 'now';
            } else if (seconds < 60) {
                return `${seconds} seconds ago`;
            } else if (seconds < 90) {
                return 'about a minute ago';
            } else if (minutes < 60) {
                return `${minutes} minutes ago`;
            } else if (isToday) {
                return this.getFormattedDate(date, 'Today'); // Today at 10:20
            } else if (isYesterday) {
                return this.getFormattedDate(date, 'Yesterday'); // Yesterday at 10:20
            } else if (isThisYear) {
                return this.getFormattedDate(date, false, true); // 10. January at 10:20
            }

            return this.getFormattedDate(date); // 10. January 2017. at 10:20
        },

    },
    watch: {
        contact: function (newContact, oldContact) {
            if (newContact && newContact !== oldContact) {
                this.scrollToBottom();
            }
        },
        messages: {
            handler: function (newMessages, oldMessages) {
                if (newMessages && newMessages.length > 0) {
                    // Auto-scroll when new messages are added
                    this.scrollToBottom();
                }
            },
            deep: true
        },
    },
    mounted() {
        // Scroll to bottom when component is mounted
        this.scrollToBottom();
    },
}
</script>

<style lang="scss" scoped>
.feed {
    height: 60vh;
    max-height: 60vh;
    overflow-y: auto;
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.message-bubble {
    margin-bottom: 15px;
    max-width: 70%;
    animation: slideIn 0.3s ease;
}

.message-sent {
    margin-left: auto;
}

.message-received {
    margin-right: auto;
}

.message-content {
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message-sent .message-content {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-received .message-content {
    background: white;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}

.message-text {
    font-size: 0.95rem;
    line-height: 1.4;
    margin-bottom: 4px;
    word-wrap: break-word;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-time {
    font-weight: 500;
}

.message-status {
    margin-left: 8px;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
}

.empty-state i {
    opacity: 0.5;
    color: #9ca3af;
}

.empty-state p {
    color: #6b7280;
    font-size: 1rem;
    margin-top: 16px;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

@media screen and (max-width: 768px) {
    .message-bubble {
        max-width: 85%;
    }
    
    .feed {
        padding: 15px;
    }
}
</style>
