/**
 * Status utility functions — pure, non-reactive.
 * Centralises severity/label mappings for use outside Vue components
 * (e.g. in Vitest tests or composables).
 *
 * For component rendering, prefer <StatusBadge> which uses these same maps.
 * Import explicitly: import { getSeverity, getStatusLabel } from '@/utils/status'
 */

/** @type {Record<string, Record<string, string>>} */
const SEVERITY_MAP = {
  'incident-status': {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  },
  priority: {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  },
  'post-status': {
    Published: 'success',
    Draft: 'warn',
  },
  published: {
    true: 'success',
    false: 'secondary',
  },
  'document-stage': {
    'Perlu Dibuat': 'secondary',
    'Telah Dibuat': 'info',
    'Perlu Review': 'warn',
    'Telah Direview': 'info',
    'Perlu TTD': 'warn',
    Final: 'success',
  },
}

/** @type {Record<string, Record<string, string>>} */
const LABEL_MAP = {
  'post-status': {
    Published: 'Diterbitkan',
    Draft: 'Draft',
  },
  published: {
    true: 'Diterbitkan',
    false: 'Draft',
  },
}

/**
 * Get the PrimeVue Tag severity for a given status type and value.
 * Returns 'info' as fallback when the mapping is not found.
 *
 * @param {'incident-status'|'priority'|'post-status'|'published'} type
 * @param {string|boolean} value
 * @returns {string}
 */
export function getSeverity(type, value) {
  return SEVERITY_MAP[type]?.[value] ?? 'info'
}

/**
 * Get the human-readable label for a given status type and value.
 * Returns the raw string value when no label mapping is defined.
 *
 * @param {'incident-status'|'priority'|'post-status'|'published'} type
 * @param {string|boolean} value
 * @returns {string}
 */
export function getStatusLabel(type, value) {
  const map = LABEL_MAP[type]
  return map ? (map[value] ?? String(value)) : String(value)
}
