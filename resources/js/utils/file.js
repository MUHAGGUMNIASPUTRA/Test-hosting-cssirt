/**
 * File utility functions — pure, non-reactive.
 * Import explicitly: import { isExternalUrl } from '@/utils/file'
 */

/**
 * Check whether a path is an external URL (starts with http:// or https://).
 * Documents with external official_file_path are view-only (no download).
 *
 * @param {string|null|undefined} path
 * @returns {boolean}
 */
export function isExternalUrl(path) {
  if (!path) return false
  return path.startsWith('http://') || path.startsWith('https://')
}

/**
 * Get the file extension from a path or URL (without the leading dot).
 * Returns empty string when there is no extension.
 *
 * @param {string|null|undefined} path
 * @returns {string}
 */
export function getFileExtension(path) {
  if (!path) return ''
  // Strip query string and hash before extracting extension
  const clean = path.split('?')[0].split('#')[0]
  const parts = clean.split('.')
  return parts.length > 1 ? parts[parts.length - 1].toLowerCase() : ''
}

/**
 * Format a byte count into a human-readable size string, e.g. "1.23 MB".
 *
 * @param {number|null|undefined} bytes
 * @param {number} [precision=2]
 * @returns {string}
 */
export function formatFileSize(bytes, precision = 2) {
  if (bytes == null || bytes < 0) return '—'
  if (bytes === 0) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB', 'TB']
  let i       = 0
  let size    = bytes
  while (size >= 1024 && i < units.length - 1) {
    size /= 1024
    i++
  }
  return `${size.toFixed(precision)} ${units[i]}`
}

/**
 * Build the storage URL for a file stored in Laravel's public disk.
 * Pass-through for external URLs. Returns null for empty paths.
 *
 * @param {string|null|undefined} path
 * @returns {string|null}
 */
export function storageUrl(path) {
  if (!path) return null
  if (isExternalUrl(path)) return path
  return `/storage/${path}`
}
