<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
  public function run(): void
  {
    Document::create([
      'title' => 'RFC 2350 - CSIRT Bojonegoro',
      'slug' => Str::slug('RFC 2350 - CSIRT Bojonegoro'),
      'description' => 'Dokumen deskripsi CSIRT Bojonegoro sesuai standar RFC 2350. Berisi informasi tentang mandat, layanan, dan cara kontak tim.',
      'file_path' => 'documents/rfc2350-csirt-v1.pdf',
      'version' => '1.0',
      'published_at' => now(),
    ]);
  }
}
