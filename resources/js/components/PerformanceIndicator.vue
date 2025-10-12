<template>
    <div class="performance-indicator" v-if="showIndicator">
        <div class="indicator-content">
            <div class="indicator-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="indicator-text">
                <span class="indicator-label">{{ indicatorText }}</span>
                <span class="indicator-time">{{ switchTime }}ms</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PerformanceIndicator",
    data() {
        return {
            showIndicator: false,
            switchTime: 0,
            indicatorText: '',
            timeout: null
        }
    },
    methods: {
        showSwitchTime(time, isPreloaded = false) {
            this.switchTime = time;
            this.indicatorText = isPreloaded ? 'Instant Switch' : 'API Call';
            this.showIndicator = true;
            
            // Hide after 2 seconds
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.showIndicator = false;
            }, 2000);
        },
        showLoading() {
            this.indicatorText = 'Loading...';
            this.switchTime = 0;
            this.showIndicator = true;
        },
        hide() {
            this.showIndicator = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
        }
    },
    beforeUnmount() {
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
    }
}
</script>

<style scoped>
.performance-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.indicator-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.indicator-icon {
    font-size: 16px;
}

.indicator-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.indicator-label {
    font-size: 12px;
    font-weight: 500;
    opacity: 0.9;
}

.indicator-time {
    font-size: 14px;
    font-weight: 700;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .performance-indicator {
        top: 10px;
        right: 10px;
        padding: 8px 12px;
    }
    
    .indicator-label {
        font-size: 11px;
    }
    
    .indicator-time {
        font-size: 12px;
    }
}
</style>

