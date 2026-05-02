<script setup>
// filepath: resources/js/Pages/Auth/Login.vue

import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useParticles } from '@/Composables/useParticles'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const { loginParticlesOptions } = useParticles()
const showPassword = ref(false)

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}

const togglePassword = () => {
  showPassword.value = !showPassword.value
}
</script>

<template>
  <Head title="Login" />

  <!-- Main Container with Background -->
  <div
    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-4"
  >
    <!-- Particles Background -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <vue-particles
        id="loginParticles"
        :options="loginParticlesOptions"
        class="h-full w-full"
      />
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">
      <!-- Header Section -->
      <div class="mb-8 text-center">
        <!-- Logo Container -->
        <div
          class="mx-auto mb-6 flex h-20 w-20 transform items-center justify-center rounded-2xl bg-white shadow-xl transition-transform duration-300 hover:scale-105"
        >
          <img
            src="/logo-bojonegoro.png"
            alt="Logo CSIRT Bojonegoro"
            class="h-12 w-12 object-contain"
          />
        </div>

        <!-- Title -->
        <h1 class="mb-2 text-3xl font-bold text-white">Selamat Datang</h1>
        <p class="text-lg text-blue-200">Admin Panel CSIRT Bojonegoro</p>
        <div
          class="mx-auto mt-4 h-1 w-20 rounded-full bg-gradient-to-r from-blue-400 to-emerald-400"
        ></div>
      </div>

      <!-- Login Form Card -->
      <div
        class="rounded-2xl border border-white/20 bg-white/95 p-8 shadow-2xl backdrop-blur-sm"
      >
        <!-- Error Messages -->
        <div
          v-if="form.hasErrors"
          class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4"
        >
          <div class="flex items-start">
            <svg
              class="mr-3 h-5 w-5 flex-shrink-0 text-red-400"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
            <div>
              <p class="text-sm text-red-700">
                Email atau password tidak valid.
              </p>
            </div>
          </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Email Field -->
          <div>
            <label
              for="email"
              class="mb-2 block text-sm font-semibold text-slate-700"
            >
              Email
            </label>
            <div class="relative">
              <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
              >
                <svg
                  class="h-5 w-5 text-slate-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                  />
                </svg>
              </div>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                autofocus
                class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-4 transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                :class="{
                  'border-red-300 focus:border-red-500 focus:ring-red-500':
                    form.errors.email,
                }"
                placeholder="Masukkan password"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label
              for="password"
              class="mb-2 block text-sm font-semibold text-slate-700"
            >
              Password
            </label>
            <div class="relative">
              <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
              >
                <svg
                  class="h-5 w-5 text-slate-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="current-password"
                class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-12 transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                :class="{
                  'border-red-300 focus:border-red-500 focus:ring-red-500':
                    form.errors.password,
                }"
                placeholder="Masukkan password"
              />
              <button
                type="button"
                @click="togglePassword"
                class="absolute inset-y-0 right-0 flex items-center pr-4"
              >
                <svg
                  v-if="showPassword"
                  class="h-5 w-5 text-slate-400 transition-colors hover:text-slate-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"
                  />
                </svg>
                <svg
                  v-else
                  class="h-5 w-5 text-slate-400 transition-colors hover:text-slate-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 transition-colors focus:ring-indigo-500"
              />
              <label for="remember" class="ml-2 block text-sm text-slate-700">
                Ingat saya
              </label>
            </div>
            <div class="text-sm">
              <a
                href="#"
                class="font-medium text-indigo-600 transition-colors hover:text-indigo-500"
              >
                Lupa password?
              </a>
            </div>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="flex w-full transform items-center justify-center rounded-xl border border-transparent bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-[1.02] hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
          >
            <svg
              v-if="form.processing"
              class="-ml-1 mr-3 h-5 w-5 animate-spin text-white"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <svg
              v-else
              class="mr-2 h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
              />
            </svg>
            {{ form.processing ? 'Memproses...' : 'Masuk ke Dashboard' }}
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 border-t border-slate-200 pt-6">
          <div class="text-center">
            <p class="text-xs text-slate-500">
              © {{ new Date().getFullYear() }} CSIRT Kabupaten Bojonegoro
            </p>
            <p class="mt-1 text-xs text-slate-500">
              Dinas Komunikasi dan Informatika
            </p>
          </div>
        </div>
      </div>

      <!-- Security Note -->
      <div class="mt-6 text-center">
        <div
          class="inline-flex items-center rounded-lg border border-blue-400/20 bg-blue-900/30 px-4 py-2 backdrop-blur-sm"
        >
          <svg
            class="mr-2 h-4 w-4 text-blue-300"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-sm text-blue-200"
            >Koneksi diamankan dengan SSL</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations */
@keyframes float {
  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-10px) rotate(1deg);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Focus styles for better accessibility */
input:focus {
  outline: none;
}

/* Custom checkbox styles */
input[type='checkbox']:checked {
  background-color: #4f46e5;
  border-color: #4f46e5;
}
</style>
