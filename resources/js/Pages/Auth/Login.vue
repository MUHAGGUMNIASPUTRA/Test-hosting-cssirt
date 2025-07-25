<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Log In" />

  <div
    class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8"
  >
    <div class="w-full max-w-md space-y-8">
      <div>
        <img
          class="mx-auto h-12 w-auto"
          src="/logo-bojonegoro.png"
          alt="Logo Bojonegoro"
        />
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Login ke Panel Admin
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Selamat datang kembali, Tim CSIRT!
        </p>
      </div>

      <div
        v-if="form.hasErrors"
        class="relative rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700"
        role="alert"
      >
        <strong class="font-bold">Oops!</strong>
        <span class="block sm:inline">{{
          form.errors.email || form.errors.password
        }}</span>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="submit">
        <div class="-space-y-px rounded-md shadow-sm">
          <div class="p-field mb-4">
            <label for="email" class="sr-only">Email</label>
            <InputText
              id="email"
              v-model="form.email"
              type="email"
              placeholder="Alamat Email"
              class="w-full"
              required
              autofocus
            />
          </div>
          <div class="p-field">
            <label for="password" class="sr-only">Password</label>
            <Password
              id="password"
              v-model="form.password"
              placeholder="Password"
              class="w-full"
              inputClass="w-full"
              :feedback="false"
              toggleMask
              required
            />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <Checkbox id="remember" v-model="form.remember" :binary="true" />
            <label for="remember" class="ml-2 block text-sm text-gray-900">
              Ingat saya
            </label>
          </div>
        </div>

        <div>
          <Button
            type="submit"
            label="Log In"
            class="w-full"
            :loading="form.processing"
          />
        </div>
      </form>
    </div>
  </div>
</template>
