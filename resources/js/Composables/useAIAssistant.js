// File: resources/js/Composables/useAIAssistant.js

import { nextTick, ref } from 'vue'

/**
 * useAIAssistant
 *
 * Composable untuk mengelola state dan logic AI Knowledge Assistant.
 * Menangani: pengiriman pesan, penerimaan jawaban, clear chat, error handling.
 */
export function useAIAssistant() {
  /**
   * Daftar pesan dalam sesi chat saat ini.
   * Setiap pesan: { id, role: 'user'|'assistant', content, references?, timestamp }
   */
  const messages = ref([])

  /** Status loading saat menunggu respons AI */
  const isLoading = ref(false)

  /** Pesan error jika terjadi masalah koneksi */
  const error = ref(null)

  /**
   * Kirim pertanyaan ke API AI Assistant.
   * @param {string} question - Pertanyaan dari user
   */
  async function sendMessage(question) {
    const trimmed = question.trim()
    if (!trimmed || isLoading.value) return

    error.value = null

    // Tambahkan pesan user ke chat
    messages.value.push({
      id: Date.now(),
      role: 'user',
      content: trimmed,
      timestamp: new Date(),
    })

    isLoading.value = true

    // Scroll ke bawah setelah pesan user muncul
    await nextTick()

    try {
      const response = await fetch('/api/ai-assistant', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          Accept: 'application/json',
        },
        body: JSON.stringify({ question: trimmed }),
      })

      if (!response.ok) {
        const errData = await response.json().catch(() => ({}))
        throw new Error(
          errData?.message || `Server error: ${response.status}`,
        )
      }

      const data = await response.json()

      // Tambahkan jawaban AI ke chat
      messages.value.push({
        id: Date.now() + 1,
        role: 'assistant',
        content: data.answer || 'Maaf, tidak ada jawaban yang diterima.',
        references: data.references || [],
        hasResult: data.has_result ?? false,
        timestamp: new Date(),
      })
    } catch (err) {
      console.error('AI Assistant error:', err)
      error.value = err.message

      // Tampilkan pesan error sebagai bubble assistant
      messages.value.push({
        id: Date.now() + 1,
        role: 'assistant',
        content:
          'Maaf, terjadi kesalahan saat menghubungi layanan AI. Silakan coba lagi atau hubungi tim CSIRT secara langsung.',
        references: [],
        hasResult: false,
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
