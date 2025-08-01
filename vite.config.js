import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';

import Icons from 'unplugin-icons/vite';
import IconsResolver from 'unplugin-icons/resolver';

export default defineConfig(({ command, mode, isSsrBuild }) => ({
  plugins: [
    laravel({
      input: isSsrBuild ? 'resources/js/ssr.js' : 'resources/js/app.js',
      ssr: isSsrBuild ? 'resources/js/ssr.js' : undefined,
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    Components({
      dirs: ['resources/js/Components', 'resources/js/Layouts'],
      resolvers: [
        PrimeVueResolver(),
        IconsResolver({
          prefix: 'i',
        }),
      ],
      directives: false,
    }),
    Icons({
      autoInstall: true,
    }),
  ],
  ssr: {
    noExternal: [
      'primevue',
      '@primevue/core',
      '@primevue/themes',
      '@primevue/auto-import-resolver',
      'primeicons',
      '@inertiajs/vue3',
      '@inertiajs/core',
      'laravel-vite-plugin'
    ],
    external: ['fs', 'path', 'url'],
    // Allow Node.js modules in SSR
    target: 'node',
  },
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
  define: {
    global: 'globalThis',
    // Disable SSR-incompatible features
    'process.browser': 'false',
  },
}));
