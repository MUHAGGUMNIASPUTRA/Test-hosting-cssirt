// DOM polyfills for SSR environment
export function setupSSRPolyfills() {
  if (typeof global !== 'undefined' && typeof window === 'undefined') {
    const createElementMock = (tagName = 'div') => ({
      tagName: tagName.toUpperCase(),
      children: [],
      childNodes: [],
      style: {},
      classList: {
        add: () => {},
        remove: () => {},
        contains: () => false,
        toggle: () => {},
        replace: () => {},
      },
      setAttribute: () => {},
      getAttribute: () => null,
      hasAttribute: () => false,
      removeAttribute: () => {},
      appendChild: function (child) {
        this.children.push(child)
        child.parentNode = this
        return child
      },
      removeChild: function (child) {
        const index = this.children.indexOf(child)
        if (index > -1) {
          this.children.splice(index, 1)
          child.parentNode = null
        }
        return child
      },
      addEventListener: () => {},
      removeEventListener: () => {},
      querySelector: () => null,
      querySelectorAll: () => [],
      getElementsByTagName: () => [],
      getElementsByClassName: () => [],
      getElementById: () => null,
      innerHTML: '',
      textContent: '',
      parentNode: null,
      dataset: {},
      offsetWidth: 0,
      offsetHeight: 0,
      clientWidth: 0,
      clientHeight: 0,
      scrollWidth: 0,
      scrollHeight: 0,
      getBoundingClientRect: () => ({
        top: 0,
        left: 0,
        bottom: 0,
        right: 0,
        width: 0,
        height: 0,
      }),
    })

    global.window = {
      document: {
        head: createElementMock('head'),
        body: createElementMock('body'),
        documentElement: createElementMock('html'),
        createElement: createElementMock,
        getElementById: () => null,
        querySelector: () => null,
        querySelectorAll: () => [],
        getElementsByTagName: () => [],
        getElementsByClassName: () => [],
        addEventListener: () => {},
        removeEventListener: () => {},
        createTextNode: (text) => ({ textContent: text, nodeType: 3 }),
        activeElement: createElementMock('body'),
      },
      location: {
        href: '',
        origin: '',
        pathname: '/',
        search: '',
        hash: '',
        host: '',
        hostname: '',
      },
      navigator: {
        userAgent: 'SSR',
        platform: 'SSR',
      },
      getComputedStyle: () => ({}),
      addEventListener: () => {},
      removeEventListener: () => {},
      matchMedia: () => ({
        matches: false,
        addEventListener: () => {},
        removeEventListener: () => {},
      }),
      console: console,
      setTimeout: setTimeout,
      clearTimeout: clearTimeout,
      setInterval: setInterval,
      clearInterval: clearInterval,
    }

    global.document = global.window.document
    global.navigator = global.window.navigator
    global.location = global.window.location
  }
}
