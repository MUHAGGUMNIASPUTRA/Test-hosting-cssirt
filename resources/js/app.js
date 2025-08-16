import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

import PrimeVue from 'primevue/config'
import Noir from "./presets/noir";
import 'primeicons/primeicons.css'

import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

const appName = import.meta.env.VITE_APP_NAME || 'CSIRT Bojonegoro'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, {
        theme: {
          preset: Noir,
          options: {
            prefix: 'p',
            darkModeSelector: '.dark',
          },
        },
        ripple: true,
      })
      .use(ConfirmationService)
      .use(ToastService)
      .mount(el)
  },
  // Progress bar enabled for client
  progress: {
    color: '#4B5563',
  },
})
