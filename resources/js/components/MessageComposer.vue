<template>
    <div id="emojyPicker" class="mb-2"></div>
    <div class="message-composer py-3 px-3 bg-white border-top" style="box-shadow: 0 -1px 4px rgba(0,0,0,0.03);">
        <div class="composer-input-group d-flex align-items-end gap-2">
            <button 
                class="btn btn-outline-secondary emoji-btn d-flex align-items-center justify-content-center" 
                @click="pickEmojy" 
                type="button"
                style="width: 40px; height: 40px; border-radius: 100%;"
                title="Chèn emoji"
            >
                <i class="fas fa-smile fs-5"></i>
            </button>
            <button 
                class="btn btn-outline-secondary emoji-btn d-flex align-items-center justify-content-center" 
                type="button"
                @click="triggerFile"
                style="width: 40px; height: 40px; border-radius: 100%;"
                title="Đính kèm ảnh/tệp"
            >
                <i class="fas fa-paperclip fs-5"></i>
            </button>
            <div class="input-wrapper flex-grow-1">
                <textarea 
                    class="form-control message-input border-0 shadow-none px-3 py-2" 
                    placeholder="Nhập tin nhắn..."
                    v-model="message"
                    @keydown.enter="send" 
                    rows="1"
                    style="resize: none; background: #f9f9fa; border-radius: 18px; min-height: 40px; font-size: 1rem;"
                ></textarea>
                <div v-if="attachment" class="mt-2 d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-2 py-1">
                        <i class="fas fa-file me-1"></i>{{ attachment.name }} ({{ prettySize(attachment.size) }})
                    </span>
                    <button class="btn btn-sm btn-outline-secondary" @click="clearAttachment" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input ref="fileInput" type="file" class="d-none" @change="onFileChange" />
            </div>
            <button 
                class="btn btn-primary send-btn d-flex align-items-center justify-content-center" 
                type="button" 
                @click="send" 
                :disabled="!canSend"
                style="width: 40px; height: 40px; border-radius: 100%;"
                title="Gửi"
            >
                <i class="fas fa-paper-plane fs-5"></i>
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
            attachment: null,
        }
    },
    methods: {
        send(e) {
            e.preventDefault();
            if (!this.canSend) {
                return;
            }
            this.$emit('send', { text: this.message, file: this.attachment });
            this.message = '';
            this.clearAttachment();
        },
        triggerFile() {
            this.$refs.fileInput && this.$refs.fileInput.click();
        },
        onFileChange(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            if (file.size > 20 * 1024 * 1024) {
                alert('Kích thước tệp tối đa 20MB');
                event.target.value = '';
                return;
            }
            this.attachment = file;
        },
        clearAttachment() {
            this.attachment = null;
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
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
        },
        prettySize(size) {
            if (size < 1024) return size + ' B';
            if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB';
            return (size / (1024*1024)).toFixed(1) + ' MB';
        }
    },
    computed: {
        canSend() {
            return (this.message && this.message.trim().length > 0) || !!this.attachment;
        }
    }
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
