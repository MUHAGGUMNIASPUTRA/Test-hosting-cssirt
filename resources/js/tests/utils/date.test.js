import { describe, it, expect } from 'vitest'
import { formatDate, formatDatetime, formatRelative } from '@/utils/date'

describe('formatDate', () => {
  it('returns "—" for null', () => {
    expect(formatDate(null)).toBe('—')
  })

  it('returns "—" for undefined', () => {
    expect(formatDate(undefined)).toBe('—')
  })

  it('returns "—" for empty string', () => {
    expect(formatDate('')).toBe('—')
  })

  it('returns "—" for an invalid date string', () => {
    expect(formatDate('not-a-date')).toBe('—')
  })

  it('formats a valid ISO date string in Indonesian locale', () => {
    // 2026-04-13 should contain "Apr" and "2026"
    const result = formatDate('2026-04-13')
    expect(result).toContain('2026')
    expect(result).toMatch(/Apr|04/)
  })

  it('accepts a Date object', () => {
    const result = formatDate(new Date('2026-01-01'))
    expect(result).toContain('2026')
  })
})

describe('formatDatetime', () => {
  it('returns "—" for null', () => {
    expect(formatDatetime(null)).toBe('—')
  })

  it('returns "—" for invalid input', () => {
    expect(formatDatetime('bad')).toBe('—')
  })

  it('includes both date and time parts', () => {
    const result = formatDatetime('2026-04-13T14:30:00')
    expect(result).toContain('2026')
    // Should contain hours:minutes pattern
    expect(result).toMatch(/\d{2}[:.]\d{2}/)
  })
})

describe('formatRelative', () => {
  it('returns "—" for null', () => {
    expect(formatRelative(null)).toBe('—')
  })

  it('returns "Baru saja" for a date within the last minute', () => {
    const recent = new Date(Date.now() - 10 * 1000) // 10 seconds ago
    expect(formatRelative(recent)).toBe('Baru saja')
  })

  it('returns menit lalu for a date 5 minutes ago', () => {
    const fiveMinAgo = new Date(Date.now() - 5 * 60 * 1000)
    expect(formatRelative(fiveMinAgo)).toContain('menit lalu')
  })

  it('returns jam lalu for a date 3 hours ago', () => {
    const threeHrsAgo = new Date(Date.now() - 3 * 60 * 60 * 1000)
    expect(formatRelative(threeHrsAgo)).toContain('jam lalu')
  })

  it('returns hari lalu for a date 2 days ago', () => {
    const twoDaysAgo = new Date(Date.now() - 2 * 24 * 60 * 60 * 1000)
    expect(formatRelative(twoDaysAgo)).toContain('hari lalu')
  })

  it('falls back to formatDate for dates older than 30 days', () => {
    const old = new Date(Date.now() - 60 * 24 * 60 * 60 * 1000)
    const result = formatRelative(old)
    // Should not contain "lalu" — falls back to formatted date
    expect(result).not.toContain('lalu')
    expect(result).toContain(String(old.getFullYear()))
  })
})
