<script setup>
// File: resources/js/Components/AIAssistant/AIAssistantSuggestions.vue
// Komponen suggested questions yang tampil saat chat masih kosong.

const emit = defineEmits(['select'])

const suggestions = [
  { icon: '🚨', text: 'Bagaimana cara melaporkan insiden?' },
  { icon: '🎣', text: 'Apa itu phishing?' },
  { icon: '🔐', text: 'Apa itu ransomware?' },
  { icon: '🕐', text: 'Jam operasional CSIRT?' },
  { icon: '📞', text: 'Kontak CSIRT Bojonegoro?' },
  { icon: '🛡️', text: 'Layanan CSIRT apa saja Ya?' },
]

function select(question) {
  emit('select', question)
}
</script>

<template>
  <div class="ai-suggestions">
    <p class="ai-suggestions__title">
      <svg viewBox="0 0 20 20" fill="currentColor" class="ai-suggestions__star">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
      Pertanyaan yang sering diajukan
    </p>
    <div class="ai-suggestions__grid">
      <button
        v-for="s in suggestions"
        :key="s.text"
        class="ai-suggestion-btn"
        @click="select(s.text)"
      >
        <span class="ai-suggestion-btn__icon">{{ s.icon }}</span>
        <span class="ai-suggestion-btn__text">{{ s.text }}</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.ai-suggestions {
  padding: 12px 4px 0;
}
.ai-suggestions__title {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0 0 10px 0;
}
.ai-suggestions__star {
  width: 12px;
  height: 12px;
  color: #f59e0b;
}
.ai-suggestions__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}

.ai-suggestion-btn {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  padding: 8px 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  text-align: left;
  transition: all 0.15s ease;
  font-size: 12px;
  color: #334155;
  line-height: 1.35;
}
.ai-suggestion-btn:hover {
  background: #eef2ff;
  border-color: #c7d2fe;
  color: #4f46e5;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(99, 102, 241, 0.12);
}
.ai-suggestion-btn:active {
  transform: translateY(0);
}
.ai-suggestion-btn__icon {
  font-size: 14px;
  line-height: 1;
  flex-shrink: 0;
  margin-top: 1px;
}
.ai-suggestion-btn__text {
  flex: 1;
}
</style>
