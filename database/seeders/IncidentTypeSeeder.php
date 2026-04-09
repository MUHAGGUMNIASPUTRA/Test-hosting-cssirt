<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentType;
use Illuminate\Support\Str;

class IncidentTypeSeeder extends Seeder
{
  public function run(): void
  {
    $types = [
      [
        'name' => 'Phishing',
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
      ],
      [
        'name' => 'Malware',
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
      ],
      [
        'name' => 'Defacement',
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
      ],
      [
        'name' => 'Serangan DDoS',
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
      ],
      [
        'name' => 'Kebocoran Data',
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
      ],
    ];

    foreach ($types as $type) {
      IncidentType::updateOrCreate(
        ['slug' => Str::slug($type['name'])],
        [
          'name' => $type['name'],
          'description' => $type['description'],
          'guide' => $type['guide'],
        ]
      );
    }
  }
}
