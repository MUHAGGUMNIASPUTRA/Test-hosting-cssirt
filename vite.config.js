import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';

import Icons from 'unplugin-icons/vite';
import IconsResolver from 'unplugin-icons/resolver';

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
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
      // Tell the plugin where to scan for components
      dirs: ['resources/js/Components', 'resources/js/Layouts'],

      // This part is for PrimeVue auto-import (already exists)
      resolvers: [
        PrimeVueResolver(),
        IconsResolver({
          prefix: 'i',
        }),
      ],

      // This makes it work with Inertia's <Link> component
      directives: false,
    }),
    Icons({
      autoInstall: true,
    }),
  ],
});
