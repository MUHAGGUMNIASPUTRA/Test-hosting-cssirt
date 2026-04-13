import { ref } from 'vue'

export function useParticles() {
  const particlesOptions = ref({
    preset: 'links',
    background: { color: 'transparent' },
    fullScreen: { enable: false, zIndex: 0 },
    particles: {
      number: { value: 100, density: { enable: true, area: 800 } },
      color: { value: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B'] },
      shape: { type: 'circle' },
      opacity: { value: 0.5, random: { enable: true, minimumValue: 0.1 } },
      size: {
        value: { min: 1, max: 3 },
        random: { enable: true, minimumValue: 1 },
      },
      links: {
        enable: true,
        distance: 150,
        color: '#ffffff',
        opacity: 0.2,
        width: 1,
      },
      move: {
        enable: true,
        speed: 1,
        direction: 'none',
        random: true,
        straight: false,
        outModes: { default: 'out' },
      },
    },
    interactivity: {
      detectsOn: 'window',
      events: {
        onHover: { enable: true, mode: 'attract' },
        onClick: { enable: true, mode: 'push' },
        resize: true,
      },
      modes: {
        push: { quantity: 2 },
      },
    },
    responsive: [
      {
        maxWidth: 768,
        options: {
          particles: {
            number: { value: 100, density: { enable: true, area: 600 } },
          },
        },
      },
      {
        maxWidth: 1024,
        options: {
          particles: {
            number: { value: 100, density: { enable: true, area: 600 } },
          },
        },
      },
    ],
    detectRetina: true,
  })

  // Optional: Create different preset configurations
  const createCustomOptions = (overrides = {}) => {
    return ref({
      ...particlesOptions.value,
      ...overrides,
    })
  }

  // Preset for hero sections with more particles
  const heroParticlesOptions = createCustomOptions({
    particles: {
      ...particlesOptions.value.particles,
      number: { value: 100, density: { enable: true, area: 800 } },
      opacity: { value: 0.4, random: { enable: true, minimumValue: 0.1 } },
    },
  })

  // Preset for login page with security theme
  const loginParticlesOptions = ref({
    background: { color: 'transparent' },
    fullScreen: { enable: false, zIndex: 0 },
    particles: {
      number: { value: 40, density: { enable: true, area: 800 } },
      color: { value: ['#3B82F6', '#1E40AF', '#1D4ED8', '#2563EB', '#60A5FA'] },
      shape: {
        type: ['circle', 'triangle', 'polygon'],
        options: { polygon: { sides: 6 } },
      },
      opacity: {
        value: 0.2,
        random: { enable: true, minimumValue: 0.05 },
        animation: {
          enable: true,
          speed: 0.8,
          minimumValue: 0.05,
          sync: false,
        },
      },
      size: {
        value: { min: 2, max: 5 },
        random: { enable: true, minimumValue: 1 },
        animation: { enable: true, speed: 1.5, minimumValue: 0.5, sync: false },
      },
      links: {
        enable: true,
        distance: 120,
        color: '#3B82F6',
        opacity: 0.15,
        width: 1,
        triangles: { enable: true, color: '#60A5FA', opacity: 0.05 },
      },
      move: {
        enable: true,
        speed: 1.2,
        direction: 'none',
        random: true,
        straight: false,
        outModes: { default: 'bounce' },
        attract: { enable: true, rotateX: 600, rotateY: 1200 },
      },
    },
    interactivity: {
      detectsOn: 'window',
      events: {
        onHover: { enable: true, mode: ['grab', 'bubble'] },
        onClick: { enable: true, mode: 'push' },
        resize: true,
      },
      modes: {
        grab: { distance: 150, links: { opacity: 0.3 } },
        bubble: { distance: 200, size: 8, duration: 2, opacity: 0.4, speed: 3 },
        push: { quantity: 3 },
      },
    },
    responsive: [
      {
        maxWidth: 768,
        options: {
          particles: {
            number: { value: 40, density: { enable: true, area: 600 } },
            links: { distance: 80, opacity: 0.1 },
            move: { speed: 0.8 },
          },
          interactivity: {
            modes: {
              grab: { distance: 100 },
              bubble: { distance: 120, size: 6 },
            },
          },
        },
      },
    ],
    detectRetina: true,
  })

  // Preset for minimal particle effect
  const minimalParticlesOptions = createCustomOptions({
    particles: {
      ...particlesOptions.value.particles,
      number: { value: 50, density: { enable: true, area: 800 } },
      links: { enable: false },
      opacity: { value: 0.2, random: { enable: true, minimumValue: 0.05 } },
      move: {
        enable: true,
        speed: 1,
        direction: 'none',
        random: true,
        straight: false,
        outModes: { default: 'bounce' },
      },
    },
    interactivity: {
      ...particlesOptions.value.interactivity,
      events: {
        ...particlesOptions.value.interactivity.events,
        onHover: { enable: true, mode: 'repulse' },
      },
      modes: {
        ...particlesOptions.value.interactivity.modes,
        repulse: { distance: 50, duration: 0.4 },
      },
    },
  })

  return {
    particlesOptions,
    heroParticlesOptions,
    minimalParticlesOptions,
    loginParticlesOptions,
    createCustomOptions,
  }
}
