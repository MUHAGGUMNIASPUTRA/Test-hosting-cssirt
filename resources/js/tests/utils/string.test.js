import { describe, it, expect } from 'vitest'
import { truncate, slugify, capitalize } from '@/utils/string'

describe('truncate', () => {
  it('returns "—" for null', () => {
    expect(truncate(null)).toBe('—')
  })

  it('returns "—" for undefined', () => {
    expect(truncate(undefined)).toBe('—')
  })

  it('returns the string unchanged when within limit', () => {
    expect(truncate('Hello world', 20)).toBe('Hello world')
  })

  it('truncates and appends ellipsis when over limit', () => {
    const long   = 'Ini adalah kalimat yang sangat panjang sekali dan perlu dipotong'
    const result = truncate(long, 20)
    expect(result.length).toBeLessThanOrEqual(21) // 20 chars + "…"
    expect(result).toEndWith('…')
  })

  it('uses default maxLength of 80', () => {
    const str80  = 'a'.repeat(80)
    const str81  = 'a'.repeat(81)
    expect(truncate(str80)).toBe(str80)
    expect(truncate(str81)).toEndWith('…')
  })
})

describe('slugify', () => {
  it('returns empty string for null', () => {
    expect(slugify(null)).toBe('')
  })

  it('converts to lowercase and replaces spaces with hyphens', () => {
    expect(slugify('Hello World')).toBe('hello-world')
  })

  it('collapses multiple spaces and hyphens', () => {
    expect(slugify('hello   world')).toBe('hello-world')
    expect(slugify('hello--world')).toBe('hello-world')
  })

  it('removes special characters', () => {
    expect(slugify('Keamanan & Siber!')).toBe('keamanan-siber')
  })

  it('handles basic Indonesian accented characters', () => {
    expect(slugify('éducation')).toBe('education')
  })

  it('handles a typical article title', () => {
    expect(slugify('Panduan Keamanan Siber 2026')).toBe('panduan-keamanan-siber-2026')
  })
})

describe('capitalize', () => {
  it('returns empty string for null', () => {
    expect(capitalize(null)).toBe('')
  })

  it('capitalizes the first letter', () => {
    expect(capitalize('hello world')).toBe('Hello world')
  })

  it('does not change already-capitalized strings', () => {
    expect(capitalize('Hello')).toBe('Hello')
  })

  it('works on single character', () => {
    expect(capitalize('a')).toBe('A')
  })
})
