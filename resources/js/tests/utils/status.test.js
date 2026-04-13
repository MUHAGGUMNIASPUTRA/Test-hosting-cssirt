import { describe, it, expect } from 'vitest'
import { getSeverity, getStatusLabel } from '@/utils/status'

describe('getSeverity', () => {
  describe('incident-status', () => {
    it.each([
      ['Baru', 'info'],
      ['Diverifikasi', 'primary'],
      ['Dalam Penyelidikan', 'warn'],
      ['Selesai', 'success'],
      ['Ditutup', 'secondary'],
    ])('maps %s → %s', (value, expected) => {
      expect(getSeverity('incident-status', value)).toBe(expected)
    })
  })

  describe('priority', () => {
    it.each([
      ['Rendah', 'success'],
      ['Sedang', 'info'],
      ['Tinggi', 'warn'],
      ['Kritikal', 'danger'],
    ])('maps %s → %s', (value, expected) => {
      expect(getSeverity('priority', value)).toBe(expected)
    })
  })

  describe('post-status', () => {
    it('maps Published → success', () => {
      expect(getSeverity('post-status', 'Published')).toBe('success')
    })

    it('maps Draft → warn', () => {
      expect(getSeverity('post-status', 'Draft')).toBe('warn')
    })
  })

  describe('published', () => {
    it('maps true → success', () => {
      expect(getSeverity('published', true)).toBe('success')
    })

    it('maps false → secondary', () => {
      expect(getSeverity('published', false)).toBe('secondary')
    })
  })

  it('returns "info" as fallback for unknown type', () => {
    expect(getSeverity('unknown-type', 'anything')).toBe('info')
  })

  it('returns "info" as fallback for unknown value in known type', () => {
    expect(getSeverity('priority', 'SangatKritikal')).toBe('info')
  })
})

describe('getStatusLabel', () => {
  it('returns "Diterbitkan" for post-status Published', () => {
    expect(getStatusLabel('post-status', 'Published')).toBe('Diterbitkan')
  })

  it('returns "Draft" for post-status Draft', () => {
    expect(getStatusLabel('post-status', 'Draft')).toBe('Draft')
  })

  it('returns "Diterbitkan" for published true', () => {
    expect(getStatusLabel('published', true)).toBe('Diterbitkan')
  })

  it('returns "Draft" for published false', () => {
    expect(getStatusLabel('published', false)).toBe('Draft')
  })

  it('returns raw value string for types without label mapping', () => {
    expect(getStatusLabel('incident-status', 'Selesai')).toBe('Selesai')
  })

  it('returns raw value string for unknown type', () => {
    expect(getStatusLabel('unknown', 'SomeValue')).toBe('SomeValue')
  })
})
