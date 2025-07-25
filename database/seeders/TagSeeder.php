<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
  public function run(): void
  {
    $tags = ['Phishing', 'Ransomware', 'Kata Sandi', 'Keamanan Jaringan', 'Aplikasi Mobile', 'Pemerintahan'];
    foreach ($tags as $tag) {
      Tag::create([
        'name' => $tag,
        'slug' => Str::slug($tag),
      ]);
    }
  }
}
