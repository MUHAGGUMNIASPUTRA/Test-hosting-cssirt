import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createSSRApp, h, ref, computed, reactive } from 'vue'
// Mock Ziggy route function for SSR
const route = (name, params = {}) => {
  const routes = {
    home: '/',
    'services.index': '/services',
    'posts.index': '/posts',
    'contact.index': '/contact',
    'faq.index': '/faq',
    'profile.index': '/profile',
    'incidents.create': '/incidents/create',
  }
  return routes[name] || '#'
}
import { renderToString } from '@vue/server-renderer'
import { createServer } from 'http'

import PrimeVue from 'primevue/config'
import Noir from './presets/noir'

import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'

// Import any missing components that might be referenced
import Toast from 'primevue/toast'

// Import layouts that might be needed
// Note: We'll let these be resolved dynamically to avoid circular dependencies

// Create global mocks for composables used in components
const createInertiaPageMock = () => {
  return {
    usePage: () => ref(null), // Will be overridden in the app setup
    Link: 'RouterLink', // Component placeholder
    Head: 'Head', // Component placeholder
  }
}

// Comprehensive SSR DOM polyfills - must be first
if (typeof global !== 'undefined' && typeof window === 'undefined') {
  const createElementMock = (tagName = 'div') => ({
    tagName: tagName.toUpperCase(),
    children: [],
    childNodes: [],
    parentNode: null,
    nextSibling: null,
    previousSibling: null,
    firstChild: null,
    lastChild: null,
    nodeType: 1,
    nodeName: tagName.toUpperCase(),

    // Style and classes
    style: new Proxy(
      {},
      {
        get: () => '',
        set: () => true,
      },
    ),
    classList: {
      add: () => {},
      remove: () => {},
      contains: () => false,
      toggle: () => {},
      replace: () => {},
      value: '',
    },
    className: '',

    // Attributes and dataset
    attributes: {},
    dataset: new Proxy(
      {},
      {
        get: () => '',
        set: () => true,
      },
    ),

    setAttribute: function (name, value) {
      this.attributes[name] = value
      if (name.startsWith('data-')) {
        const key = name
          .slice(5)
          .replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())
        this.dataset[key] = value
      }
    },
    getAttribute: function (name) {
      return this.attributes[name] || null
    },
    hasAttribute: function (name) {
      return name in this.attributes
    },
    removeAttribute: function (name) {
      delete this.attributes[name]
    },

    // DOM manipulation
    appendChild: function (child) {
      if (child && typeof child === 'object') {
        this.children.push(child)
        this.childNodes.push(child)
        child.parentNode = this
        if (!this.firstChild) this.firstChild = child
        this.lastChild = child
      }
      return child
    },
    removeChild: function (child) {
      const index = this.children.indexOf(child)
      if (index > -1) {
        this.children.splice(index, 1)
        this.childNodes.splice(index, 1)
        child.parentNode = null
      }
      return child
    },
    insertBefore: function (newChild, referenceChild) {
      const index = this.children.indexOf(referenceChild)
      if (index > -1) {
        this.children.splice(index, 0, newChild)
        this.childNodes.splice(index, 0, newChild)
      } else {
        this.appendChild(newChild)
      }
      return newChild
    },

    // Event handling
    addEventListener: () => {},
    removeEventListener: () => {},

    // Query methods
    querySelector: () => null,
    querySelectorAll: () => [],
    getElementsByTagName: () => [],
    getElementsByClassName: () => [],
    getElementById: () => null,

    // Content
    innerHTML: '',
    outerHTML: '',
    textContent: '',
    innerText: '',

    // Dimensions
    offsetWidth: 0,
    offsetHeight: 0,
    offsetTop: 0,
    offsetLeft: 0,
    clientWidth: 0,
    clientHeight: 0,
    scrollWidth: 0,
    scrollHeight: 0,
    scrollTop: 0,
    scrollLeft: 0,

    getBoundingClientRect: () => ({
      top: 0,
      left: 0,
      bottom: 0,
      right: 0,
      width: 0,
      height: 0,
      x: 0,
      y: 0,
      toJSON: () => ({}),
    }),

    // Form elements
    value: '',
    checked: false,
    selected: false,
    disabled: false,

    // Focus
    focus: () => {},
    blur: () => {},

    // Clone
    cloneNode: function (deep = false) {
      return createElementMock(this.tagName)
    },
  })

  // Create comprehensive document mock
  const documentMock = {
    head: createElementMock('head'),
    body: createElementMock('body'),
    documentElement: createElementMock('html'),
    activeElement: null,

    createElement: createElementMock,
    createTextNode: (text) => ({
      textContent: text,
      nodeType: 3,
      nodeName: '#text',
      parentNode: null,
    }),
    createDocumentFragment: () => createElementMock('fragment'),

    getElementById: () => null,
    querySelector: () => null,
    querySelectorAll: () => [],
    getElementsByTagName: () => [],
    getElementsByClassName: () => [],
    getElementsByName: () => [],

    addEventListener: () => {},
    removeEventListener: () => {},

    cookie: '',
    domain: '',
    referrer: '',
    title: '',
    URL: '',
    readyState: 'complete',

    // For compatibility
    defaultView: null,
  }

  // Set activeElement
  documentMock.activeElement = documentMock.body
  documentMock.defaultView = null

  // Create window mock
  global.window = {
    document: documentMock,

    location: {
      href: 'http://localhost/',
      origin: 'http://localhost',
      protocol: 'http:',
      host: 'localhost',
      hostname: 'localhost',
      port: '',
      pathname: '/',
      search: '',
      hash: '',
      assign: () => {},
      replace: () => {},
      reload: () => {},
    },

    navigator: {
      userAgent: 'SSR/1.0',
      platform: 'SSR',
      language: 'en',
      languages: ['en'],
      cookieEnabled: true,
      onLine: true,
    },

    screen: {
      width: 1920,
      height: 1080,
      availWidth: 1920,
      availHeight: 1080,
    },

    history: {
      length: 1,
      state: null,
      pushState: () => {},
      replaceState: () => {},
      back: () => {},
      forward: () => {},
      go: () => {},
    },

    // Styles and layout
    getComputedStyle: () =>
      new Proxy(
        {},
        {
          get: () => '',
          set: () => true,
        },
      ),

    // Media queries
    matchMedia: () => ({
      matches: false,
      media: '',
      addEventListener: () => {},
      removeEventListener: () => {},
    }),

    // Events
    addEventListener: () => {},
    removeEventListener: () => {},
    dispatchEvent: () => true,

    // Timing
    setTimeout: setTimeout,
    clearTimeout: clearTimeout,
    setInterval: setInterval,
    clearInterval: clearInterval,
    requestAnimationFrame: (fn) => setTimeout(fn, 16),
    cancelAnimationFrame: clearTimeout,

    // Storage
    localStorage: {
      getItem: () => null,
      setItem: () => {},
      removeItem: () => {},
      clear: () => {},
      key: () => null,
      length: 0,
    },
    sessionStorage: {
      getItem: () => null,
      setItem: () => {},
      removeItem: () => {},
      clear: () => {},
      key: () => null,
      length: 0,
    },

    // Window properties
    innerWidth: 1920,
    innerHeight: 1080,
    outerWidth: 1920,
    outerHeight: 1080,
    pageXOffset: 0,
    pageYOffset: 0,
    scrollX: 0,
    scrollY: 0,

    // Console
    console: console,

    // Other globals
    alert: () => {},
    confirm: () => false,
    prompt: () => null,
    open: () => null,
    close: () => {},
    focus: () => {},
    blur: () => {},

    // Performance
    performance: {
      now: () => Date.now(),
    },
  }

  // Set global references
  global.document = global.window.document
  global.navigator = global.window.navigator
  global.location = global.window.location
  global.history = global.window.history
  global.localStorage = global.window.localStorage
  global.sessionStorage = global.window.sessionStorage

  // Additional globals that might be accessed
  global.HTMLElement = function () {}
  global.Element = function () {}
  global.Node = function () {}
  global.NodeList = function () {
    return []
  }
  global.HTMLCollection = function () {
    return []
  }
}

const appName = process.env.VITE_APP_NAME || 'CSIRT Bojonegoro'

const server = createServer(async (request, response) => {
  try {
    const url = new URL(request.url || '', `http://${request.headers.host}`)

    if (url.pathname === '/render') {
      let body = ''

      request.on('data', (chunk) => {
        body += chunk.toString()
      })

      request.on('end', async () => {
        try {
          const { page } = JSON.parse(body)
          console.log('Received page data:', JSON.stringify(page, null, 2))

          try {
            console.log('About to render SSR manually...')

            // Create a mock route helper function
            const routeHelper = (name, params = {}) => {
              // Define route mappings for SSR
              const routes = {
                landing: '/',
                'profile.show': '/profile',
                'services.index': '/services',
                'posts.index': '/posts',
                'faq.index': '/faq',
                'contact.index': '/contact',
                'incident.create': '/incident',
                login: '/login',
                'admin.dashboard': '/admin',
                'posts.show': (params) =>
                  `/posts/${params.post || params.slug || ''}`,
                'categories.show': (params) =>
                  `/posts/categories/${params.category || params.slug || ''}`,
              }

              const route = routes[name]
              if (typeof route === 'function') {
                return route(params)
              }
              return route || `/${name.replace('.', '/')}`
            }

            // Create Inertia page mock that matches the real structure
            const createPageMock = (pageData) => {
              const mockPage = {
                component: pageData.component, // Keep original component name for props context
                props: {
                  auth: {
                    user: null, // For public pages, user is null
                  },
                  flash: {
                    success: null,
                    error: null,
                    info: null,
                    warning: null,
                  },
                  errors: {},
                  ...pageData.props,
                },
                url: pageData.url,
                version: pageData.version || '1',
                scrollRegions: [],
                rememberedState: {},
                resolvedErrors: {},
              }

              // Add reactive wrapper for the page
              return reactive(mockPage)
            }

            // Create the page mock
            const pageMock = createPageMock(page)

            // Map regular components to their SEO versions
            const seoComponentMapping = {
              Landing: 'SEOLanding',
              'Services/Index': 'SEOServices',
              'Posts/Index': 'SEOPosts',
              'Posts/Show': 'SEOPostShow',
              'Categories/Show': 'SEOCategoryShow',
              'Contact/Index': 'SEOContact',
              'Documents/Index': 'SEODocuments',
              'Faq/Index': 'SEOFAQ',
              'Incidents/Create': 'SEOIncident',
              'Profile/Index': 'SEOProfile',
            }

            // Use SEO component if available, otherwise fallback to original
            const actualComponent =
              seoComponentMapping[page.component] || page.component
            console.log(`Mapping ${page.component} → ${actualComponent}`)

            // Resolve the component with the mapped name
            const componentResolver = resolvePageComponent(
              `./Pages/${actualComponent}.vue`,
              import.meta.glob('./Pages/**/*.vue'),
            )

            const resolvedComponent = await componentResolver
            console.log('Resolved component successfully')

            // Create Vue app with proper Inertia context
            const app = createSSRApp({
              render: () =>
                h(resolvedComponent.default, {
                  ...page.props,
                }),
            })

            // Mock Inertia's usePage composable properly
            const pageRef = ref(pageMock)

            // Provide the page for Inertia
            app.provide('inertia', {
              page: pageRef,
            })

            // Create a proper usePage function that components can import
            const usePageMock = () => pageRef
            app.provide('usePage', usePageMock)

            // Also make it available globally for components that import usePage
            app.config.globalProperties.usePage = usePageMock

            // Add global properties for SSR compatibility
            app.config.globalProperties.$page = pageMock
            app.config.globalProperties.$inertia = {
              page: pageMock,
              visit: () => {},
              get: () => {},
              post: () => {},
              put: () => {},
              patch: () => {},
              delete: () => {},
              remember: () => {},
              restore: () => {},
            }

            // Add route helper
            app.config.globalProperties.route = routeHelper
            app.provide('route', routeHelper)

            // Add PrimeVue with proper SSR-safe configuration
            app.use(PrimeVue, {
              theme: {
                preset: Noir,
                options: {
                  prefix: 'p',
                  darkModeSelector: '.p-dark',
                  cssLayer: false, // Disable CSS layers for SSR
                },
              },
              ripple: false, // Disable ripple for SSR
              csp: {
                nonce: undefined, // No nonce needed for SSR
              },
            })

            // Add PrimeVue services - these need to be added before other providers
            app.use(ToastService)
            app.use(ConfirmationService)

            // Mock PrimeVue providers that might be missing in SSR
            app.provide('primevue', {
              config: {
                theme: {
                  preset: Noir,
                },
              },
            })

            // Register global components that might be used
            app.component('Toast', Toast)

            // Register Inertia components for SSR
            app.component('Head', {
              props: ['title'],
              template: '<template></template>', // No-op for SSR
            })

            app.component('Link', {
              props: [
                'href',
                'method',
                'data',
                'headers',
                'preserveState',
                'preserveScroll',
                'only',
                'except',
              ],
              template: '<a :href="href"><slot /></a>', // Simple anchor for SSR
            })

            // Register loading-page component (used in AppLayout)
            app.component('loading-page', {
              template: '<template></template>', // No-op for SSR
            })

            // Mock useToast composable for SSR - make it globally available
            const mockToast = {
              add: () => {},
              remove: () => {},
              removeGroup: () => {},
              removeAllGroups: () => {},
            }
            app.provide('primevue/usetoast', mockToast)
            app.config.globalProperties.useToast = () => mockToast

            // Mock useResponsive composable - make it globally available
            const mockResponsive = {
              isMobile: computed(() => false),
              isTablet: computed(() => false),
              isDesktop: computed(() => true),
            }
            app.provide('useResponsive', () => mockResponsive)
            app.config.globalProperties.useResponsive = () => mockResponsive

            // Mock Ziggy route function globally for components that might use it
            app.config.globalProperties.route = route
            app.provide('ziggy-route', route)

            // Add error handler
            app.config.errorHandler = (error, instance, info) => {
              console.error('SSR Vue Error:', error.message)
              console.error('Error info:', info)
              // For SSR, we don't want to crash - just continue
            }

            // Render the app
            console.log('About to render with renderToString...')
            const html = await renderToString(app)

            console.log(
              'SSR HTML result:',
              html ? html.length : 0,
              'characters',
            )

            // For SEO, we want to wrap in basic HTML structure if it's just content
            const finalHtml = html.startsWith('<') ? html : `<div>${html}</div>`

            console.log('Final HTML length:', finalHtml.length)

            response.writeHead(200, {
              'Content-Type': 'application/json',
            })

            response.end(JSON.stringify({ html: finalHtml }))
          } catch (renderError) {
            console.error('CreateInertiaApp Error:', renderError)
            console.error('Render Error Stack:', renderError.stack)

            response.writeHead(500, {
              'Content-Type': 'application/json',
            })

            response.end(
              JSON.stringify({
                error: `Render Error: ${renderError.message}`,
                stack: renderError.stack,
              }),
            )
          }
        } catch (error) {
          console.error('SSR Render Error:', error)
          response.writeHead(500, {
            'Content-Type': 'application/json',
          })

          response.end(
            JSON.stringify({
              error: error.message,
              stack: error.stack,
            }),
          )
        }
      })
    } else {
      response.writeHead(404)
      response.end('Not Found')
    }
  } catch (error) {
    console.error('SSR Server Error:', error)
    response.writeHead(500, {
      'Content-Type': 'application/json',
    })

    response.end(
      JSON.stringify({
        error: error.message,
        stack: error.stack,
      }),
    )
  }
})

const port = process.env.INERTIA_SSR_PORT || 13714
server.listen(port, () => {
  console.log(`Inertia SSR server started on port ${port}.`)
})
