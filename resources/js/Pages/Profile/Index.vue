<script setup>
</script>

<template>
  <AppLayout title="Profil">
    <div class="bg-gray-50 py-24 sm:py-32">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0">
          <h2
            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
          >
            Profil CSIRT Bojonegoro
          </h2>
          <p class="mt-6 text-lg leading-8 text-gray-600">
            Computer Security Incident Response Team (CSIRT) Pemerintah
            Kabupaten Bojonegoro adalah tim yang bertanggung jawab untuk
            menerima, meninjau, dan menanggapi laporan insiden keamanan siber di
            lingkungan Pemkab Bojonegoro.
          </p>
        </div>

        <!-- Visi & Misi -->
        <div
          class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-2"
        >
          <div class="max-w-xl">
            <h3 class="text-2xl font-bold tracking-tight text-gray-900">
              Visi
            </h3>
            <p class="mt-4 text-gray-600">
              "Terwujudnya keamanan siber yang andal dan terpercaya di
              lingkungan Pemerintah Kabupaten Bojonegoro untuk mendukung
              penyelenggaraan pemerintahan dan pelayanan publik yang efektif dan
              efisien."
            </p>
          </div>
          <div class="max-w-xl">
            <h3 class="text-2xl font-bold tracking-tight text-gray-900">
              Misi
            </h3>
            <ul class="mt-4 list-inside list-disc space-y-2 text-gray-600">
              <li>
                Membangun sistem deteksi dan pencegahan insiden keamanan siber
                yang proaktif.
              </li>
              <li>
                Melakukan penanganan insiden siber secara cepat, tepat, dan
                tuntas.
              </li>
              <li>
                Meningkatkan kesadaran dan kompetensi sumber daya manusia di
                bidang keamanan siber.
              </li>
              <li>
                Membangun kemitraan dan kolaborasi dengan berbagai pihak dalam
                penanganan insiden siber.
              </li>
            </ul>
          </div>
        </div>

        <!-- Struktur Tim (Dummy) -->
        <div class="mt-24">
          <h3
            class="text-center text-2xl font-bold tracking-tight text-gray-900"
          >
            Struktur Tim
          </h3>
          <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div
                class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
              >
                <table class="min-w-full divide-y divide-gray-300">
                  <thead>
                    <tr>
                      <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                      >
                        Nama
                      </th>
                      <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                      >
                        Jabatan
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                      >
                        Dr. H. Ahmad Subagyo, S.Kom., M.T.
                      </td>
                      <td
                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                      >
                        Pembina / Kepala Diskominfo
                      </td>
                    </tr>
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                      >
                        Budi Santoso, S.T.
                      </td>
                      <td
                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                      >
                        Ketua Tim / Kepala Bidang E-Gov
                      </td>
                    </tr>
                    <tr>
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                      >
                        Siti Aminah, A.Md.Kom.
                      </td>
                      <td
                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                      >
                        Anggota Tim Teknis
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
