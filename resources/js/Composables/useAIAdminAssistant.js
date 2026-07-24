// File: resources/js/Composables/useAIAdminAssistant.js
// Composable untuk AI Admin Assistant — state management dan API calls.

import { nextTick, ref } from 'vue'

/**
 * useAIAdminAssistant
 *
 * Composable Vue 3 untuk mengelola state dan logic AI Admin Assistant.
 * Berkomunikasi dengan endpoint /api/admin/ai-assistant (auth-protected).
 *
 * @returns {object} messages, isLoading, error, sendMessage, clearChat
 */
export function useAIAdminAssistant() {
  /**
   * Riwayat pesan dalam sesi chat saat ini.
   * Setiap pesan: { id, role: 'user'|'assistant', content, action?, metadata?, timestamp }
   */
  const messages = ref([])

  /** Status loading saat menunggu respons AI */
  const isLoading = ref(false)

  /** Pesan error koneksi */
  const error = ref(null)

  /**
   * Kirim pesan ke AI Admin Assistant API.
   *
   * @param {string} action  - Jenis aksi: 'draft_article', 'generate_faq', dll.
   * @param {string} prompt  - Prompt/input dari Administrator
   * @param {string} displayText - Teks yang ditampilkan di bubble user (opsional)
   */
  async function sendMessage(action, prompt, displayText = null) {
    const trimmedPrompt = prompt.trim()
    if (!trimmedPrompt || isLoading.value) return

    error.value = null

    // Tampilkan pesan user di chat
    messages.value.push({
      id: Date.now(),
      role: 'user',
      content: displayText || trimmedPrompt,
      timestamp: new Date(),
    })

    isLoading.value = true
    await nextTick()

    try {
      const response = await fetch('/api/admin/ai-assistant', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          Accept: 'application/json',
        },
        body: JSON.stringify({ action, prompt: trimmedPrompt }),
      })

      if (response.status === 401 || response.status === 403) {
        throw new Error('Sesi login Anda telah habis. Silakan login kembali.')
      }

      if (!response.ok) {
        const errData = await response.json().catch(() => ({}))
        throw new Error(errData?.message || `Server error: ${response.status}`)
      }

      const data = await response.json()

      // Tambahkan jawaban AI ke chat
      messages.value.push({
        id: Date.now() + 1,
        role: 'assistant',
        content: data.answer || 'Maaf, tidak ada jawaban yang diterima.',
        action: data.action || action,
        metadata: data.metadata || {},
        timestamp: new Date(),
      })
    } catch (err) {
      console.error('AI Admin Assistant error:', err)
      error.value = err.message

      messages.value.push({
        id: Date.now() + 1,
        role: 'assistant',
        content: err.message.includes('login')
          ? `⚠️ ${err.message}`
          : 'Maaf, terjadi kesalahan saat menghubungi layanan AI. Silakan coba lagi atau periksa koneksi internet Anda.',
        action,
        metadata: {},
        isError: true,
        timestamp: new Date(),
      })
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Hapus seluruh riwayat chat.
   */
  function clearChat() {
    messages.value = []
    error.value = null
    isLoading.value = false
  }

  /**
   * Dapatkan CSRF token dari meta tag Laravel.
   */
  function getCsrfToken() {
    return (
      document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    )
  }

  return {
    messages,
    isLoading,
    error,
    sendMessage,
    clearChat,
  }
}
