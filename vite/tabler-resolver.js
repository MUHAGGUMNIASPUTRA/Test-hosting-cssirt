// File: /vite/tabler-resolver.js
export function TablerIconsResolver({ prefix = 'Icon' } = {}) {
  return (name) => {
    if (!name.startsWith(prefix)) return

    const iconName = name.slice(prefix.length)
    return {
      name,
      from: `@tabler/icons-vue`,
      sideEffects: [],
    }
  }
}
