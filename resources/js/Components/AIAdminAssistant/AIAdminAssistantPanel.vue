<script setup>
// File: resources/js/Components/AIAdminAssistant/AIAdminAssistantPanel.vue
// Panel chat utama AI Admin Assistant.

import { nextTick, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AIAdminAssistantMessage from './AIAdminAssistantMessage.vue'
import { useAIAdminAssistant } from '@/Composables/useAIAdminAssistant'

const emit = defineEmits(['close'])

const page = usePage()
const userName = page.props?.auth?.user?.name || 'Administrator'
const userInitial = userName.charAt(0).toUpperCase()

const { messages, isLoading, sendMessage, clearChat } = useAIAdminAssistant()

const inputValue = ref('')
const messagesContainer = ref(null)
const inputRef = ref(null)

/** Definisi Quick Actions */
const quickActions = [
  {
    id: 'draft_article',
    label: '📝 Buat Artikel',
    action: 'draft_article',
    prefill: 'Buat draft artikel tentang ',
    directSend: false,
  },
  {
    id: 'generate_faq',
    label: '❓ Generate FAQ',
    action: 'generate_faq',
    prefill: 'Buat FAQ dari artikel tentang ',
    directSend: false,
  },
  {
    id: 'summarize',
    label: '📄 Ringkas Dokumen',
    action: 'summarize',
    prefill: 'Ringkas dokumen berikut:\n\n',
    directSend: false,
  },
  {
    id: 'dashboard_insight',
    label: '📊 Insight Dashboard',
    action: 'dashboard_insight',
    prompt: 'Berikan insight dashboard CSIRT saat ini',
    directSend: true,
  },
  {
    id: 'search_article',
    label: '🔍 Cari Artikel',
    action: 'search_article',
    prefill: 'Cari artikel tentang ',
    directSend: false,
  },
  {
    id: 'statistics',
    label: '📈 Statistik Hari Ini',
    action: 'statistics',
    prompt: 'Tampilkan statistik website hari ini',
    directSend: true,
  },
]

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

/**
 * Deteksi action dari teks input.
 * Periksa apakah prompt cocok dengan kata kunci aksi tertentu.
 */
function detectAction(text) {
  const t = text.toLowerCase()
  if (t.startsWith('buat draft artikel') || t.startsWith('buat artikel')) return 'draft_article'
  if (t.startsWith('buat faq') || t.startsWith('generate faq')) return 'generate_faq'
  if (t.startsWith('ringkas dokumen') || t.startsWith('ringkaskan')) return 'summarize'
  if (t.startsWith('insight dashboard') || t.startsWith('berikan insight')) return 'dashboard_insight'
  if (t.startsWith('statistik') || t.startsWith('tampilkan statistik')) return 'statistics'
  if (t.startsWith('cari artikel')) return 'search_article'
  if (t.startsWith('cari faq')) return 'search_faq'
  if (t.startsWith('cari dokumen')) return 'search_document'
  return 'general'
}

async function handleSend() {
  const q = inputValue.value.trim()
  if (!q || isLoading.value) return

  const action = detectAction(q)

  inputValue.value = ''
  await sendMessage(action, q, q)

  await nextTick()
  inputRef.value?.focus()
}

function handleKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    handleSend()
  }
}

/** Handle klik Quick Action */
async function handleQuickAction(qa) {
  if (qa.directSend) {
    await sendMessage(qa.action, qa.prompt, qa.label)
  } else {
    inputValue.value = qa.prefill
    await nextTick()
    inputRef.value?.focus()
    // Pindahkan cursor ke akhir
    const el = inputRef.value
    if (el) {
      el.setSelectionRange(el.value.length, el.value.length)
    }
  }
}

function handleClear() {
  clearChat()
  nextTick(() => inputRef.value?.focus())
}

/** Auto-resize textarea */
function autoResize(e) {
  const el = e.target
  el.style.height = 'auto'
  el.style.height = Math.min(el.scrollHeight, 120) + 'px'
}
</script>

<template>
  <div class="aiap-panel">
    <!-- ===== HEADER ===== -->
    <div class="aiap-header">
      <div class="aiap-header__left">
        <div class="aiap-header__logo">
          <span class="aiap-header__robot">🤖</span>
          <span class="aiap-header__status-dot" />
        </div>
        <div class="aiap-header__info">
          <h3 class="aiap-header__title">AI Admin Assistant</h3>
          <p class="aiap-header__subtitle">
            Halo, {{ userName }} 👋
          </p>
        </div>
      </div>

      <div class="aiap-header__actions">
        <!-- Clear button -->
        <button
          v-if="messages.length > 0"
          class="aiap-header__btn aiap-header__btn--clear"
          title="Hapus percakapan"
          @click="handleClear"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aiap-btn-icon">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Close button -->
        <button
          class="aiap-header__btn aiap-header__btn--close"
          title="Tutup"
          @click="emit('close')"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aiap-btn-icon">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <!-- ===== MESSAGES AREA ===== -->
    <div ref="messagesContainer" class="aiap-messages">
      <!-- Welcome state (chat kosong) -->
      <div v-if="messages.length === 0" class="aiap-welcome">
        <!-- Capabilities -->
        <div class="aiap-capabilities">
          <p class="aiap-capabilities__title">Saya dapat membantu:</p>
          <div class="aiap-capabilities__list">
            <span>📝 Draft Artikel</span>
            <span>❓ Generate FAQ</span>
            <span>📄 Ringkas Dokumen</span>
            <span>📊 Insight Dashboard</span>
            <span>🔍 Cari Data</span>
            <span>📈 Statistik Website</span>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="aiap-quick-actions">
          <p class="aiap-quick-actions__title">💡 Contoh Perintah</p>
          <div class="aiap-quick-actions__grid">
            <button
              v-for="qa in quickActions"
              :key="qa.id"
              class="aiap-quick-btn"
              :disabled="isLoading"
              @click="handleQuickAction(qa)"
            >
              {{ qa.label }}
            </button>
          </div>
        </div>

        <!-- AI Copilot notice -->
        <div class="aiap-notice">
          <svg viewBox="0 0 20 20" fill="currentColor" class="aiap-notice__icon">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          <p>AI Copilot — hanya membantu Administrator. Semua keputusan tetap ada pada Anda.</p>
        </div>
      </div>

      <!-- Daftar pesan -->
      <template v-else>
        <AIAdminAssistantMessage
          v-for="msg in messages"
          :key="msg.id"
          :message="msg"
        />
      </template>

      <!-- Typing indicator -->
      <div v-if="isLoading" class="aiap-typing">
        <div class="aiap-typing__avatar">🤖</div>
        <div class="aiap-typing__bubble">
          <span class="aiap-typing__dot" />
          <span class="aiap-typing__dot" />
          <span class="aiap-typing__dot" />
        </div>
      </div>
    </div>

    <!-- ===== INPUT AREA ===== -->
    <div class="aiap-input-area">
      <div class="aiap-input-wrapper">
        <textarea
          ref="inputRef"
          v-model="inputValue"
          rows="1"
          class="aiap-input"
          placeholder="Tanyakan sesuatu..."
          :disabled="isLoading"
          @keydown="handleKeydown"
          @input="autoResize"
        />
        <button
          class="aiap-send-btn"
          :class="{ 'aiap-send-btn--active': inputValue.trim() && !isLoading }"
          :disabled="!inputValue.trim() || isLoading"
          title="Kirim (Enter)"
          @click="handleSend"
        >
          <svg viewBox="0 0 20 20" fill="currentColor" class="aiap-send-icon">
            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
          </svg>
        </button>
      </div>
      <p class="aiap-hint">
        <kbd>Enter</kbd> kirim &nbsp;·&nbsp; <kbd>Shift+Enter</kbd> baris baru
      </p>
    </div>
  </div>
</template>

<style scoped>
/* ===== PANEL ===== */
.aiap-panel {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 40%, #0f172a 100%);
  border-radius: 20px;
  overflow: hidden;
}

/* ===== HEADER ===== */
.aiap-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e3a5f 100%);
  border-bottom: 1px solid rgba(99, 102, 241, 0.2);
  flex-shrink: 0;
}
.aiap-header__left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.aiap-header__logo {
  position: relative;
  flex-shrink: 0;
  width: 38px;
  height: 38px;
  background: rgba(99, 102, 241, 0.15);
  border: 1.5px solid rgba(99, 102, 241, 0.3);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.aiap-header__robot {
  font-size: 20px;
  line-height: 1;
}
.aiap-header__status-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 9px;
  height: 9px;
  background: #22c55e;
  border: 2px solid #0f172a;
  border-radius: 50%;
}
.aiap-header__info {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.aiap-header__title {
  font-size: 13.5px;
  font-weight: 700;
  color: #f1f5f9;
  margin: 0;
  line-height: 1.2;
}
.aiap-header__subtitle {
  font-size: 10.5px;
  color: #94a3b8;
  margin: 0;
}
.aiap-header__actions {
  display: flex;
  align-items: center;
  gap: 4px;
}
.aiap-header__btn {
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
.aiap-header__btn--clear {
  background: rgba(255, 255, 255, 0.06);
  color: #64748b;
}
.aiap-header__btn--clear:hover {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
}
.aiap-header__btn--close {
  background: rgba(255, 255, 255, 0.06);
  color: #64748b;
}
.aiap-header__btn--close:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
}
.aiap-btn-icon {
  width: 15px;
  height: 15px;
}

/* ===== MESSAGES ===== */
.aiap-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 14px;
  scroll-behavior: smooth;
}
.aiap-messages::-webkit-scrollbar {
  width: 4px;
}
.aiap-messages::-webkit-scrollbar-thumb {
  background: rgba(99, 102, 241, 0.3);
  border-radius: 4px;
}

/* ===== WELCOME STATE ===== */
.aiap-welcome {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 4px 0;
}

/* Capabilities */
.aiap-capabilities {
  background: rgba(99, 102, 241, 0.06);
  border: 1px solid rgba(99, 102, 241, 0.15);
  border-radius: 14px;
  padding: 14px;
}
.aiap-capabilities__title {
  font-size: 11.5px;
  font-weight: 600;
  color: #94a3b8;
  margin: 0 0 10px 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.aiap-capabilities__list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.aiap-capabilities__list span {
  display: inline-block;
  background: rgba(99, 102, 241, 0.12);
  border: 1px solid rgba(99, 102, 241, 0.2);
  color: #c7d2fe;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 500;
}

/* Quick Actions */
.aiap-quick-actions {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(99, 102, 241, 0.15);
  border-radius: 14px;
  padding: 14px;
}
.aiap-quick-actions__title {
  font-size: 11.5px;
  font-weight: 600;
  color: #94a3b8;
  margin: 0 0 10px 0;
}
.aiap-quick-actions__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 7px;
}
.aiap-quick-btn {
  padding: 8px 10px;
  background: rgba(99, 102, 241, 0.08);
  border: 1px solid rgba(99, 102, 241, 0.2);
  border-radius: 10px;
  color: #a5b4fc;
  font-size: 11.5px;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
  transition: all 0.18s ease;
  line-height: 1.3;
}
.aiap-quick-btn:hover:not(:disabled) {
  background: rgba(99, 102, 241, 0.18);
  border-color: rgba(99, 102, 241, 0.4);
  color: #c7d2fe;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}
.aiap-quick-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* AI Notice */
.aiap-notice {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: rgba(30, 58, 138, 0.2);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 10px;
  padding: 10px 12px;
}
.aiap-notice__icon {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  color: #60a5fa;
  margin-top: 1px;
}
.aiap-notice p {
  margin: 0;
  font-size: 11px;
  color: #93c5fd;
  line-height: 1.5;
}

/* ===== TYPING INDICATOR ===== */
.aiap-typing {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  margin-bottom: 16px;
}
.aiap-typing__avatar {
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(99, 102, 241, 0.15);
  border: 1.5px solid rgba(99, 102, 241, 0.25);
  font-size: 18px;
}
.aiap-typing__bubble {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 12px 14px;
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(99, 102, 241, 0.18);
  border-radius: 16px;
  border-bottom-left-radius: 4px;
  backdrop-filter: blur(8px);
}
.aiap-typing__dot {
  width: 7px;
  height: 7px;
  background: #6366f1;
  border-radius: 50%;
  animation: aiap-bounce 1.2s ease-in-out infinite;
}
.aiap-typing__dot:nth-child(2) { animation-delay: 0.2s; }
.aiap-typing__dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes aiap-bounce {
  0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
  30% { transform: translateY(-6px); opacity: 1; }
}

/* ===== INPUT AREA ===== */
.aiap-input-area {
  flex-shrink: 0;
  padding: 10px 14px 12px;
  border-top: 1px solid rgba(99, 102, 241, 0.15);
  background: rgba(15, 23, 42, 0.8);
}
.aiap-input-wrapper {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  background: rgba(30, 27, 75, 0.6);
  border: 1.5px solid rgba(99, 102, 241, 0.2);
  border-radius: 14px;
  padding: 8px 8px 8px 12px;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.aiap-input-wrapper:focus-within {
  border-color: rgba(99, 102, 241, 0.5);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
}
.aiap-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 13px;
  color: #e2e8f0;
  line-height: 1.5;
  resize: none;
  outline: none;
  max-height: 120px;
  overflow-y: auto;
  font-family: inherit;
}
.aiap-input::placeholder {
  color: rgba(148, 163, 184, 0.5);
}
.aiap-input:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.aiap-send-btn {
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 10px;
  background: rgba(30, 27, 75, 0.6);
  color: #475569;
  cursor: not-allowed;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}
.aiap-send-btn--active {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #fff;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(99, 102, 241, 0.4);
}
.aiap-send-btn--active:hover {
  background: linear-gradient(135deg, #4338ca, #4f46e5);
  transform: scale(1.06);
}
.aiap-send-btn--active:active {
  transform: scale(0.97);
}
.aiap-send-icon {
  width: 16px;
  height: 16px;
}
.aiap-hint {
  font-size: 10px;
  color: rgba(148, 163, 184, 0.4);
  text-align: center;
  margin: 6px 0 0 0;
}
.aiap-hint kbd {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 3px;
  padding: 0 4px;
  font-size: 9.5px;
  font-family: inherit;
  color: rgba(148, 163, 184, 0.6);
}
</style>
