<script setup>
// File: resources/js/Components/AIAssistant/AIAssistantMessage.vue
// Komponen satu bubble pesan dalam chat AI Assistant.

import { computed } from 'vue'

const props = defineProps({
  /** Objek pesan: { role, content, references, hasResult, isError, timestamp } */
  message: {
    type: Object,
    required: true,
  },
})

const isUser = computed(() => props.message.role === 'user')
const isAssistant = computed(() => props.message.role === 'assistant')

/** Format timestamp menjadi HH:MM */
const formattedTime = computed(() => {
  if (!props.message.timestamp) return ''
  const d = new Date(props.message.timestamp)
  return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
})

/** Ikon per tipe referensi */
const referenceIcon = (type) => {
  const icons = {
    faq: '❓',
    article: '📄',
    document: '📋',
    rfc2350: '📜',
    service: '🛡️',
    contact: '📞',
  }
  return icons[type] || '🔗'
}
</script>

<template>
  <!-- Bubble User -->
  <div v-if="isUser" class="ai-msg-row ai-msg-row--user">
    <div class="ai-bubble ai-bubble--user">
      <p class="ai-bubble__text">{{ message.content }}</p>
      <span class="ai-bubble__time">{{ formattedTime }}</span>
    </div>
    <!-- Avatar User -->
    <div class="ai-avatar ai-avatar--user">
      <svg viewBox="0 0 24 24" fill="currentColor" class="ai-avatar__icon">
        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
      </svg>
    </div>
  </div>

  <!-- Bubble Assistant -->
  <div v-else-if="isAssistant" class="ai-msg-row ai-msg-row--assistant">
    <!-- Avatar AI -->
    <div class="ai-avatar ai-avatar--assistant" :class="{ 'ai-avatar--error': message.isError }">
      <svg viewBox="0 0 24 24" fill="currentColor" class="ai-avatar__icon">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
      </svg>
    </div>
    <div class="ai-bubble-wrapper">
      <!-- Bubble Konten -->
      <div class="ai-bubble ai-bubble--assistant" :class="{ 'ai-bubble--error': message.isError }">
        <!-- Render konten dengan newline support -->
        <div class="ai-bubble__content">
          <p
            v-for="(paragraph, idx) in message.content.split('\n')"
            :key="idx"
            class="ai-bubble__paragraph"
            :class="{ 'ai-bubble__paragraph--empty': paragraph.trim() === '' }"
          >
            <!-- Render bold text (**text**) -->
            <template v-if="paragraph.trim().startsWith('**') && paragraph.trim().endsWith('**')">
              <strong>{{ paragraph.trim().slice(2, -2) }}</strong>
            </template>
            <!-- Bullet points -->
            <template v-else-if="paragraph.trim().startsWith('- ') || paragraph.trim().startsWith('• ')">
              <span class="ai-bullet">{{ paragraph.trim() }}</span>
            </template>
            <template v-else>{{ paragraph }}</template>
          </p>
        </div>
        <span class="ai-bubble__time">{{ formattedTime }}</span>
      </div>

      <!-- Referensi Sumber -->
      <div v-if="message.references && message.references.length > 0" class="ai-references">
        <p class="ai-references__label">
          <svg viewBox="0 0 20 20" fill="currentColor" class="ai-references__icon">
            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
          </svg>
          Sumber:
        </p>
        <div class="ai-references__list">
          <a
            v-for="ref in message.references"
            :key="ref.url"
            :href="ref.url"
            target="_blank"
            rel="noopener noreferrer"
            class="ai-reference-chip"
          >
            <span class="ai-reference-chip__icon">{{ referenceIcon(ref.type) }}</span>
            <span class="ai-reference-chip__label">{{ ref.label }}</span>
            <svg viewBox="0 0 20 20" fill="currentColor" class="ai-reference-chip__arrow">
              <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ===== ROW ===== */
.ai-msg-row {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  margin-bottom: 16px;
}
.ai-msg-row--user {
  flex-direction: row-reverse;
}
.ai-msg-row--assistant {
  flex-direction: row;
}

/* ===== AVATAR ===== */
.ai-avatar {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.ai-avatar--user {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
}
.ai-avatar--assistant {
  background: linear-gradient(135deg, #0ea5e9, #2563eb);
}
.ai-avatar--error {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}
.ai-avatar__icon {
  width: 16px;
  height: 16px;
  color: #fff;
}

/* ===== BUBBLE ===== */
.ai-bubble-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  max-width: calc(100% - 44px);
}
.ai-bubble {
  position: relative;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 13.5px;
  line-height: 1.55;
  word-break: break-word;
}
.ai-bubble--user {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #fff;
  border-bottom-right-radius: 4px;
  max-width: 280px;
  align-self: flex-end;
}
.ai-bubble--assistant {
  background: #f1f5f9;
  color: #1e293b;
  border-bottom-left-radius: 4px;
  border: 1px solid #e2e8f0;
}
.ai-bubble--error {
  background: #fef2f2;
  border-color: #fecaca;
  color: #7f1d1d;
}

/* ===== BUBBLE CONTENT ===== */
.ai-bubble__content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.ai-bubble__paragraph {
  margin: 0;
}
.ai-bubble__paragraph--empty {
  height: 6px;
}
.ai-bullet {
  display: block;
  padding-left: 4px;
}
.ai-bubble__time {
  display: block;
  font-size: 10px;
  margin-top: 4px;
  opacity: 0.6;
  text-align: right;
}

/* ===== REFERENCES ===== */
.ai-references {
  padding: 8px 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
}
.ai-references__label {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  margin: 0 0 6px 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.ai-references__icon {
  width: 12px;
  height: 12px;
}
.ai-references__list {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}
.ai-reference-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: #fff;
  border: 1px solid #c7d2fe;
  border-radius: 20px;
  font-size: 11.5px;
  color: #4f46e5;
  text-decoration: none;
  transition: all 0.15s ease;
  cursor: pointer;
}
.ai-reference-chip:hover {
  background: #eef2ff;
  border-color: #6366f1;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.15);
}
.ai-reference-chip__icon {
  font-size: 12px;
}
.ai-reference-chip__label {
  max-width: 160px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.ai-reference-chip__arrow {
  width: 10px;
  height: 10px;
  flex-shrink: 0;
  opacity: 0.6;
}
</style>
