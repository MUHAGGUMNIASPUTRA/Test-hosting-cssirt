<script setup>
// File: resources/js/Components/AIAdminAssistant/AIAdminAssistantMessage.vue
// Bubble pesan individual untuk AI Admin Assistant.

import { computed, ref } from 'vue'

const props = defineProps({
  message: {
    type: Object,
    required: true,
    // { id, role, content, action?, metadata?, isError?, timestamp }
  },
})

const copied = ref(false)

const isUser = computed(() => props.message.role === 'user')
const isAssistant = computed(() => props.message.role === 'assistant')

const formattedTime = computed(() => {
  const d = props.message.timestamp
    ? new Date(props.message.timestamp)
    : new Date()
  return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
})

/**
 * Render markdown sederhana ke HTML:
 * - ## Heading 2
 * - ### Heading 3
 * - **bold**
 * - `code`
 * - - bullet / * bullet
 * - Baris kosong → paragraf
 */
const renderedContent = computed(() => {
  let text = props.message.content || ''

  // Escape HTML dasar
  text = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')

  // Code blocks ```...```
  text = text.replace(/```[\w]*\n?([\s\S]*?)```/g, '<pre class="aiam-code-block"><code>$1</code></pre>')

  // Inline code `...`
  text = text.replace(/`([^`]+)`/g, '<code class="aiam-inline-code">$1</code>')

  // Heading ## dan ###
  text = text.replace(/^### (.+)$/gm, '<h4 class="aiam-h4">$1</h4>')
  text = text.replace(/^## (.+)$/gm, '<h3 class="aiam-h3">$1</h3>')

  // Bold **text**
  text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')

  // Horizontal rule ---
  text = text.replace(/^---+$/gm, '<hr class="aiam-hr" />')

  // Bullet list - atau *
  text = text.replace(/^[*-] (.+)$/gm, '<li class="aiam-li">$1</li>')
  text = text.replace(/(<li class="aiam-li">.*<\/li>\n?)+/g, (match) => `<ul class="aiam-ul">${match}</ul>`)

  // Baris baru
  text = text.replace(/\n\n/g, '</p><p class="aiam-p">')
  text = text.replace(/\n/g, '<br />')

  // Wrap dalam paragraf
  if (!text.startsWith('<h') && !text.startsWith('<ul') && !text.startsWith('<pre')) {
    text = `<p class="aiam-p">${text}</p>`
  }

  return text
})

async function copyToClipboard() {
  try {
    await navigator.clipboard.writeText(props.message.content || '')
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (e) {
    // fallback
  }
}
</script>

<template>
  <div
    class="aiam-message"
    :class="{
      'aiam-message--user': isUser,
      'aiam-message--assistant': isAssistant,
      'aiam-message--error': message.isError,
    }"
  >
    <!-- Avatar assistant -->
    <div v-if="isAssistant" class="aiam-avatar">
      <span class="aiam-avatar__icon">🤖</span>
    </div>

    <div class="aiam-bubble-group" :class="{ 'aiam-bubble-group--user': isUser }">
      <!-- Bubble -->
      <div
        class="aiam-bubble"
        :class="{
          'aiam-bubble--user': isUser,
          'aiam-bubble--assistant': isAssistant,
          'aiam-bubble--error': message.isError,
        }"
      >
        <!-- User: plain text -->
        <p v-if="isUser" class="aiam-user-text">{{ message.content }}</p>

        <!-- Assistant: rendered markdown -->
        <!-- eslint-disable-next-line vue/no-v-html -->
        <div v-else class="aiam-assistant-content" v-html="renderedContent" />
      </div>

      <!-- Footer: waktu + copy button -->
      <div class="aiam-footer" :class="{ 'aiam-footer--user': isUser }">
        <span class="aiam-time">{{ formattedTime }}</span>

        <button
          v-if="isAssistant"
          class="aiam-copy-btn"
          :class="{ 'aiam-copy-btn--copied': copied }"
          :title="copied ? 'Tersalin!' : 'Salin jawaban'"
          @click="copyToClipboard"
        >
          <svg v-if="!copied" viewBox="0 0 20 20" fill="currentColor" class="aiam-copy-icon">
            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
          </svg>
          <svg v-else viewBox="0 0 20 20" fill="currentColor" class="aiam-copy-icon">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          <span class="aiam-copy-label">{{ copied ? 'Tersalin' : 'Salin' }}</span>
        </button>
      </div>
    </div>

    <!-- Avatar user -->
    <div v-if="isUser" class="aiam-avatar aiam-avatar--user">
      <span class="aiam-avatar__initial">A</span>
    </div>
  </div>
</template>

<style scoped>
/* ===== MESSAGE WRAPPER ===== */
.aiam-message {
  display: flex;
  gap: 10px;
  margin-bottom: 18px;
  align-items: flex-start;
}
.aiam-message--user {
  flex-direction: row-reverse;
}

/* ===== AVATAR ===== */
.aiam-avatar {
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
  line-height: 1;
}
.aiam-avatar--user {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  border-color: transparent;
}
.aiam-avatar__icon {
  font-size: 18px;
}
.aiam-avatar__initial {
  font-size: 13px;
  font-weight: 700;
  color: #fff;
}

/* ===== BUBBLE GROUP ===== */
.aiam-bubble-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
  max-width: 82%;
}
.aiam-bubble-group--user {
  align-items: flex-end;
}

/* ===== BUBBLE ===== */
.aiam-bubble {
  padding: 11px 14px;
  border-radius: 16px;
  line-height: 1.55;
  word-break: break-word;
}
.aiam-bubble--user {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #fff;
  border-bottom-right-radius: 4px;
  box-shadow: 0 3px 12px rgba(99, 102, 241, 0.3);
}
.aiam-bubble--assistant {
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(99, 102, 241, 0.18);
  color: #e2e8f0;
  border-bottom-left-radius: 4px;
  backdrop-filter: blur(8px);
}
.aiam-bubble--error {
  background: rgba(127, 29, 29, 0.5);
  border-color: rgba(239, 68, 68, 0.3);
  color: #fca5a5;
}

/* ===== USER TEXT ===== */
.aiam-user-text {
  margin: 0;
  font-size: 13px;
}

/* ===== ASSISTANT CONTENT (markdown) ===== */
.aiam-assistant-content {
  font-size: 13px;
  color: #e2e8f0;
}

/* Paragraf */
:deep(.aiam-p) {
  margin: 0 0 8px 0;
  line-height: 1.6;
}
:deep(.aiam-p:last-child) {
  margin-bottom: 0;
}

/* Heading */
:deep(.aiam-h3) {
  font-size: 14px;
  font-weight: 700;
  color: #a5b4fc;
  margin: 12px 0 6px 0;
  padding-bottom: 4px;
  border-bottom: 1px solid rgba(165, 180, 252, 0.2);
}
:deep(.aiam-h4) {
  font-size: 13px;
  font-weight: 600;
  color: #c7d2fe;
  margin: 10px 0 4px 0;
}

/* List */
:deep(.aiam-ul) {
  margin: 6px 0;
  padding-left: 18px;
}
:deep(.aiam-li) {
  margin-bottom: 4px;
  color: #cbd5e1;
}

/* Code */
:deep(.aiam-inline-code) {
  background: rgba(99, 102, 241, 0.2);
  color: #a5b4fc;
  padding: 1px 5px;
  border-radius: 4px;
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: 12px;
}
:deep(.aiam-code-block) {
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(99, 102, 241, 0.2);
  border-radius: 8px;
  padding: 10px 12px;
  overflow-x: auto;
  margin: 8px 0;
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: 11.5px;
  color: #93c5fd;
  white-space: pre;
}

/* HR */
:deep(.aiam-hr) {
  border: none;
  border-top: 1px solid rgba(99, 102, 241, 0.2);
  margin: 10px 0;
}

/* Strong */
:deep(strong) {
  color: #c7d2fe;
  font-weight: 600;
}

/* ===== FOOTER ===== */
.aiam-footer {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 2px;
}
.aiam-footer--user {
  flex-direction: row-reverse;
}
.aiam-time {
  font-size: 10px;
  color: rgba(148, 163, 184, 0.7);
}

/* ===== COPY BUTTON ===== */
.aiam-copy-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(148, 163, 184, 0.6);
  padding: 2px 4px;
  border-radius: 4px;
  transition: all 0.15s ease;
  font-size: 10.5px;
}
.aiam-copy-btn:hover {
  color: #a5b4fc;
  background: rgba(99, 102, 241, 0.1);
}
.aiam-copy-btn--copied {
  color: #6ee7b7;
}
.aiam-copy-icon {
  width: 12px;
  height: 12px;
}
.aiam-copy-label {
  font-size: 10px;
}
</style>
