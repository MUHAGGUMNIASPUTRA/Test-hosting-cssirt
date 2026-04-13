<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clean the table before seeding to avoid duplicates on re-seed
        DB::table('announcements')->truncate();

        $announcements = [
            [
                'title' => 'Waspada! Peningkatan Serangan Phishing Mengatasnamakan Admin TI',
                'content' => 'Tim CSIRT mendeteksi adanya peningkatan upaya phishing melalui email yang mengaku sebagai Admin TI dan meminta Anda untuk melakukan verifikasi akun. **Harap jangan klik tautan apapun atau memberikan kredensial Anda.** Email resmi hanya akan dikirim dari domain @bojonegorokab.go.id. Laporkan setiap email mencurigakan.',
                'level' => 'critical',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(7),
                'is_active' => true,
            ],
            [
                'title' => 'Pemberitahuan Maintenance Sistem e-Office',
                'content' => 'Akan dilakukan maintenance rutin pada sistem e-Office untuk peningkatan keamanan dan performa. Sistem diperkirakan akan nonaktif sementara.<br><br><strong>Jadwal:</strong> Sabtu, 3 Agustus 2025, pukul 00:00 - 04:00 WIB.<br><br>Mohon untuk menyimpan semua pekerjaan Anda sebelum waktu tersebut. Terima kasih atas pengertiannya.',
                'level' => 'warning',
                'start_date' => now(),
                'end_date' => now()->addDays(5),
                'is_active' => true,
            ],
            [
                'title' => 'Tips Keamanan Selama Libur Panjang Idul Adha',
                'content' => 'Menjelang libur panjang, pastikan Anda tetap waspada terhadap potensi ancaman siber. Berikut beberapa tips:<br>1. Jangan gunakan WiFi publik untuk transaksi penting.<br>2. Waspada terhadap email atau pesan ucapan selamat yang berisi link mencurigakan.<br>3. Pastikan perangkat kerja Anda dalam keadaan terkunci (locked) atau mati (shutdown) saat ditinggal.',
                'level' => 'info',
                'start_date' => now()->subDays(10),
                'end_date' => now()->subDays(2), // Expired announcement
                'is_active' => false,
            ],
            [
                'title' => 'URGENT: Terdeteksi Ancaman Ransomware Baru',
                'content' => 'Sebuah varian ransomware baru terdeteksi aktif menyerang instansi pemerintah. Ciri-cirinya adalah file terenkripsi dengan ekstensi .LOCKED. **Segera putuskan koneksi jaringan dan matikan komputer Anda jika menemukan ciri-ciri tersebut.** Hubungi tim CSIRT secepatnya untuk penanganan lebih lanjut.',
                'level' => 'critical',
                'start_date' => now()->subHours(3),
                'end_date' => now()->addDays(10),
                'is_active' => true,
            ],
            [
                'title' => 'Kebijakan Penggantian Password Berkala',
                'content' => 'Untuk meningkatkan keamanan akun, kami mengingatkan kembali tentang kebijakan penggantian password secara berkala setiap 90 hari. Anda akan menerima notifikasi 7 hari sebelum masa berlaku password Anda habis. Pastikan password baru Anda memenuhi kriteria kompleksitas yang telah ditetapkan.',
                'level' => 'info',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'title' => 'Laporan Insiden Siber Bulan Juni 2025',
                'content' => 'Tim CSIRT telah merilis laporan bulanan mengenai insiden siber yang ditangani selama bulan Juni 2025. Laporan ini mencakup statistik jenis serangan, tren, dan rekomendasi keamanan. Dokumen dapat diakses di portal internal.',
                'level' => 'info',
                'start_date' => now()->subDays(25),
                'end_date' => now()->subDays(5), // Expired announcement
                'is_active' => false,
            ],
            [
                'title' => 'Peringatan Keamanan: Celah Keamanan Kritis pada Aplikasi X',
                'content' => 'Ditemukan celah keamanan (vulnerability) pada aplikasi X versi 1.2. Tim pengembang sedang mengerjakan patch. Untuk sementara, mohon untuk tidak menggunakan fitur Z pada aplikasi tersebut hingga ada pemberitahuan lebih lanjut.',
                'level' => 'warning',
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(4),
                'is_active' => true,
            ],
            [
                'title' => 'Pelatihan Kesadaran Keamanan Siber (Batch 3)',
                'content' => 'Pendaftaran untuk Pelatihan Kesadaran Keamanan Siber (Security Awareness) Batch 3 kini telah dibuka. Pelatihan ini wajib bagi seluruh ASN. Jadwal dan link pendaftaran dapat diakses melalui e-Office.',
                'level' => 'info',
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_active' => true,
            ],
            [
                'title' => 'Update Aplikasi Antivirus Wajib',
                'content' => 'Telah dirilis pembaruan definisi virus terbaru untuk melindungi dari ancaman malware terkini. Mohon pastikan aplikasi antivirus di komputer Anda telah ter-update ke versi terbaru. Proses update seharusnya berjalan otomatis, namun Anda bisa memicunya secara manual jika diperlukan.',
                'level' => 'warning',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(12),
                'is_active' => true,
            ],
            [
                'title' => 'Perbaruan Kontak Darurat Tim CSIRT',
                'content' => 'Telah terjadi perbaruan pada nomor kontak darurat tim CSIRT. Mohon simpan informasi kontak terbaru yang dapat diakses pada halaman utama portal ini untuk mempercepat pelaporan insiden.',
                'level' => 'info',
                'start_date' => now()->subDays(20),
                'end_date' => now()->addDays(10),
                'is_active' => true,
            ],
        ];

        // Insert data into the database
        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
