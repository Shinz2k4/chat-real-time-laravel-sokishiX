<template>
    <div id="emojyPicker"></div>
    <div class="message-composer">
        <div class="composer-input-group">
            <button class="btn btn-outline-secondary emoji-btn" @click="pickEmojy" type="button">
                <i class="fas fa-smile"></i>
            </button>
            <div class="input-wrapper">
                <textarea 
                    class="form-control message-input" 
                    placeholder="Type your message here..." 
                    v-model="message"
                    @keydown.enter="send" 
                    rows="1" 
                    style="resize: none">
                </textarea>
            </div>
            <button class="btn btn-primary send-btn" type="button" @click="send" :disabled="!message.trim()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</template>

<script>
import 'boxicons';
import {createPicker} from 'picmo';

export default {
    name: "MessageComposer",
    data() {
        return {
            message: '',
            toggle_emojy: false,
        }
    },
    methods: {
        send(e) {
            e.preventDefault();
            if (this.message === '') {
                return;
            }
            this.$emit('send', this.message);
            this.message = '';
        },
        pickEmojy() {
            const rootElement = document.querySelector('#emojyPicker');
            if (this.toggle_emojy) {
                rootElement.innerHTML = '';
                this.toggle_emojy = false;
                return;
            }
            const picker = createPicker({rootElement});
            this.toggle_emojy = true;

            // The picker emits an event when an emoji is selected. Do with it as you will!
            picker.addEventListener('emoji:select', event => {
                this.message += event.emoji;
            });
        }
    },
}
</script>

<style scoped>
.message-composer {
    padding: 20px;
    background: white;
    border-top: 1px solid #e5e7eb;
}

.composer-input-group {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: #f8fafc;
    padding: 12px;
    border-radius: 20px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.composer-input-group:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background: white;
}

.emoji-btn {
    border-radius: 12px;
    padding: 8px 12px;
    border: none;
    background: white;
    color: #6b7280;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.emoji-btn:hover {
    background: #6366f1;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.input-wrapper {
    flex: 1;
}

.message-input {
    border: none;
    background: transparent;
    resize: none;
    outline: none;
    font-size: 0.95rem;
    line-height: 1.4;
    max-height: 120px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.message-input::placeholder {
    color: #9ca3af;
}

.send-btn {
    border-radius: 12px;
    padding: 8px 16px;
    border: none;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    transition: all 0.3s ease;
    min-width: 50px;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.send-btn:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

@media screen and (max-width: 768px) {
    .message-composer {
        padding: 15px;
    }
    
    .composer-input-group {
        padding: 10px;
        gap: 8px;
    }
}
</style>
