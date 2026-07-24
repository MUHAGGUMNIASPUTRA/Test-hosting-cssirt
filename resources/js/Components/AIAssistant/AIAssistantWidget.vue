<script setup>
// File: resources/js/Components/AIAssistant/AIAssistantWidget.vue
// Root widget: tombol floating + chat popup overlay.
// Komponen ini diintegrasikan di AppLayout dan SEOLayout.

import { onMounted, onUnmounted, ref } from 'vue'
import AIAssistantChat from './AIAssistantChat.vue'

/** Apakah chat popup sedang terbuka */
const isOpen = ref(false)

/** Apakah animasi "attention pulse" sedang aktif (muncul setelah 3 detik) */
const showPulse = ref(false)

let pulseTimer = null

onMounted(() => {
  // Tampilkan pulse setelah 3 detik untuk menarik perhatian
  pulseTimer = setTimeout(() => {
    if (!isOpen.value) showPulse.value = true
  }, 3000)
})

onUnmounted(() => {
  if (pulseTimer) clearTimeout(pulseTimer)
})

function openChat() {
  isOpen.value = true
  showPulse.value = false
}

function closeChat() {
  isOpen.value = false
}

function toggleChat() {
  if (isOpen.value) {
    closeChat()
  } else {
    openChat()
  }
}

/** Tutup saat klik backdrop (area di luar popup di mobile) */
function handleBackdropClick(e) {
  if (e.target === e.currentTarget) {
    closeChat()
  }
}

/** Tutup dengan Escape */
function handleKeyEscape(e) {
  if (e.key === 'Escape' && isOpen.value) {
    closeChat()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyEscape)
})
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyEscape)
})
</script>

<template>
  <!-- Backdrop (mobile) -->
  <Transition name="ai-backdrop">
    <div
      v-if="isOpen"
      class="ai-widget-backdrop"
      @click="handleBackdropClick"
    />
  </Transition>

  <!-- Chat Popup -->
  <Transition name="ai-popup">
    <div v-if="isOpen" class="ai-widget-popup" role="dialog" aria-label="AI Knowledge Assistant">
      <AIAssistantChat @close="closeChat" />
    </div>
  </Transition>

  <!-- Floating Button -->
  <div class="ai-widget-fab-wrapper">
    <!-- Tooltip -->
    <Transition name="ai-tooltip">
      <div v-if="showPulse && !isOpen" class="ai-widget-tooltip">
        💬 Tanya AI Assistant
        <div class="ai-widget-tooltip__arrow" />
      </div>
    </Transition>

    <!-- FAB Button -->
    <button
      class="ai-widget-fab"
      :class="{ 'ai-widget-fab--open': isOpen }"
      :aria-expanded="isOpen"
      aria-label="Buka AI Knowledge Assistant"
      @click="toggleChat"
    >
      <!-- Pulse ring -->
      <span v-if="showPulse && !isOpen" class="ai-widget-fab__pulse" />

      <!-- Icon: Chat (saat tutup) -->
      <Transition name="ai-icon" mode="out-in">
        <svg v-if="!isOpen" key="chat" viewBox="0 0 24 24" fill="none" class="ai-fab-icon">
          <path
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <!-- AI sparkle dots -->
          <circle cx="8" cy="12" r="1" fill="currentColor" />
          <circle cx="12" cy="12" r="1" fill="currentColor" />
          <circle cx="16" cy="12" r="1" fill="currentColor" />
        </svg>

        <!-- Icon: X (saat buka) -->
        <svg v-else key="close" viewBox="0 0 24 24" fill="none" class="ai-fab-icon">
          <path
            d="M6 18L18 6M6 6l12 12"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
          />
        </svg>
      </Transition>

      <!-- AI label badge -->
      <span v-if="!isOpen" class="ai-widget-fab__badge">AI</span>
    </button>
  </div>
</template>

<style scoped>
/* ===== BACKDROP (mobile) ===== */
.ai-widget-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  z-index: 9997;
  backdrop-filter: blur(2px);
}

/* ===== POPUP ===== */
.ai-widget-popup {
  position: fixed;
  bottom: 90px;
  right: 20px;
  width: 370px;
  height: 580px;
  max-height: calc(100vh - 110px);
  z-index: 9998;
  border-radius: 20px;
  box-shadow:
    0 25px 60px rgba(0, 0, 0, 0.2),
    0 10px 30px rgba(99, 102, 241, 0.15);
  overflow: hidden;
  border: 1px solid rgba(99, 102, 241, 0.15);
}

@media (max-width: 480px) {
  .ai-widget-popup {
    width: calc(100vw - 24px);
    right: 12px;
    bottom: 80px;
    height: calc(100vh - 100px);
    max-height: none;
    border-radius: 16px;
  }
}

/* ===== FAB WRAPPER ===== */
.ai-widget-fab-wrapper {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

/* ===== FAB BUTTON ===== */
.ai-widget-fab {
  position: relative;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #0ea5e9 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow:
    0 6px 20px rgba(79, 70, 229, 0.45),
    0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  outline: none;
}
.ai-widget-fab:hover {
  transform: scale(1.1) translateY(-2px);
  box-shadow:
    0 10px 30px rgba(79, 70, 229, 0.55),
    0 4px 12px rgba(0, 0, 0, 0.2);
}
.ai-widget-fab:active {
  transform: scale(0.96);
}
.ai-widget-fab--open {
  background: linear-gradient(135deg, #374151, #4b5563);
  box-shadow:
    0 4px 15px rgba(0, 0, 0, 0.3);
}
.ai-fab-icon {
  width: 26px;
  height: 26px;
  transition: all 0.2s ease;
}

/* AI badge */
.ai-widget-fab__badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: linear-gradient(135deg, #f59e0b, #ef4444);
  color: #fff;
  font-size: 9px;
  font-weight: 800;
  padding: 2px 4px;
  border-radius: 6px;
  line-height: 1;
  letter-spacing: 0.05em;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Pulse ring */
.ai-widget-fab__pulse {
  position: absolute;
  inset: -6px;
  border-radius: 50%;
  background: rgba(99, 102, 241, 0.25);
  animation: fab-pulse 2s ease-in-out infinite;
}
@keyframes fab-pulse {
  0%, 100% { transform: scale(1); opacity: 0.6; }
  50% { transform: scale(1.15); opacity: 0.2; }
}

/* ===== TOOLTIP ===== */
.ai-widget-tooltip {
  position: relative;
  background: #1e293b;
  color: #fff;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 500;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  cursor: pointer;
}
.ai-widget-tooltip__arrow {
  position: absolute;
  bottom: -5px;
  right: 22px;
  width: 10px;
  height: 10px;
  background: #1e293b;
  transform: rotate(45deg);
  border-radius: 1px;
}

/* ===== TRANSITIONS ===== */

/* Popup */
.ai-popup-enter-active {
  animation: popup-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.ai-popup-leave-active {
  animation: popup-out 0.2s ease-in;
}
@keyframes popup-in {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.92);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
@keyframes popup-out {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(16px) scale(0.94);
  }
}

/* Backdrop */
.ai-backdrop-enter-active,
.ai-backdrop-leave-active {
  transition: opacity 0.2s ease;
}
.ai-backdrop-enter-from,
.ai-backdrop-leave-to {
  opacity: 0;
}

/* Icon swap */
.ai-icon-enter-active,
.ai-icon-leave-active {
  transition: all 0.15s ease;
}
.ai-icon-enter-from {
  opacity: 0;
  transform: rotate(-90deg) scale(0.5);
}
.ai-icon-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.5);
}

/* Tooltip */
.ai-tooltip-enter-active {
  animation: tooltip-in 0.3s ease;
}
.ai-tooltip-leave-active {
  animation: tooltip-out 0.2s ease;
}
@keyframes tooltip-in {
  from { opacity: 0; transform: translateX(10px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes tooltip-out {
  from { opacity: 1; transform: translateX(0); }
  to { opacity: 0; transform: translateX(10px); }
}
</style>
