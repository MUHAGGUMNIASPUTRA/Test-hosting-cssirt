<script setup>
defineEmits(['select'])
</script>

<template>
  <div class="p-6 sm:p-8">
    <div class="mb-6 text-center sm:mb-8">
      <h2 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">
        Pilih Layanan
      </h2>
      <p class="text-slate-600">Apa yang ingin Anda lakukan hari ini?</p>
    </div>

    <div class="mx-auto grid max-w-4xl grid-cols-1 gap-6 md:grid-cols-2">
      <!-- Create New Ticket -->
      <div
        @click="$emit('select', 'create')"
        class="group relative cursor-pointer"
      >
        <div
          class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 transition-all duration-300 hover:border-blue-400 hover:shadow-lg group-hover:-translate-y-1 sm:p-8"
        >
          <div class="text-center">
            <div
              class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 transition-colors group-hover:bg-blue-200 sm:h-16 sm:w-16"
            >
              <IconPlus size="18" stroke-width="1.5" class="text-blue-600" />
            </div>
            <h3 class="mb-1 text-xl font-bold text-slate-900">
              Buat Tiket Baru
            </h3>
            <p class="text-slate-600">
              Laporkan insiden keamanan siber yang baru terjadi
            </p>
          </div>
          <div
            class="absolute inset-0 rounded-xl bg-blue-600 opacity-0 transition-opacity group-hover:opacity-5"
          ></div>
        </div>
      </div>

      <!-- Check Existing Ticket -->
      <div
        @click="$emit('select', 'search')"
        class="group relative cursor-pointer"
      >
        <div
          class="rounded-xl border-2 border-green-200 bg-gradient-to-br from-green-50 to-emerald-50 p-6 transition-all duration-300 hover:border-green-400 hover:shadow-lg group-hover:-translate-y-1 sm:p-8"
        >
          <div class="text-center">
            <div
              class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 transition-colors group-hover:bg-green-200 sm:h-16 sm:w-16"
            >
              <IconSearch size="18" stroke-width="1.5" class="text-green-600" />
            </div>
            <h3 class="mb-1 text-xl font-bold text-slate-900">
              Cek Status Tiket
            </h3>
            <p class="text-slate-600">
              Lacak perkembangan tiket yang sudah dibuat
            </p>
          </div>
          <div
            class="absolute inset-0 rounded-xl bg-green-600 opacity-0 transition-opacity group-hover:opacity-5"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>
