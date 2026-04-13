// .claude/hooks/check-claude-md.js
// Claude Code Stop/PostToolUse hook — prints reminder when structural files change.
// Reads JSON payload from stdin (Claude Code hook protocol).

import { createReadStream } from 'fs'

let payload = ''

process.stdin.setEncoding('utf8')
process.stdin.on('data', (chunk) => {
  payload += chunk
})
process.stdin.on('end', () => {
  let data = {}
  try {
    data = JSON.parse(payload)
  } catch {
    process.exit(0)
  }

  const paths = JSON.stringify(data)

  const STRUCTURAL_PATTERNS = [
    /app\/Http\/Controllers\//,
    /app\/Http\/Requests\//,
    /app\/Services\//,
    /app\/Enums\//,
    /resources\/js\/Composables\//,
    /resources\/js\/Components\//,
    /resources\/js\/utils\//,
    /routes\/web\.php/,
    /database\/migrations\//,
    /CLAUDE\.md/,
  ]

  const affected = STRUCTURAL_PATTERNS.some((p) => p.test(paths))

  if (affected) {
    console.log(
      '\n[CLAUDE.md REMINDER] File struktural berubah — periksa apakah CLAUDE.md perlu diperbarui:\n' +
        '  - CLAUDE.md (root)          — routing, model, controller list\n' +
        '  - app/CLAUDE.md             — services, enums, form requests, controller pattern\n' +
        '  - resources/js/CLAUDE.md    — komponen baru, composable, utils, pola delete\n',
    )
  }

  process.exit(0)
})
