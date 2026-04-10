<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
  public function up(): void
  {
    $now = now();

    $areas = [
      [
        'name' => 'Tata Kelola Keamanan Informasi',
        'slug' => Str::slug('Tata Kelola Keamanan Informasi'),
        'description' => 'Kebijakan, struktur organisasi, peran, dan tanggung jawab dalam pengelolaan keamanan informasi di tingkat strategis.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Pengelolaan Risiko Keamanan Informasi',
        'slug' => Str::slug('Pengelolaan Risiko Keamanan Informasi'),
        'description' => 'Proses identifikasi, analisis, evaluasi, dan penanganan risiko yang berkaitan dengan aset dan sistem informasi.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Kerangka Kerja Keamanan Informasi',
        'slug' => Str::slug('Kerangka Kerja Keamanan Informasi'),
        'description' => 'Standar, pedoman, dan kontrol yang menjadi acuan implementasi keamanan informasi (seperti ISO 27001, NIST, dsb).',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Pengelolaan Aset Informasi',
        'slug' => Str::slug('Pengelolaan Aset Informasi'),
        'description' => 'Inventarisasi, klasifikasi, kepemilikan, dan perlindungan aset informasi organisasi.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Teknologi dan Keamanan Informasi',
        'slug' => Str::slug('Teknologi dan Keamanan Informasi'),
        'description' => 'Kontrol teknis, infrastruktur, sistem, dan solusi teknologi untuk melindungi keamanan informasi.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Pelindungan Data Pribadi (PDP)',
        'slug' => Str::slug('Pelindungan Data Pribadi PDP'),
        'description' => 'Kepatuhan terhadap regulasi perlindungan data pribadi, hak subjek data, dan penanganan data sensitif.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'name' => 'Suplemen',
        'slug' => Str::slug('Suplemen'),
        'description' => 'Dokumen pendukung, referensi tambahan, template, atau panduan pelengkap yang mendukung kategori utama di atas.',
        'created_at' => $now,
        'updated_at' => $now,
      ],
    ];

    DB::table('document_areas')->insertOrIgnore($areas);
  }

  public function down(): void
  {
    DB::table('document_areas')->whereIn('slug', [
      'tata-kelola-keamanan-informasi',
      'pengelolaan-risiko-keamanan-informasi',
      'kerangka-kerja-keamanan-informasi',
      'pengelolaan-aset-informasi',
      'teknologi-dan-keamanan-informasi',
      'pelindungan-data-pribadi-pdp',
      'suplemen',
    ])->delete();
  }
};
