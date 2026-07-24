<script setup>
// File: resources/js/Components/AIAssistant/AIAssistantChat.vue
// Panel chat utama AI Knowledge Assistant.

import { nextTick, ref, watch } from 'vue'
import AIAssistantMessage from './AIAssistantMessage.vue'
import AIAssistantSuggestions from './AIAssistantSuggestions.vue'
import { useAIAssistant } from '@/Composables/useAIAssistant'

const emit = defineEmits(['close'])

const { messages, isLoading, sendMessage, clearChat } = useAIAssistant()

const inputValue = ref('')
const messagesContainer = ref(null)
const inputRef = ref(null)

/** Auto-scroll ke bawah saat ada pesan baru */
watch(
  messages,
  async () => {
    await nextTick()
    scrollToBottom()
  },
  { deep: true },
)

watch(isLoading, async () => {
  await nextTick()
  scrollToBottom()
})

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

async function handleSend() {
  const q = inputValue.value.trim()
  if (!q || isLoading.value) return

  inputValue.value = ''
  await sendMessage(q)

  // Fokus kembali ke input setelah selesai
  await nextTick()
  inputRef.value?.focus()
}

function handleKeydown(e) {
  // Enter untuk kirim, Shift+Enter untuk newline
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    handleSend()
  }
}

async function handleSuggestion(question) {
  inputValue.value = question
  await handleSend()
}

function handleClear() {
  clearChat()
  inputRef.value?.focus()
}

/** Auto-resize textarea saat mengetik */
function autoResize(e) {
  const el = e.target
  el.style.height = 'auto'
  el.style.height = Math.min(el.scrollHeight, 100) + 'px'
}
</script>

<template>
  <div class="aic-panel">
    <!-- ===== HEADER ===== -->
    <div class="aic-header">
      <div class="aic-header__left">
        <!-- Logo/Icon -->
        <div class="aic-header__logo">
          <svg viewBox="0 0 24 24" fill="none" class="aic-header__logo-icon">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"
              fill="url(#headerGrad)"
            />
            <path
              d="M12 6v6l4 2"
              stroke="#fff"
              stroke-width="1.5"
              stroke-linecap="round"
            />
            <circle cx="12" cy="12" r="2" fill="#fff" opacity="0.8" />
            <defs>
              <linearGradient id="headerGrad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                <stop stop-color="#4f46e5" />
                <stop offset="1" stop-color="#0ea5e9" />
              </linearGradient>
            </defs>
          </svg>
          <!-- Status dot -->
          <span class="aic-header__status" />
        </div>

        <div class="aic-header__info">
          <h3 class="aic-header__title">AI Knowledge Assistant</h3>
          <p class="aic-header__subtitle">
            Informasi resmi CSIRT Bojonegoro
          </p>
        </div>
      </div>

      <div class="aic-header__actions">
        <!-- Clear button -->
        <button
          v-if="messages.length > 0"
          class="aic-header__btn aic-header__btn--clear"
          title="Hapus percakapan"
          @click="handleClear"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aic-btn-icon">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Close button -->
        <button
          class="aic-header__btn aic-header__btn--close"
          title="Tutup"
          @click="emit('close')"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aic-btn-icon">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <!-- ===== MESSAGES AREA ===== -->
    <div ref="messagesContainer" class="aic-messages">
      <!-- Welcome state (chat kosong) -->
      <div v-if="messages.length === 0" class="aic-welcome">
        <div class="aic-welcome__icon">
          <svg viewBox="0 0 48 48" fill="none" class="aic-welcome__svg">
            <circle cx="24" cy="24" r="22" fill="url(#welcomeGrad)" opacity="0.15" />
            <path d="M16 18h16M16 24h12M16 30h8" stroke="url(#welcomeGrad)" stroke-width="2" stroke-linecap="round" />
            <circle cx="34" cy="34" r="6" fill="url(#welcomeGrad)" />
            <path d="M32 34l1.5 1.5L36 32" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <defs>
              <linearGradient id="welcomeGrad" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                <stop stop-color="#4f46e5" />
                <stop offset="1" stop-color="#0ea5e9" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <h4 class="aic-welcome__title">Halo! Saya siap membantu 👋</h4>
        <p class="aic-welcome__desc">
          Tanyakan apa saja seputar CSIRT Bojonegoro. Saya akan menjawab berdasarkan informasi resmi website.
        </p>

        <!-- Suggested Questions -->
        <AIAssistantSuggestions @select="handleSuggestion" />
      </div>

      <!-- Daftar pesan -->
      <template v-else>
        <AIAssistantMessage
          v-for="msg in messages"
          :key="msg.id"
          :message="msg"
        />
      </template>

      <!-- Typing indicator -->
      <div v-if="isLoading" class="aic-typing">
        <div class="aic-avatar aic-avatar--assistant">
          <svg viewBox="0 0 24 24" fill="currentColor" class="aic-avatar-icon">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
        <div class="aic-typing__bubble">
          <span class="aic-typing__dot" />
          <span class="aic-typing__dot" />
          <span class="aic-typing__dot" />
        </div>
      </div>
    </div>

    <!-- ===== INPUT AREA ===== -->
    <div class="aic-input-area">
      <div class="aic-input-wrapper">
        <textarea
          ref="inputRef"
          v-model="inputValue"
          rows="1"
          class="aic-input"
          placeholder="Ketik pertanyaan Anda..."
          :disabled="isLoading"
          @keydown="handleKeydown"
          @input="autoResize"
        />
        <button
          class="aic-send-btn"
          :class="{ 'aic-send-btn--active': inputValue.trim() && !isLoading }"
          :disabled="!inputValue.trim() || isLoading"
          title="Kirim (Enter)"
          @click="handleSend"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aic-send-icon">
            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
          </svg>
        </button>
      </div>
      <p class="aic-hint">
        <kbd>Enter</kbd> kirim &nbsp;·&nbsp; <kbd>Shift+Enter</kbd> baris baru
      </p>
    </div>
  </div>
</template>

<style scoped>
/* ===== PANEL ===== */
.aic-panel {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
}

/* ===== HEADER ===== */
.aic-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1e3a5f 100%);
  flex-shrink: 0;
}
.aic-header__left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.aic-header__logo {
  position: relative;
  flex-shrink: 0;
}
.aic-header__logo-icon {
  width: 36px;
  height: 36px;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}
.aic-header__status {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 9px;
  height: 9px;
  background: #22c55e;
  border: 2px solid #1e1b4b;
  border-radius: 50%;
}
.aic-header__info {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.aic-header__title {
  font-size: 13.5px;
  font-weight: 700;
  color: #fff;
  margin: 0;
  line-height: 1.2;
}
.aic-header__subtitle {
  font-size: 10.5px;
  color: #a5b4fc;
  margin: 0;
}
.aic-header__actions {
  display: flex;
  align-items: center;
  gap: 4px;
}
.aic-header__btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
}
.aic-header__btn--clear {
  background: rgba(255, 255, 255, 0.1);
  color: #a5b4fc;
}
.aic-header__btn--clear:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
}
.aic-header__btn--close {
  background: rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
}
.aic-header__btn--close:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
}
.aic-btn-icon {
  width: 15px;
  height: 15px;
}

/* ===== MESSAGES ===== */
.aic-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 14px;
  scroll-behavior: smooth;
}
.aic-messages::-webkit-scrollbar {
  width: 4px;
}
.aic-messages::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

/* ===== WELCOME ===== */
.aic-welcome {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 8px 4px;
}
.aic-welcome__icon {
  margin-bottom: 12px;
}
.aic-welcome__svg {
  width: 56px;
  height: 56px;
}
.aic-welcome__title {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 6px 0;
}
.aic-welcome__desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.5;
  margin: 0 0 16px 0;
  max-width: 280px;
}

/* ===== TYPING INDICATOR ===== */
.aic-typing {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  margin-bottom: 16px;
}
.aic-avatar {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0ea5e9, #2563eb);
}
.aic-avatar-icon {
  width: 16px;
  height: 16px;
  color: #fff;
}
.aic-typing__bubble {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 12px 14px;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  border-bottom-left-radius: 4px;
}
.aic-typing__dot {
  width: 7px;
  height: 7px;
  background: #94a3b8;
  border-radius: 50%;
  animation: typing-bounce 1.2s ease-in-out infinite;
}
.aic-typing__dot:nth-child(2) {
  animation-delay: 0.2s;
}
.aic-typing__dot:nth-child(3) {
  animation-delay: 0.4s;
}
@keyframes typing-bounce {
  0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
  30% { transform: translateY(-6px); opacity: 1; }
}

/* ===== INPUT ===== */
.aic-input-area {
  flex-shrink: 0;
  padding: 10px 14px 12px;
  border-top: 1px solid #e2e8f0;
  background: #fff;
}
.aic-input-wrapper {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 8px 8px 8px 12px;
  transition: border-color 0.15s ease;
}
.aic-input-wrapper:focus-within {
  border-color: #6366f1;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
}
.aic-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 13px;
  color: #1e293b;
  line-height: 1.5;
  resize: none;
  outline: none;
  max-height: 100px;
  overflow-y: auto;
  font-family: inherit;
}
.aic-input::placeholder {
  color: #94a3b8;
}
.aic-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.aic-send-btn {
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 10px;
  background: #e2e8f0;
  color: #94a3b8;
  cursor: not-allowed;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}
.aic-send-btn--active {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #fff;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
}
.aic-send-btn--active:hover {
  background: linear-gradient(135deg, #4338ca, #4f46e5);
  transform: scale(1.05);
}
.aic-send-btn--active:active {
  transform: scale(0.97);
}
.aic-send-icon {
  width: 16px;
  height: 16px;
}
.aic-hint {
  font-size: 10px;
  color: #94a3b8;
  text-align: center;
  margin: 6px 0 0 0;
}
.aic-hint kbd {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 3px;
  padding: 0 4px;
  font-size: 9.5px;
  font-family: inherit;
}
</style>

