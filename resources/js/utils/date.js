/**
 * Date utility functions — pure, non-reactive.
 * Import explicitly: import { formatDate } from '@/utils/date'
 */

const LOCALE = 'id-ID'

/**
 * Format a date to a short readable string, e.g. "13 Apr 2026".
 * Returns "—" for null/undefined/invalid input.
 *
 * @param {string|Date|null|undefined} date
 * @returns {string}
 */
export function formatDate(date) {
  if (!date) return '—'
  const d = new Date(date)
  if (isNaN(d)) return '—'
  return new Intl.DateTimeFormat(LOCALE, {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(d)
}

/**
 * Format a date-time to a readable string, e.g. "13 Apr 2026, 14:30".
 * Returns "—" for null/undefined/invalid input.
 *
 * @param {string|Date|null|undefined} date
 * @returns {string}
 */
export function formatDatetime(date) {
  if (!date) return '—'
  const d = new Date(date)
  if (isNaN(d)) return '—'
  return new Intl.DateTimeFormat(LOCALE, {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(d)
}

/**
 * Format a date as a relative human-readable string, e.g. "3 hari lalu".
 * Falls back to formatDate for dates older than 30 days.
 *
 * @param {string|Date|null|undefined} date
 * @returns {string}
 */
export function formatRelative(date) {
  if (!date) return '—'
  const d = new Date(date)
  if (isNaN(d)) return '—'

  const diffMs = Date.now() - d.getTime()
  const diffSeconds = Math.floor(diffMs / 1000)
  const diffMinutes = Math.floor(diffSeconds / 60)
  const diffHours = Math.floor(diffMinutes / 60)
  const diffDays = Math.floor(diffHours / 24)

  if (diffSeconds < 60) return 'Baru saja'
  if (diffMinutes < 60) return `${diffMinutes} menit lalu`
  if (diffHours < 24) return `${diffHours} jam lalu`
  if (diffDays <= 30) return `${diffDays} hari lalu`

  return formatDate(d)
}
