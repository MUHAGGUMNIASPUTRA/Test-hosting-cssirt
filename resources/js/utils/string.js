/**
 * String utility functions — pure, non-reactive.
 * Import explicitly: import { truncate, slugify } from '@/utils/string'
 */

/**
 * Truncate a string to a maximum length, appending "…" if cut.
 * Returns "—" for null/undefined input.
 *
 * @param {string|null|undefined} str
 * @param {number} [maxLength=80]
 * @returns {string}
 */
export function truncate(str, maxLength = 80) {
  if (str == null) return '—'
  if (str.length <= maxLength) return str
  return str.slice(0, maxLength).trimEnd() + '…'
}

/**
 * Convert a string to a URL-safe slug (lowercase, hyphens, no special chars).
 * Handles basic Indonesian characters (accented vowels).
 *
 * @param {string|null|undefined} str
 * @returns {string}
 */
export function slugify(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .replace(/[àáâãäå]/g, 'a')
    .replace(/[èéêë]/g, 'e')
    .replace(/[ìíîï]/g, 'i')
    .replace(/[òóôõö]/g, 'o')
    .replace(/[ùúûü]/g, 'u')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/[\s_]+/g, '-')
    .replace(/-+/g, '-')
}

/**
 * Capitalize the first letter of a string.
 *
 * @param {string|null|undefined} str
 * @returns {string}
 */
export function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}
