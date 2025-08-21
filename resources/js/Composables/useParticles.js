import { ref } from 'vue'

export function useParticles() {
  const particlesOptions = ref({
    preset: "links",
    background: { color: "transparent" },
    fullScreen: { enable: false, zIndex: 0 },
    particles: {
      number: { value: 100, density: { enable: true, area: 800 } },
      color: { value: ["#3B82F6", "#8B5CF6", "#10B981", "#F59E0B"] },
      shape: { type: "circle" },
      opacity: { value: 0.5, random: { enable: true, minimumValue: 0.1 } },
      size: { value: { min: 1, max: 3 }, random: { enable: true, minimumValue: 1 } },
      links: { enable: true, distance: 150, color: "#ffffff", opacity: 0.2, width: 1 },
      move: { enable: true, speed: 1, direction: "none", random: true, straight: false, outModes: { default: "out" } }
    },
    interactivity: {
      detectsOn: "window",
      events: {
        onHover: { enable: true, mode: "repulse" },
        onClick: { enable: true, mode: "push" },
        resize: true
      },
      modes: {
        repulse: { distance: 100, duration: 0.4 },
        push: { quantity: 2 }
      }
    },
    responsive: [
      {
        maxWidth: 768,
        options: { particles: { number: { value: 100, density: { enable: true, area: 600 } } } }
      },
      {
        maxWidth: 1024,
        options: {particles: { number: { value: 100, density: { enable: true, area: 600 } } } }
      }
    ],
    detectRetina: true
  })

  // Optional: Create different preset configurations
  const createCustomOptions = (overrides = {}) => {
    return ref({
      ...particlesOptions.value,
      ...overrides
    })
  }

  // Preset for hero sections with more particles
  const heroParticlesOptions = createCustomOptions({
    particles: {
      ...particlesOptions.value.particles,
      number: {
        value: 100,
        density: {
          enable: true,
          area: 800
        }
      },
      opacity: {
        value: 0.4,
        random: {
          enable: true,
          minimumValue: 0.1
        }
      }
    }
  })

  // Preset for minimal particle effect
  const minimalParticlesOptions = createCustomOptions({
    particles: {
      ...particlesOptions.value.particles,
      number: {
        value: 50,
        density: {
          enable: true,
          area: 800
        }
      },
      links: {
        enable: false
      },
      opacity: {
        value: 0.2,
        random: {
          enable: true,
          minimumValue: 0.05
        }
      },
      move: {
        enable: true,
        speed: 1,
        direction: "none",
        random: true,
        straight: false,
        outModes: {
          default: "bounce"
        }
      }
    },
    interactivity: {
      ...particlesOptions.value.interactivity,
      events: {
        ...particlesOptions.value.interactivity.events,
        onHover: {
          enable: false
        }
      }
    }
  })

  return {
    particlesOptions,
    heroParticlesOptions,
    minimalParticlesOptions,
    createCustomOptions
  }
}
