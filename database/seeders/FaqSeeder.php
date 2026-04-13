<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::truncate();

        $faqs = [
            [
                'question' => 'Apa itu CSIRT Bojonegoro?',
                'answer' => 'CSIRT Bojonegoro (Computer Security Incident Response Team) adalah tim resmi yang dibentuk oleh Pemerintah Kabupaten Bojonegoro untuk menangani insiden keamanan siber di lingkungan instansi pemerintahan daerah. Tim ini berfungsi sebagai pusat koordinasi, penanganan, dan mitigasi insiden keamanan informasi yang terjadi di wilayah Bojonegoro.',
                'category' => 'Pengantar & Umum',
            ],
            [
                'question' => 'Mengapa CSIRT Bojonegoro dibentuk?',
                'answer' => 'CSIRT dibentuk sebagai upaya perlindungan terhadap sistem elektronik dan data pemerintah dari berbagai ancaman siber seperti peretasan, malware, kebocoran data, dan bentuk kejahatan siber lainnya. Pembentukan CSIRT sejalan dengan amanat Peraturan Presiden Nomor 95 Tahun 2018 tentang Sistem Pemerintahan Berbasis Elektronik (SPBE).',
                'category' => 'Pengantar & Umum',
            ],
            [
                'question' => 'Apa saja tugas utama CSIRT Bojonegoro?',
                'answer' => 'Menerima dan merespons laporan insiden keamanan siber. Melakukan analisis dan investigasi terhadap insiden siber. Memberikan rekomendasi teknis dan prosedural dalam penanganan insiden. Melakukan koordinasi antar instansi jika terjadi insiden berskala besar. Melakukan edukasi dan peningkatan kesadaran keamanan informasi (cyber awareness).',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro juga memberikan pelatihan atau sosialisasi?',
                'answer' => 'Benar. Salah satu fungsi CSIRT adalah meningkatkan literasi keamanan siber di lingkungan perangkat daerah. CSIRT Bojonegoro secara berkala menyelenggarakan pelatihan, simulasi penanganan insiden, dan kampanye kesadaran keamanan informasi.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Siapa saja yang dapat menghubungi CSIRT Bojonegoro?',
                'answer' => 'CSIRT Bojonegoro melayani seluruh perangkat daerah di lingkungan Pemerintah Kabupaten Bojonegoro. Masyarakat umum dapat melapor jika menemukan potensi ancaman yang berkaitan dengan sistem layanan publik pemerintah daerah.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Apa saja jenis insiden yang dapat dilaporkan ke CSIRT Bojonegoro?',
                'answer' => 'Beberapa contoh insiden yang bisa dilaporkan meliputi: Phishing atau penipuan online yang menyasar akun instansi, serangan malware atau ransomware, peretasan akun atau website milik instansi pemerintah, kebocoran data, dan aktivitas mencurigakan dalam sistem jaringan internal.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Bagaimana cara melaporkan insiden kepada CSIRT Bojonegoro?',
                'answer' => 'Laporan dapat dikirimkan melalui: Email: ttis@bojonegorokab.go.id, Website: csirt.bojonegorokab.go.id, atau kontak darurat lainnya. Sertakan informasi yang lengkap dan jelas agar penanganan dapat segera dilakukan.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Apakah pelaporan insiden dijamin kerahasiaannya?',
                'answer' => 'Ya. CSIRT Bojonegoro menjamin kerahasiaan data pelapor dan isi laporan, sesuai dengan prinsip-prinsip keamanan informasi dan peraturan perundang-undangan yang berlaku.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro bekerja sama dengan lembaga lain?',
                'answer' => 'Ya. CSIRT Bojonegoro berkoordinasi dengan BSSN (Badan Siber dan Sandi Negara) dan instansi lain seperti CSIRT sektor pemerintah pusat untuk mendukung penanganan insiden yang bersifat lintas sektor atau berskala nasional.',
                'category' => 'Koordinasi & Kolaborasi',
            ],
            [
                'question' => 'Bagaimana saya bisa tahu jika ada serangan siber yang sedang berlangsung?',
                'answer' => 'CSIRT Bojonegoro secara aktif akan menginformasikan peringatan dini (early warning) dan advisory melalui situs resmi dan saluran komunikasi internal kepada perangkat daerah.',
                'category' => 'Informasi & Peringatan Dini',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro hanya menangani serangan dari luar (eksternal)?',
                'answer' => 'Tidak. CSIRT Bojonegoro juga menangani potensi insiden internal, seperti kelalaian pengguna, kesalahan konfigurasi sistem, atau pelanggaran kebijakan keamanan yang berasal dari dalam organisasi.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apa perbedaan antara CSIRT Bojonegoro dan Diskominfo Bojonegoro?',
                'answer' => 'Diskominfo adalah dinas yang memiliki fungsi luas di bidang komunikasi dan informatika. Sementara CSIRT Bojonegoro adalah tim khusus yang berada di bawah koordinasi Diskominfo dan fokus pada keamanan informasi dan penanganan insiden siber.',
                'category' => 'Pengantar & Umum',
            ],
            [
                'question' => 'Apakah laporan insiden bisa dilakukan secara anonim?',
                'answer' => 'Sebisa mungkin, CSIRT mendorong pelapor untuk menyertakan identitas demi validasi dan koordinasi lebih lanjut. Namun, jika alasan keamanan diperlukan, laporan anonim tetap dapat diproses selama disertai bukti yang cukup.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Berapa lama waktu respons dari CSIRT Bojonegoro terhadap laporan insiden?',
                'answer' => 'Waktu respons bervariasi tergantung tingkat urgensi insiden. Laporan dengan dampak tinggi akan diprioritaskan dan ditangani dalam waktu secepat mungkin. Umumnya, CSIRT akan memberikan respons awal dalam 1x24 jam kerja.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro juga menangani hoaks digital yang menyebar di masyarakat?',
                'answer' => 'Penanganan hoaks secara umum merupakan tugas dari tim pengelola informasi publik. Namun, jika hoaks tersebut berpotensi merusak reputasi atau sistem elektronik Pemkab, maka CSIRT dapat turut terlibat dalam analisis dampaknya.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apakah ada pelaporan insiden yang harus dilakukan secara resmi melalui surat?',
                'answer' => 'Untuk koordinasi lanjutan antar-OPD atau insiden besar, CSIRT dapat meminta laporan formal melalui surat dinas. Namun, pelaporan awal tetap bisa dilakukan secara digital melalui email atau web.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro memiliki tim teknis yang turun langsung ke lapangan?',
                'answer' => 'Ya. CSIRT Bojonegoro memiliki tim teknis yang dapat melakukan investigasi langsung ke perangkat daerah jika dibutuhkan, terutama dalam kasus insiden berat yang memerlukan penelusuran fisik atau jaringan lokal.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apa yang harus dilakukan jika terjadi kebocoran data di instansi?',
                'answer' => 'Segera hubungi CSIRT dan jangan menyembunyikan kejadian. Lakukan isolasi sistem terdampak, amankan bukti log atau data, dan ikuti instruksi teknis CSIRT. Penanganan cepat dapat mengurangi dampak lebih lanjut.',
                'category' => 'Pelaporan Insiden',
            ],
            [
                'question' => 'Apakah CSIRT Bojonegoro terhubung dengan sistem pemantauan 24/7?',
                'answer' => 'Saat ini, CSIRT Bojonegoro masih mengembangkan sistem pemantauan dan early warning. Namun, notifikasi insiden penting dapat diterima kapan saja melalui sistem tertentu, terutama untuk layanan kritikal.',
                'category' => 'Tugas & Layanan',
            ],
            [
                'question' => 'Apakah ada panduan keamanan siber untuk perangkat daerah?',
                'answer' => 'Ya. CSIRT Bojonegoro menyediakan panduan dasar keamanan informasi, seperti kebijakan penggunaan email, pengamanan password, dan manajemen perangkat. Panduan ini dibagikan melalui pelatihan atau permintaan instansi.',
                'category' => 'Informasi & Peringatan Dini',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
