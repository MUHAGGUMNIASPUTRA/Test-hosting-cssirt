// .claude/hooks/format-on-save.js
// Claude Code PostToolUse hook — auto-formats file after Write/Edit.
// PHP → Laravel Pint | JS/Vue/TS/CSS/JSON → Prettier

import { spawnSync } from 'child_process'

const JS_EXTS = new Set(['js', 'vue', 'ts', 'mjs', 'cjs', 'css', 'json'])
const isWin   = process.platform === 'win32'

function run(cmd, args) {
  return spawnSync(cmd, args, { stdio: 'inherit', shell: true })
}

let raw = ''
process.stdin.setEncoding('utf8')
process.stdin.on('data', (chunk) => { raw += chunk })
process.stdin.on('end', () => {
  let data = {}
  try { data = JSON.parse(raw) } catch { process.exit(0) }

  const filePath = data?.tool_input?.file_path
  if (!filePath) process.exit(0)

  // Skip vendor/, node_modules/, bootstrap/, public/, storage/
  if (/[\\/](vendor|node_modules|bootstrap|public|storage)[\\/]/.test(filePath)) {
    process.exit(0)
  }

  const ext = filePath.split('.').pop()?.toLowerCase()

  if (ext === 'php') {
    const pint   = isWin ? 'vendor\\bin\\pint.bat' : './vendor/bin/pint'
    const result = run(pint, [filePath])
    if (result.status !== 0) {
      console.warn(`[format-on-save] Pint returned non-zero for: ${filePath}`)
    }
  } else if (JS_EXTS.has(ext)) {
    const result = run('npx', ['prettier', '--write', '--log-level', 'warn', filePath])
    if (result.status !== 0) {
      console.warn(`[format-on-save] Prettier returned non-zero for: ${filePath}`)
    }
  }

  process.exit(0)
})
