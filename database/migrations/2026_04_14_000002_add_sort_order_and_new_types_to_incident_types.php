<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_types', function (Blueprint $table) {
            $table->integer('sort_order')->default(99)->after('guide');
        });

        // Kosongkan tabel dan isi ulang dengan data lengkap
        DB::table('incident_types')->truncate();

        DB::table('incident_types')->insert([
            [
                'id' => '33333333-0000-0000-0000-000000000001',
                'name' => 'Belum Mengetahui',
                'slug' => 'belum-mengetahui',
                'sort_order' => 1,
                'description' => 'Pelapor belum dapat mengidentifikasi jenis insiden yang terjadi. Pilih kategori ini jika Anda tidak yakin dengan jenis ancaman yang sedang dihadapi.',
                'guide' => '<h3>Panduan Pelaporan Insiden yang Belum Diketahui Jenisnya</h3>
<p>Tidak apa-apa jika Anda belum tahu jenis insiden yang terjadi. Yang terpenting, laporkan secepatnya. Sertakan informasi berikut:</p>
<ul>
  <li><strong>Gejala yang dialami:</strong> Apa yang tidak berjalan normal? (sistem lambat, file hilang, muncul pesan aneh, dll.)</li>
  <li><strong>Kapan pertama kali muncul:</strong> Waktu dan tanggal kejadian pertama kali diketahui</li>
  <li><strong>Perangkat/sistem terdampak:</strong> Komputer, server, aplikasi, atau jaringan apa yang bermasalah</li>
  <li><strong>Perubahan terakhir:</strong> Apakah ada software yang baru diinstal, email yang diklik, atau akses ke website baru?</li>
  <li><strong>Screenshot/foto:</strong> Tangkap layar kondisi yang mencurigakan sebagai bukti</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Jangan matikan perangkat jika ada data penting yang mungkin terdampak</li>
  <li>Cabut dari jaringan (cabut kabel LAN atau matikan WiFi) jika dirasa sudah terinfeksi</li>
  <li>Segera hubungi tim CSIRT dengan informasi sebanyak mungkin</li>
  <li>Jangan hapus file atau log apapun sebelum investigasi dilakukan</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000002',
                'name' => 'Bug Hunter',
                'slug' => 'bug-hunter',
                'sort_order' => 2,
                'description' => 'Pelaporan temuan celah keamanan (vulnerability) pada sistem atau aplikasi milik Pemerintah Kabupaten Bojonegoro oleh peneliti keamanan atau bug hunter.',
                'guide' => '<h3>Panduan Pelaporan Temuan Celah Keamanan (Bug Bounty)</h3>
<p>Terima kasih atas kontribusi Anda dalam menjaga keamanan sistem kami. Untuk mempercepat penanganan, sertakan informasi berikut:</p>
<ul>
  <li><strong>URL/endpoint yang rentan:</strong> Alamat lengkap halaman atau API yang memiliki celah</li>
  <li><strong>Jenis kerentanan:</strong> XSS, SQL Injection, IDOR, RCE, CSRF, dll.</li>
  <li><strong>Langkah reproduksi (PoC):</strong> Cara langkah demi langkah untuk mereproduksi celah</li>
  <li><strong>Dampak potensial:</strong> Apa yang bisa dilakukan penyerang jika memanfaatkan celah ini</li>
  <li><strong>Screenshot/video:</strong> Bukti visual celah keamanan</li>
  <li><strong>Tools yang digunakan:</strong> Burp Suite, OWASP ZAP, atau tools lainnya</li>
</ul>
<p><strong>Ketentuan pelaporan:</strong></p>
<ol>
  <li>Laporkan segera setelah ditemukan — jangan dieksploitasi lebih lanjut</li>
  <li>Jangan akses, modifikasi, atau hapus data milik orang lain</li>
  <li>Jangan melakukan serangan DoS atau merusak layanan</li>
  <li>Berikan waktu yang cukup (minimal 90 hari) sebelum mengungkap ke publik</li>
  <li>Temuan yang valid dan bertanggung jawab akan mendapat pengakuan resmi</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000003',
                'name' => 'Phishing',
                'slug' => 'phishing',
                'sort_order' => 3,
                'description' => 'Upaya penipuan untuk mencuri informasi sensitif seperti username, password, atau data keuangan dengan menyamar sebagai entitas terpercaya melalui email, SMS, atau situs web palsu.',
                'guide' => '<h3>Panduan Pelaporan Insiden Phishing</h3>
<p>Saat melaporkan insiden phishing, harap sertakan informasi berikut:</p>
<ul>
  <li><strong>Sumber phishing:</strong> Email, SMS, WhatsApp, atau situs web palsu</li>
  <li><strong>URL/tautan mencurigakan:</strong> Salin tautan (jangan klik) dan sertakan dalam laporan</li>
  <li><strong>Pengirim/nomor pengirim:</strong> Alamat email atau nomor telepon yang digunakan</li>
  <li><strong>Isi pesan:</strong> Screenshot atau salinan teks pesan</li>
  <li><strong>Apakah ada data yang sudah bocor:</strong> Informasi apa yang mungkin sudah diberikan</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Jangan klik tautan atau unduh lampiran mencurigakan</li>
  <li>Segera ganti password akun yang mungkin telah dikompromi</li>
  <li>Laporkan email phishing ke admin IT instansi Anda</li>
  <li>Aktifkan autentikasi dua faktor (2FA) jika belum aktif</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000004',
                'name' => 'Malware',
                'slug' => 'malware',
                'sort_order' => 4,
                'description' => 'Infeksi perangkat lunak berbahaya seperti virus, worm, trojan, ransomware, atau spyware yang dapat merusak sistem, mencuri data, atau mengenkripsi file.',
                'guide' => '<h3>Panduan Pelaporan Insiden Malware</h3>
<p>Untuk membantu penanganan lebih cepat, sertakan:</p>
<ul>
  <li><strong>Jenis malware (jika diketahui):</strong> Ransomware, virus, worm, keylogger, dsb.</li>
  <li><strong>Gejala yang terdeteksi:</strong> File terenkripsi, sistem lambat, pesan tebusan, dsb.</li>
  <li><strong>Sumber infeksi yang diduga:</strong> Email, flashdisk, unduhan, dsb.</li>
  <li><strong>Sistem yang terdampak:</strong> Nama komputer, alamat IP, sistem operasi</li>
  <li><strong>Langkah yang sudah diambil:</strong> Isolasi jaringan, scan antivirus, dsb.</li>
  <li><strong>Screenshot/log:</strong> Pesan error, notifikasi antivirus, atau log sistem</li>
</ul>
<p><strong>Tindakan darurat:</strong></p>
<ol>
  <li>Segera isolasi perangkat yang terinfeksi dari jaringan</li>
  <li>Jangan matikan komputer (untuk ransomware, biarkan menyala untuk investigasi)</li>
  <li>Jangan bayar tebusan tanpa konsultasi dengan tim CSIRT</li>
  <li>Dokumentasikan semua bukti sebelum melakukan pembersihan</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000005',
                'name' => 'Defacement',
                'slug' => 'defacement',
                'sort_order' => 5,
                'description' => 'Perubahan tampilan halaman web secara tidak sah oleh peretas, biasanya mengganti konten asli dengan pesan dari penyerang.',
                'guide' => '<h3>Panduan Pelaporan Insiden Defacement</h3>
<p>Sertakan informasi berikut dalam laporan Anda:</p>
<ul>
  <li><strong>URL website yang terdampak:</strong> Alamat lengkap halaman yang diubah</li>
  <li><strong>Screenshot halaman:</strong> Tampilan sebelum dan sesudah defacement (jika ada)</li>
  <li><strong>Isi perubahan:</strong> Pesan, gambar, atau konten yang disisipkan penyerang</li>
  <li><strong>Waktu pertama diketahui:</strong> Kapan pertama kali perubahan ditemukan</li>
  <li><strong>Platform/CMS:</strong> WordPress, Joomla, custom, dsb.</li>
  <li><strong>Akses log server:</strong> Jika tersedia, sertakan log akses server web</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Ambil screenshot sebagai bukti sebelum melakukan pemulihan</li>
  <li>Nonaktifkan/offline-kan website sementara untuk mencegah penyebaran</li>
  <li>Jangan langsung melakukan pemulihan sebelum investigasi selesai</li>
  <li>Hubungi penyedia hosting atau tim IT untuk akses server</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000006',
                'name' => 'Serangan DDoS',
                'slug' => 'serangan-ddos',
                'sort_order' => 6,
                'description' => 'Serangan Distributed Denial of Service yang membuat layanan online tidak dapat diakses oleh pengguna dengan cara membanjiri server dengan trafik yang berlebihan.',
                'guide' => '<h3>Panduan Pelaporan Insiden Serangan DDoS</h3>
<p>Informasi yang diperlukan untuk penanganan:</p>
<ul>
  <li><strong>Layanan/URL yang diserang:</strong> Alamat lengkap layanan yang tidak dapat diakses</li>
  <li><strong>Waktu mulai serangan:</strong> Kapan layanan mulai tidak bisa diakses</li>
  <li><strong>Volume trafik:</strong> Jika ada monitoring, sertakan data trafik tidak wajar</li>
  <li><strong>Jenis serangan (jika diketahui):</strong> HTTP Flood, UDP Flood, SYN Flood, dsb.</li>
  <li><strong>IP sumber (jika teridentifikasi):</strong> Alamat IP yang mengirim trafik berlebihan</li>
  <li><strong>Dampak:</strong> Berapa banyak pengguna/layanan yang terdampak</li>
  <li><strong>Log server:</strong> Access log, error log, atau firewall log</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Aktifkan rate limiting atau firewall rules jika tersedia</li>
  <li>Hubungi penyedia layanan internet (ISP) atau hosting untuk mitigasi</li>
  <li>Pertimbangkan pengalihan trafik ke layanan anti-DDoS</li>
  <li>Dokumentasikan semua log selama serangan berlangsung</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '33333333-0000-0000-0000-000000000007',
                'name' => 'Kebocoran Data',
                'slug' => 'kebocoran-data',
                'sort_order' => 7,
                'description' => 'Terungkapnya data rahasia atau pribadi ke pihak yang tidak berwenang, baik melalui serangan siber, kelalaian internal, maupun kesalahan konfigurasi sistem.',
                'guide' => '<h3>Panduan Pelaporan Insiden Kebocoran Data</h3>
<p>Detail yang perlu disertakan dalam laporan:</p>
<ul>
  <li><strong>Jenis data yang bocor:</strong> Data pribadi, data keuangan, data kesehatan, dsb.</li>
  <li><strong>Jumlah data yang terdampak:</strong> Perkiraan jumlah record atau individu yang terdampak</li>
  <li><strong>Sumber kebocoran:</strong> Database, dokumen, email, cloud storage, dsb.</li>
  <li><strong>Cara kebocoran terdeteksi:</strong> Audit internal, laporan pihak ketiga, temuan di forum, dsb.</li>
  <li><strong>Apakah data sudah tersebar:</strong> Di mana data ditemukan/disebarkan</li>
  <li><strong>Periode eksposur:</strong> Berapa lama data mungkin sudah terekspos</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Segera tutup celah keamanan yang menyebabkan kebocoran</li>
  <li>Nonaktifkan akun yang diduga menjadi sumber kebocoran</li>
  <li>Dokumentasikan semua bukti kebocoran</li>
  <li>Pertimbangkan notifikasi kepada individu yang datanya bocor sesuai regulasi</li>
  <li>Laporkan ke DPO (Data Protection Officer) instansi jika ada</li>
</ol>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('incident_types')->truncate();

        Schema::table('incident_types', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
