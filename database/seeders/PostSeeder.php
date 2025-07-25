<?php
// database/seeders/PostSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get all category and tag IDs
    $categoryIds = Category::pluck('id');
    $tagIds = Tag::pluck('id');

    $postsData = [
      [
        'title' => 'Waspada! Kenali 5 Ciri-Ciri Email Phishing',
        'excerpt' => 'Phishing adalah salah satu ancaman siber paling umum. Pelajari cara mengenali email palsu agar data pribadi dan kredensial Anda tetap aman.',
        'body' => '<h3>Apa itu Phishing?</h3><p>Phishing adalah upaya untuk mendapatkan informasi data seseorang dengan teknik pengelabuan. Data yang menjadi sasaran phising adalah data pribadi (nama, usia, alamat), data akun (username dan password), dan data finansial (informasi kartu kredit, rekening).</p><h4>Berikut adalah 5 ciri utamanya:</h4><ul><li>Permintaan data sensitif yang mendesak.</li><li>Alamat email dan domain pengirim yang mencurigakan.</li><li>Terdapat banyak kesalahan tata bahasa dan ejaan.</li><li>Link atau tautan yang aneh.</li><li>Lampiran file yang tidak terduga.</li></ul><p>Selalu verifikasi pengirim sebelum mengklik link atau mengunduh lampiran apapun.</p>',
      ],
      [
        'title' => 'Pentingnya Kata Sandi Kuat dan Cara Membuatnya',
        'excerpt' => 'Kata sandi adalah kunci utama untuk melindungi akun digital Anda. Pastikan Anda membuat kata sandi yang kuat dan tidak mudah ditebak.',
        'body' => '<h3>Mengapa Kata Sandi Kuat Penting?</h3><p>Kata sandi yang lemah seperti "123456" atau "password" sangat mudah ditebak oleh peretas hanya dalam hitungan detik. Kata sandi yang kuat memberikan lapisan pertahanan pertama yang krusial.</p><h4>Tips Membuat Kata Sandi Kuat:</h4><ul><li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</li><li>Buat kata sandi dengan panjang minimal 12 karakter.</li><li>Hindari menggunakan informasi pribadi seperti tanggal lahir atau nama hewan peliharaan.</li><li>Gunakan frasa unik yang mudah Anda ingat, tapi sulit ditebak orang lain.</li><li>Aktifkan Autentikasi Dua Faktor (2FA) jika tersedia.</li></ul>',
      ],
      [
        'title' => 'Apa Itu Ransomware dan Bagaimana Cara Mencegahnya?',
        'excerpt' => 'Ransomware dapat mengenkripsi semua data penting Anda dan meminta tebusan. Pencegahan adalah kunci untuk melawan ancaman destruktif ini.',
        'body' => '<h3>Memahami Ransomware</h3><p>Ransomware adalah jenis perangkat lunak jahat (malware) yang "menyandera" data korban dengan cara mengenkripsinya. Untuk mendapatkan kunci dekripsi, korban diharuskan membayar sejumlah uang tebusan.</p><h4>Langkah-langkah Pencegahan:</h4><ul><li><strong>Backup Data Secara Teratur:</strong> Simpan salinan data penting Anda di lokasi terpisah (offline atau cloud).</li><li><strong>Jangan Klik Link Sembarangan:</strong> Hindari membuka lampiran atau mengklik tautan dari email yang tidak dikenal.</li><li><strong>Perbarui Perangkat Lunak:</strong> Selalu pastikan sistem operasi dan aplikasi Anda berada di versi terbaru untuk menambal celah keamanan.</li><li><strong>Gunakan Antivirus Terpercaya:</strong> Pasang dan aktifkan perangkat lunak antivirus yang memiliki perlindungan terhadap ransomware.</li></ul>',
      ],
      [
        'title' => 'Amankan Akun Media Sosial Anda: Panduan Praktis',
        'excerpt' => 'Media sosial adalah bagian dari kehidupan sehari-hari, namun sering menjadi target peretasan. Ikuti panduan ini untuk mengamankan akun Anda.',
        'body' => '<h3>Langkah-langkah Pengamanan Akun Media Sosial</h3><p>Akun media sosial berisi banyak informasi pribadi yang berharga. Mengamankannya adalah sebuah keharusan.</p><h4>Berikut adalah tips utamanya:</h4><ul><li><strong>Gunakan Kata Sandi Unik:</strong> Jangan gunakan kata sandi yang sama untuk semua akun media sosial Anda.</li><li><strong>Aktifkan Autentikasi Dua Faktor (2FA):</strong> Ini adalah lapisan keamanan tambahan yang paling efektif.</li><li><strong>Periksa Izin Aplikasi:</strong> Hapus akses dari aplikasi pihak ketiga yang tidak Anda kenali atau tidak digunakan lagi.</li><li><strong>Hati-hati dengan Informasi yang Dibagikan:</strong> Hindari memposting informasi yang terlalu pribadi seperti alamat rumah atau nomor telepon.</li></ul>',
      ],
      [
        'title' => 'Mengenal Serangan Man-in-the-Middle (MitM)',
        'excerpt' => 'Serangan Man-in-the-Middle (MitM) adalah saat peretas secara diam-diam menyadap komunikasi antara dua pihak. Pelajari cara kerjanya dan bagaimana menghindarinya.',
        'body' => '<h3>Bagaimana Serangan MitM Bekerja?</h3><p>Peretas menempatkan dirinya di antara Anda dan titik koneksi (misalnya, Wi-Fi publik). Saat Anda terhubung, semua lalu lintas internet Anda akan melewati peretas, memungkinkan mereka untuk mencuri informasi sensitif seperti kata sandi dan data kartu kredit.</p><h4>Cara Menghindari Serangan MitM:</h4><ul><li><strong>Hindari Wi-Fi Publik yang Tidak Aman:</strong> Jangan melakukan transaksi perbankan atau memasukkan kata sandi saat terhubung ke Wi-Fi publik tanpa proteksi.</li><li><strong>Gunakan VPN (Virtual Private Network):</strong> VPN akan mengenkripsi lalu lintas internet Anda, membuatnya tidak dapat dibaca oleh peretas.</li><li><strong>Perhatikan Peringatan Keamanan Browser:</strong> Jangan abaikan peringatan "koneksi tidak aman" dari browser Anda.</li><li><strong>Pastikan Website Menggunakan HTTPS:</strong> Selalu periksa apakah ada ikon gembok di bilah alamat browser.</li></ul>',
      ],
    ];

    foreach ($postsData as $postData) {
      $post = Post::create([
        'title' => $postData['title'],
        'slug' => Str::slug($postData['title']),
        'image' => 'https://placehold.co/800x400/e2e8f0/4a5568?text=CSIRT+Bojonegoro',
        'excerpt' => $postData['excerpt'],
        'body' => $postData['body'],
        'status' => 'Published',
        'views_count' => rand(100, 2500),
        'published_at' => now()->subDays(rand(1, 30)),
        'published_by' => 'Admin CSIRT',
        'rating' => rand(35, 50) / 10,
      ]);

      // Attach random categories and tags
      $post->categories()->attach($categoryIds->random(rand(1, 2)));
      $post->tags()->attach($tagIds->random(rand(2, 4)));
    }
  }
}
