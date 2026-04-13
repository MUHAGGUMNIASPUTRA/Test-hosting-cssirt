import { describe, it, expect } from 'vitest'
import { isExternalUrl, getFileExtension, formatFileSize, storageUrl } from '@/utils/file'

describe('isExternalUrl', () => {
  it('returns false for null', () => {
    expect(isExternalUrl(null)).toBe(false)
  })

  it('returns false for undefined', () => {
    expect(isExternalUrl(undefined)).toBe(false)
  })

  it('returns false for a relative storage path', () => {
    expect(isExternalUrl('documents/official/file.pdf')).toBe(false)
  })

  it('returns true for http:// URLs', () => {
    expect(isExternalUrl('http://example.com/file.pdf')).toBe(true)
  })

  it('returns true for https:// URLs', () => {
    expect(isExternalUrl('https://jdih.bojonegorokab.go.id/perda.pdf')).toBe(true)
  })
})

describe('getFileExtension', () => {
  it('returns empty string for null', () => {
    expect(getFileExtension(null)).toBe('')
  })

  it('returns empty string when there is no extension', () => {
    expect(getFileExtension('filename')).toBe('')
  })

  it('returns the extension in lowercase', () => {
    expect(getFileExtension('document.PDF')).toBe('pdf')
    expect(getFileExtension('image.JPG')).toBe('jpg')
  })

  it('strips query string before checking extension', () => {
    expect(getFileExtension('file.pdf?token=abc')).toBe('pdf')
  })

  it('strips hash fragment before checking extension', () => {
    expect(getFileExtension('file.pdf#page=2')).toBe('pdf')
  })

  it('handles nested paths correctly', () => {
    expect(getFileExtension('documents/official/perda.pdf')).toBe('pdf')
  })
})

describe('formatFileSize', () => {
  it('returns "—" for null', () => {
    expect(formatFileSize(null)).toBe('—')
  })

  it('returns "—" for negative values', () => {
    expect(formatFileSize(-1)).toBe('—')
  })

  it('returns "0 B" for zero', () => {
    expect(formatFileSize(0)).toBe('0 B')
  })

  it('formats bytes', () => {
    expect(formatFileSize(512)).toBe('512.00 B')
  })

  it('formats kilobytes', () => {
    expect(formatFileSize(1024)).toBe('1.00 KB')
  })

  it('formats megabytes', () => {
    expect(formatFileSize(1024 * 1024)).toBe('1.00 MB')
  })

  it('formats gigabytes', () => {
    expect(formatFileSize(1024 * 1024 * 1024)).toBe('1.00 GB')
  })

  it('respects precision parameter', () => {
    expect(formatFileSize(1536, 1)).toBe('1.5 KB') // 1536 / 1024 = 1.5
  })
})

describe('storageUrl', () => {
  it('returns null for null path', () => {
    expect(storageUrl(null)).toBeNull()
  })

  it('returns null for empty string', () => {
    expect(storageUrl('')).toBeNull()
  })

  it('returns path-through for external URLs', () => {
    const url = 'https://external.example.com/file.pdf'
    expect(storageUrl(url)).toBe(url)
  })

  it('prepends /storage/ for relative paths', () => {
    expect(storageUrl('documents/official/perda.pdf')).toBe(
      '/storage/documents/official/perda.pdf',
    )
  })

  it('prepends /storage/ for posts image paths', () => {
    expect(storageUrl('posts/cover.jpg')).toBe('/storage/posts/cover.jpg')
  })
})
