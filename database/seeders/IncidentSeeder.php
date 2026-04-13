<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        Incident::truncate();

        // Fetch a staff user to be assigned to incidents.
        // If no staff user is found, it will default to null.
        $staffUser = User::where('role', 'staff')->first();

        // Fetch all incident types to be used in the seeder.
        $incidentTypes = IncidentType::all();

        // Data array containing 20 detailed incident reports.
        $incidents = [
            // Batch 1
            [
                'reporter_name' => 'Agus Purnomo',
                'reporter_email' => 'agus.purnomo@dinkominfo.bojonegorokab.go.id',
                'reporter_phone' => '081234567801',
                'incident_type_slug' => 'phishing',
                'description' => 'Menerima email yang mengaku sebagai admin e-Office Pemkab Bojonegoro. Email tersebut menyatakan bahwa password saya akan segera kadaluarsa dan meminta untuk melakukan reset melalui link yang disediakan. Tampilan halaman login sangat mirip dengan aslinya, namun URL-nya mencurigakan (e-office-pemkab.xyz).',
                'incident_at' => now()->subHours(5),
                'status' => 'Dalam Penyelidikan',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Dewi Lestari',
                'reporter_email' => 'dewi.lestari@dinsos.bojonegorokab.go.id',
                'reporter_phone' => '081234567802',
                'incident_type_slug' => 'malware',
                'description' => 'Komputer di bagian pelayanan Dinas Sosial tiba-tiba tidak bisa membuka file Word dan Excel. Semua file memiliki ekstensi .locked dan muncul pesan di desktop yang meminta tebusan dalam bentuk Bitcoin untuk mengembalikan file. Diduga kuat terinfeksi ransomware.',
                'incident_at' => now()->subDays(2),
                'status' => 'Baru',
                'priority' => 'Kritikal',
                'assigned' => false,
            ],
            [
                'reporter_name' => 'Eko Prasetyo',
                'reporter_email' => 'eko.prasetyo@disbudpar.bojonegorokab.go.id',
                'reporter_phone' => '081234567803',
                'incident_type_slug' => 'defacement',
                'description' => 'Halaman utama subdomain pariwisata (pariwisata.bojonegorokab.go.id) tampilannya berubah total menjadi layar hitam dengan tulisan "Hacked by Bojonegoro Cyber Army". Tidak ada konten asli yang tersisa dan hanya menampilkan pesan dari peretas.',
                'incident_at' => now()->subDays(1),
                'status' => 'Diverifikasi',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Fitri Handayani',
                'reporter_email' => 'fitri.h@disdik.bojonegorokab.go.id',
                'reporter_phone' => '081234567804',
                'incident_type_slug' => 'serangan-ddos',
                'description' => 'Portal PPDB Online (ppdb.disdik.bojonegorokab.go.id) tidak dapat diakses sama sekali sejak pagi ini. Saat dicoba dibuka, browser hanya loading terus menerus (timeout). Banyak keluhan dari orang tua siswa yang tidak bisa mendaftar. Terindikasi serangan DDoS karena traffic yang sangat tinggi dan tidak wajar.',
                'incident_at' => now()->subHours(8),
                'status' => 'Dalam Penyelidikan',
                'priority' => 'Kritikal',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Gunawan Wibisono',
                'reporter_email' => 'gunawan.w@bkpp.bojonegorokab.go.id',
                'reporter_phone' => '081234567805',
                'incident_type_slug' => 'kebocoran-data',
                'description' => 'Ditemukan sebuah file database (.sql) di sebuah forum online underground yang berisi data-data pegawai Pemkab Bojonegoro. File tersebut mencakup NIP, nama lengkap, jabatan, dan nomor telepon. Perlu segera dilakukan investigasi untuk memvalidasi kebenaran data dan sumber kebocorannya.',
                'incident_at' => now()->subDays(3),
                'status' => 'Selesai',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Hesti Rahmawati',
                'reporter_email' => 'hesti.rahmawati@dpmd.bojonegorokab.go.id',
                'reporter_phone' => '081234567806',
                'incident_type_slug' => 'phishing',
                'description' => 'Beberapa staf di kantor menerima pesan WhatsApp dari nomor tidak dikenal yang menggunakan foto profil pimpinan dinas. Pesan tersebut meminta untuk mentransfer sejumlah uang ke rekening pribadi dengan alasan darurat. Beberapa hampir menjadi korban.',
                'incident_at' => now()->subDays(5),
                'status' => 'Ditutup',
                'priority' => 'Sedang',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Indra Kusuma',
                'reporter_email' => 'indra.kusuma@satpolpp.bojonegorokab.go.id',
                'reporter_phone' => '081234567807',
                'incident_type_slug' => 'malware',
                'description' => 'Antivirus di komputer patroli mendeteksi adanya aktivitas keylogger. Perangkat lunak ini diduga merekam semua ketikan keyboard, termasuk username dan password untuk sistem internal. Komputer segera diisolasi untuk pemeriksaan lebih lanjut.',
                'incident_at' => now()->subDays(4),
                'status' => 'Selesai',
                'priority' => 'Sedang',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Joko Susilo',
                'reporter_email' => 'joko.s@dishub.bojonegorokab.go.id',
                'reporter_phone' => '081234567808',
                'incident_type_slug' => 'defacement',
                'description' => 'Salah satu halaman artikel lama di website Dinas Perhubungan (dishub.bojonegorokab.go.id) kontennya disisipi link judi online. Perubahan tidak terjadi di halaman utama, namun ditemukan saat melakukan audit konten rutin.',
                'incident_at' => now()->subDays(10),
                'status' => 'Ditutup',
                'priority' => 'Rendah',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Kartika Sari',
                'reporter_email' => 'kartika.sari@rsud-sumberrejo.bojonegorokab.go.id',
                'reporter_phone' => '081234567809',
                'incident_type_slug' => 'kebocoran-data',
                'description' => 'Seorang mantan karyawan diduga masih memiliki akses ke sistem rekam medis pasien dan mengunduh beberapa data setelah tidak lagi bekerja. Hal ini terdeteksi dari log aktivitas user yang tidak wajar pada akun miliknya yang seharusnya sudah dinonaktifkan.',
                'incident_at' => now()->subDays(7),
                'status' => 'Dalam Penyelidikan',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Lutfi Hakim',
                'reporter_email' => 'lutfi.hakim@perkim.bojonegorokab.go.id',
                'reporter_phone' => '081234567810',
                'incident_type_slug' => 'phishing',
                'description' => 'Mendapat notifikasi login dari akun Google Workspace kantor yang berasal dari lokasi yang tidak dikenal (luar negeri). Tidak ada email phishing, namun ada upaya brute force atau credential stuffing yang berhasil menembus password.',
                'incident_at' => now()->subHours(24),
                'status' => 'Baru',
                'priority' => 'Sedang',
                'assigned' => false,
            ],
            // Batch 2
            [
                'reporter_name' => 'Mega Utami',
                'reporter_email' => 'mega.utami@bappeda.bojonegorokab.go.id',
                'reporter_phone' => '081234567811',
                'incident_type_slug' => 'phishing',
                'description' => 'Menerima email dengan subjek "URGENT: Undangan Rapat Koordinasi Anggaran" yang berisi lampiran file .zip. Setelah diekstrak, file tersebut ternyata adalah aplikasi (.exe) yang mencurigakan. Tidak sempat dijalankan.',
                'incident_at' => now()->subHours(6),
                'status' => 'Baru',
                'priority' => 'Sedang',
                'assigned' => false,
            ],
            [
                'reporter_name' => 'Nanda Pratama',
                'reporter_email' => 'nanda.pratama@dpupr.bojonegorokab.go.id',
                'reporter_phone' => '081234567812',
                'incident_type_slug' => 'malware',
                'description' => 'Sebuah flashdisk yang ditemukan di area lobi kantor dicolokkan ke komputer staf. Tak lama kemudian, komputer menjadi sangat lambat dan antivirus mendeteksi adanya worm yang mencoba menyebar ke komputer lain dalam jaringan melalui shared folder.',
                'incident_at' => now()->subDays(6),
                'status' => 'Dalam Penyelidikan',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Olivia Putri',
                'reporter_email' => 'olivia.putri@dinkominfo.bojonegorokab.go.id',
                'reporter_phone' => '081234567813',
                'incident_type_slug' => 'serangan-ddos',
                'description' => 'Website utama pemkab (bojonegorokab.go.id) sempat mengalami serangan DDoS skala kecil selama 30 menit saat berlangsungnya acara live streaming sambutan Bupati. Serangan berhasil dimitigasi oleh tim teknis.',
                'incident_at' => now()->subWeeks(2),
                'status' => 'Selesai',
                'priority' => 'Kritikal',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Putra Wijaya',
                'reporter_email' => 'putra.wijaya@inspektorat.bojonegorokab.go.id',
                'reporter_phone' => '081234567814',
                'incident_type_slug' => 'kebocoran-data',
                'description' => 'Sebuah dokumen hasil audit internal yang bersifat rahasia secara tidak sengaja terunggah ke folder Google Drive yang permission-nya "Public on the web". Kebocoran terdeteksi setelah 2 jam dan link segera diamankan.',
                'incident_at' => now()->subDays(8),
                'status' => 'Diverifikasi',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Qori Aulia',
                'reporter_email' => 'qori.aulia@kec-bojonegoro.bojonegorokab.go.id',
                'reporter_phone' => '081234567815',
                'incident_type_slug' => 'defacement',
                'description' => 'Kolom komentar pada salah satu berita lawas (tahun 2019) di website kecamatan dipenuhi oleh spam dari bot yang memposting ratusan komentar berisi gambar dan link tidak senonoh.',
                'incident_at' => now()->subMonths(1),
                'status' => 'Ditutup',
                'priority' => 'Rendah',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Rina Hartati',
                'reporter_email' => 'rina.hartati@rsud-padangan.bojonegorokab.go.id',
                'reporter_phone' => '081234567816',
                'incident_type_slug' => 'malware',
                'description' => 'Server utama Sistem Informasi Rumah Sakit (SIRS) menunjukkan aktivitas CPU dan jaringan yang sangat tinggi pada malam hari, padahal tidak ada jadwal maintenance. Dicurigai adanya malware yang sedang melakukan enkripsi data secara perlahan.',
                'incident_at' => now()->subHours(12),
                'status' => 'Baru',
                'priority' => 'Kritikal',
                'assigned' => false,
            ],
            [
                'reporter_name' => 'Samsul Arifin',
                'reporter_email' => 'samsul.arifin@bpkad.bojonegorokab.go.id',
                'reporter_phone' => '081234567817',
                'incident_type_slug' => 'phishing',
                'description' => 'Bagian keuangan menerima email invoice palsu yang mengatasnamakan vendor ATK langganan. Email tersebut meminta pembayaran tagihan ke nomor rekening yang berbeda dari biasanya. Untungnya, staf melakukan konfirmasi via telepon terlebih dahulu.',
                'incident_at' => now()->subDays(9),
                'status' => 'Dalam Penyelidikan',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Tia Amelia',
                'reporter_email' => 'tia.amelia@dispendukcapil.bojonegorokab.go.id',
                'reporter_phone' => '081234567818',
                'incident_type_slug' => 'kebocoran-data',
                'description' => 'Seorang oknum operator layanan kependudukan tertangkap tangan saat mencoba mencetak dan menjual data Kartu Keluarga kepada pihak ketiga. Aksi ini berhasil digagalkan oleh rekan kerjanya yang kemudian melapor ke atasan.',
                'incident_at' => now()->subWeeks(3),
                'status' => 'Selesai',
                'priority' => 'Sedang',
                'assigned' => true,
            ],
            [
                'reporter_name' => 'Umar Faruq',
                'reporter_email' => 'umar.faruq@hukum.bojonegorokab.go.id',
                'reporter_phone' => '081234567819',
                'incident_type_slug' => 'serangan-ddos',
                'description' => 'Portal JDIH (Jaringan Dokumentasi dan Informasi Hukum) mengalami gangguan akses secara berkala. Pengguna sering mengeluhkan situs menjadi sangat lambat atau tidak bisa diakses sama sekali pada jam-jam tertentu. Diduga menjadi target serangan DDoS volume rendah.',
                'incident_at' => now()->subDays(2),
                'status' => 'Baru',
                'priority' => 'Tinggi',
                'assigned' => false,
            ],
            [
                'reporter_name' => 'Vina Septiani',
                'reporter_email' => 'vina.septiani@disdik.bojonegorokab.go.id',
                'reporter_phone' => '081234567820',
                'incident_type_slug' => 'defacement',
                'description' => 'Website subdomain salah satu SMP Negeri (smpn1bojonegoro.sch.id) halaman depannya diubah dengan pesan-pesan bernada politis dan provokatif. Tim IT sekolah meminta bantuan untuk melakukan pemulihan dan pengamanan.',
                'incident_at' => now()->subHours(4),
                'status' => 'Diverifikasi',
                'priority' => 'Tinggi',
                'assigned' => true,
            ],
        ];

        // Loop through the data array and create incidents.
        foreach ($incidents as $index => $incidentData) {
            // Define incident_at from the data array
            $incidentAt = $incidentData['incident_at'];

            // Calculate reported_at to be a random time after incident_at
            // This ensures the rule incident_at <= reported_at is met.
            $reportedAt = $incidentAt->copy()->addMinutes(rand(10, 240));

            // For resolved cases, resolved_at must be after reported_at
            $resolvedAt = null;
            if (in_array($incidentData['status'], ['Selesai', 'Ditutup'])) {
                $resolvedAt = $reportedAt->copy()->addDays(rand(1, 3))->addHours(rand(1, 12));
            }

            Incident::create([
                'case_id' => 'CSIRT-2025-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'reporter_name' => $incidentData['reporter_name'],
                'reporter_email' => $incidentData['reporter_email'],
                'reporter_phone' => $incidentData['reporter_phone'],
                'incident_type_id' => $incidentTypes->where('slug', $incidentData['incident_type_slug'])->first()->id,
                'description' => $incidentData['description'],
                'incident_at' => $incidentAt,
                'reported_at' => $reportedAt,
                'status' => $incidentData['status'],
                'priority' => $incidentData['priority'],
                'assigned_to' => ($incidentData['assigned'] && $staffUser) ? $staffUser->id : null,
                'resolved_at' => $resolvedAt,
            ]);
        }
    }
}
