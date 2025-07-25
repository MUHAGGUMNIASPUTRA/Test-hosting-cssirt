<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
  public function run(): void
  {
    Faq::create([
      'question' => 'Siapa saja yang bisa melaporkan insiden ke CSIRT Bojonegoro?',
      'answer' => 'Layanan utama kami ditujukan untuk seluruh Organisasi Perangkat Daerah (OPD) di lingkungan Pemerintah Kabupaten Bojonegoro. Namun, kami juga menerima laporan dari masyarakat umum jika insiden tersebut berpotensi berdampak luas pada sistem Pemkab.',
      'category' => 'Pelaporan',
    ]);
    Faq::create([
      'question' => 'Informasi apa yang harus saya siapkan saat melapor?',
      'answer' => 'Sediakan informasi sedetail mungkin, termasuk: kronologi kejadian, waktu kejadian, bukti-bukti seperti screenshot atau pesan error, dan dampak yang ditimbulkan. Semakin lengkap informasinya, semakin cepat kami dapat melakukan penanganan.',
      'category' => 'Pelaporan',
    ]);
  }
}
