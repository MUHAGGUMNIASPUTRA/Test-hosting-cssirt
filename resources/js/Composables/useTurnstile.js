// Tujuan: Load Cloudflare Turnstile script idempotently.
// Caller: TurnstileWidget component.
// Side Effects: Appends script tag to DOM, sets window.turnstile global.

export function useTurnstile() {
  const loadTurnstileScript = () => {
    return new Promise((resolve) => {
      // Already loaded
      if (window.turnstile) {
        resolve()
        return
      }

      // Check if script already exists
      const existingScript = document.querySelector(
        'script[src="https://challenges.cloudflare.com/turnstile/v0/api.js"]',
      )
      if (existingScript) {
        existingScript.addEventListener('load', () => resolve())
        return
      }

      // Append script tag
      const script = document.createElement('script')
      script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js'
      script.async = true
      script.defer = true
      script.onload = () => resolve()
      document.head.appendChild(script)
    })
  }

  return { loadTurnstileScript }
}
