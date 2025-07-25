<script setup>

defineProps({
  // You can pass summary data here later
})
</script>

<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200 bg-white p-6">
          <h2 class="mb-4 text-2xl font-bold">
            Selamat Datang, {{ $page.props.auth.user.name }}!
          </h2>
          <p>Anda telah berhasil login ke Panel Admin CSIRT Bojonegoro.</p>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow">
          <h3 class="text-lg font-semibold">Insiden Dilaporkan</h3>
          <p class="mt-2 text-3xl font-bold">12</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
          <h3 class="text-lg font-semibold">Artikel Dipublikasi</h3>
          <p class="mt-2 text-3xl font-bold">5</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
          <h3 class="text-lg font-semibold">Layanan Aktif</h3>
          <p class="mt-2 text-3xl font-bold">3</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
