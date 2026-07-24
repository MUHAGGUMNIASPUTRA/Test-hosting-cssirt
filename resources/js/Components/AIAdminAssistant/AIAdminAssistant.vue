<script setup>
// File: resources/js/Components/AIAdminAssistant/AIAdminAssistant.vue
// Root component: floating button 🤖 + popup panel overlay untuk Admin Dashboard.
// Di-mount di AdminLayout.vue sebagai komponen global admin.

import { onMounted, onUnmounted, ref } from 'vue'
import AIAdminAssistantPanel from './AIAdminAssistantPanel.vue'

/** Apakah panel sedang terbuka */
const isOpen = ref(false)

/** Apakah pulse animation aktif (muncul setelah delay) */
const showPulse = ref(false)

/** Apakah tooltip "AI Admin" sedang ditampilkan */
const showTooltip = ref(false)

let pulseTimer = null
let tooltipTimer = null

onMounted(() => {
  // Tampilkan pulse + tooltip setelah 4 detik
  pulseTimer = setTimeout(() => {
    if (!isOpen.value) {
      showPulse.value = true
      showTooltip.value = true
      // Sembunyikan tooltip setelah 5 detik
      tooltipTimer = setTimeout(() => {
        showTooltip.value = false
      }, 5000)
    }
  }, 4000)

  window.addEventListener('keydown', handleKeyEscape)
})

onUnmounted(() => {
  if (pulseTimer) clearTimeout(pulseTimer)
  if (tooltipTimer) clearTimeout(tooltipTimer)
  window.removeEventListener('keydown', handleKeyEscape)
})

function openPanel() {
  isOpen.value = true
  showPulse.value = false
  showTooltip.value = false
}

function closePanel() {
  isOpen.value = false
}

function togglePanel() {
  if (isOpen.value) closePanel()
  else openPanel()
}

function handleBackdropClick(e) {
  if (e.target === e.currentTarget) {
    closePanel()
  }
}

function handleKeyEscape(e) {
  if (e.key === 'Escape' && isOpen.value) {
    closePanel()
  }
}
</script>

<template>
  <!-- Backdrop (klik di luar panel menutup popup) -->
  <Transition name="aiaa-backdrop">
    <div
      v-if="isOpen"
      class="aiaa-backdrop"
      @click="handleBackdropClick"
    />
  </Transition>

  <!-- Panel Popup -->
  <Transition name="aiaa-popup">
    <div
      v-if="isOpen"
      class="aiaa-popup"
      role="dialog"
      aria-label="AI Admin Assistant"
      aria-modal="true"
    >
      <AIAdminAssistantPanel @close="closePanel" />
    </div>
  </Transition>

  <!-- Floating Button Wrapper -->
  <div class="aiaa-fab-wrapper">
    <!-- Tooltip -->
    <Transition name="aiaa-tooltip">
      <div
        v-if="showTooltip && !isOpen"
        class="aiaa-tooltip"
        @click="openPanel"
      >
        🤖 AI Admin Assistant
        <div class="aiaa-tooltip__arrow" />
      </div>
    </Transition>

    <!-- FAB Button -->
    <button
      class="aiaa-fab"
      :class="{ 'aiaa-fab--open': isOpen }"
      :aria-expanded="isOpen"
      aria-label="Buka AI Admin Assistant"
      @click="togglePanel"
    >
      <!-- Pulse ring -->
      <span v-if="showPulse && !isOpen" class="aiaa-fab__pulse" />

      <!-- Icon: Robot (saat tutup) -->
      <Transition name="aiaa-icon" mode="out-in">
        <span v-if="!isOpen" key="robot" class="aiaa-fab__robot">🤖</span>

        <!-- Icon: X (saat buka) -->
        <svg
          v-else
          key="close"
          viewBox="0 0 24 24"
          fill="none"
          class="aiaa-fab__close-icon"
        >
          <path
            d="M6 18L18 6M6 6l12 12"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
          />
        </svg>
      </Transition>

      <!-- Badge "AI" -->
      <span v-if="!isOpen" class="aiaa-fab__badge">AI</span>
    </button>
  </div>
</template>

<style scoped>
/* ===== BACKDROP ===== */
.aiaa-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 9990;
  backdrop-filter: blur(2px);
}

/* ===== POPUP ===== */
.aiaa-popup {
  position: fixed;
  bottom: 90px;
  right: 24px;
  width: 380px;
  height: 600px;
  max-height: calc(100vh - 110px);
  z-index: 9995;
  border-radius: 20px;
  box-shadow:
    0 30px 70px rgba(0, 0, 0, 0.4),
    0 10px 30px rgba(99, 102, 241, 0.2),
    0 0 0 1px rgba(99, 102, 241, 0.15);
  overflow: hidden;
}

@media (max-width: 480px) {
  .aiaa-popup {
    width: calc(100vw - 24px);
    right: 12px;
    bottom: 80px;
    height: calc(100vh - 100px);
    max-height: none;
    border-radius: 16px;
  }
}

/* ===== FAB WRAPPER ===== */
.aiaa-fab-wrapper {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
}

/* ===== FAB BUTTON ===== */
.aiaa-fab {
  position: relative;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 50%, #0ea5e9 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow:
    0 6px 24px rgba(79, 70, 229, 0.5),
    0 2px 8px rgba(0, 0, 0, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.15);
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  outline: none;
}
.aiaa-fab:hover {
  transform: scale(1.1) translateY(-2px);
  box-shadow:
    0 12px 32px rgba(79, 70, 229, 0.6),
    0 4px 12px rgba(0, 0, 0, 0.25);
}
.aiaa-fab:active {
  transform: scale(0.96);
}
.aiaa-fab--open {
  background: linear-gradient(135deg, #1e293b, #334155);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
}

/* Robot icon */
.aiaa-fab__robot {
  font-size: 26px;
  line-height: 1;
  transition: all 0.2s ease;
}

/* Close icon */
.aiaa-fab__close-icon {
  width: 24px;
  height: 24px;
}

/* Badge "AI" */
.aiaa-fab__badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: linear-gradient(135deg, #f59e0b, #ef4444);
  color: #fff;
  font-size: 9px;
  font-weight: 800;
  padding: 2px 5px;
  border-radius: 6px;
  line-height: 1;
  letter-spacing: 0.05em;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
}

/* Pulse ring */
.aiaa-fab__pulse {
  position: absolute;
  inset: -8px;
  border-radius: 50%;
  background: rgba(99, 102, 241, 0.2);
  animation: aiaa-pulse 2s ease-in-out infinite;
  pointer-events: none;
}
@keyframes aiaa-pulse {
  0%, 100% { transform: scale(1); opacity: 0.7; }
  50% { transform: scale(1.2); opacity: 0.15; }
}

/* ===== TOOLTIP ===== */
.aiaa-tooltip {
  position: relative;
  background: linear-gradient(135deg, #1e1b4b, #312e81);
  color: #e2e8f0;
  padding: 9px 14px;
  border-radius: 12px;
  font-size: 12.5px;
  font-weight: 600;
  white-space: nowrap;
  box-shadow:
    0 8px 24px rgba(0, 0, 0, 0.35),
    0 0 0 1px rgba(99, 102, 241, 0.25);
  cursor: pointer;
  transition: transform 0.15s ease;
}
.aiaa-tooltip:hover {
  transform: translateX(-3px);
}
.aiaa-tooltip__arrow {
  position: absolute;
  bottom: -5px;
  right: 22px;
  width: 10px;
  height: 10px;
  background: #312e81;
  transform: rotate(45deg);
  border-radius: 1px;
}

/* ===== TRANSITIONS ===== */

/* Popup spring animation */
.aiaa-popup-enter-active {
  animation: aiaa-popup-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.aiaa-popup-leave-active {
  animation: aiaa-popup-out 0.2s ease-in;
}
@keyframes aiaa-popup-in {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
@keyframes aiaa-popup-out {
  from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateY(16px) scale(0.94);
  }
}

/* Backdrop fade */
.aiaa-backdrop-enter-active,
.aiaa-backdrop-leave-active {
  transition: opacity 0.2s ease;
}
.aiaa-backdrop-enter-from,
.aiaa-backdrop-leave-to {
  opacity: 0;
}

/* Icon swap */
.aiaa-icon-enter-active,
.aiaa-icon-leave-active {
  transition: all 0.15s ease;
}
.aiaa-icon-enter-from {
  opacity: 0;
  transform: rotate(-90deg) scale(0.5);
}
.aiaa-icon-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.5);
}

/* Tooltip */
.aiaa-tooltip-enter-active {
  animation: aiaa-tooltip-in 0.3s ease;
}
.aiaa-tooltip-leave-active {
  animation: aiaa-tooltip-out 0.2s ease;
}
@keyframes aiaa-tooltip-in {
  from { opacity: 0; transform: translateX(12px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes aiaa-tooltip-out {
  from { opacity: 1; transform: translateX(0); }
  to { opacity: 0; transform: translateX(12px); }
}
</style>
