<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Clear the table first to avoid duplicate entries on re-seeding
    DB::table('category_post')->truncate();
    DB::table('post_tag')->truncate();
    Post::truncate();

    $posts = [
      [
        'title' => 'Mengenal CSIRT: Garda Terdepan Keamanan Siber Organisasi',
        'excerpt' => 'CSIRT, atau Computer Security Incident Response Team, adalah tim khusus yang bertanggung jawab menangani insiden keamanan siber. Mereka bertindak cepat untuk mendeteksi, menganalisis, dan merespons berbagai ancaman siber yang mengintai.',
        'body' => '<h2>Apa Itu CSIRT?</h2>
<p>CSIRT (Computer Security Incident Response Team) adalah sebuah unit atau tim yang terdiri dari para ahli keamanan informasi. Tugas utama mereka adalah merespons insiden keamanan siber secara terpusat dalam sebuah organisasi. Ketika terjadi serangan siber seperti peretasan, infeksi malware, atau kebocoran data, CSIRT adalah pihak pertama yang akan bertindak.</p>
<p>Tim ini tidak hanya bekerja saat insiden terjadi. Mereka juga memiliki peran proaktif dalam menjaga keamanan. Ini termasuk melakukan analisis kerentanan, memberikan rekomendasi perbaikan sistem, serta meningkatkan kesadaran keamanan di seluruh organisasi. Keberadaan CSIRT sangat krusial untuk meminimalkan dampak negatif dari serangan siber, baik dari segi finansial maupun reputasi.</p>
<h3>Tanggung Jawab Utama CSIRT</h3>
<ul>
  <li><strong>Penanganan Insiden:</strong> Menganalisis dan menanggulangi insiden keamanan yang sedang terjadi.</li>
  <li><strong>Manajemen Kerentanan:</strong> Mengidentifikasi dan mengelola celah keamanan pada sistem.</li>
  <li><strong>Analisis Artefak:</strong> Memeriksa malware atau log sistem untuk memahami metode serangan.</li>
  <li><strong>Peningkatan Kesadaran:</strong> Memberikan edukasi dan pelatihan keamanan kepada karyawan.</li>
</ul>
<p>Dengan adanya CSIRT, sebuah organisasi memiliki kemampuan untuk merespons ancaman dengan lebih cepat, terstruktur, dan efektif, memastikan kelangsungan bisnis tetap terjaga di tengah lanskap digital yang penuh risiko.</p>',
        'categories' => [4], // Panduan Teknis
        'tags' => [4, 6], // Keamanan Jaringan, Pemerintahan
      ],
      [
        'title' => 'Siklus Hidup Penanganan Insiden oleh Tim CSIRT',
        'excerpt' => 'Penanganan insiden siber bukanlah proses acak. Tim CSIRT mengikuti siklus hidup yang terstruktur, mulai dari persiapan hingga evaluasi pasca-insiden, untuk memastikan setiap ancaman ditangani secara efektif dan efisien.',
        'body' => '<h2>Fase dalam Penanganan Insiden</h2>
<p>Tim CSIRT bekerja mengikuti kerangka kerja yang dikenal sebagai siklus hidup penanganan insiden (<em>incident handling lifecycle</em>). Proses ini memastikan bahwa setiap langkah diambil secara sistematis untuk meminimalkan kerusakan dan mencegah insiden serupa di masa depan. Berikut adalah fase-fase utamanya:</p>
<ol>
  <li><strong>Persiapan (Preparation):</strong> Fase ini adalah pondasi dari semuanya. Tim mempersiapkan tools, prosedur, dan kontak yang diperlukan. Pelatihan dan simulasi serangan juga dilakukan di sini untuk memastikan tim selalu siap.</li>
  <li><strong>Identifikasi (Identification):</strong> Ketika anomali terdeteksi, tim CSIRT akan menganalisisnya untuk menentukan apakah itu benar-benar sebuah insiden keamanan. Mereka mengumpulkan data dari berbagai sumber seperti log sistem, firewall, dan sistem deteksi intrusi.</li>
  <li><strong>Penahanan (Containment):</strong> Setelah insiden terkonfirmasi, langkah selanjutnya adalah menahan penyebarannya. Ini bisa berarti mengisolasi sistem yang terinfeksi dari jaringan atau menonaktifkan akun yang disusupi untuk mencegah kerusakan lebih lanjut.</li>
  <li><strong>Pemberantasan (Eradication):</strong> Pada fase ini, akar penyebab insiden dihilangkan. Misalnya, menghapus malware dari sistem, menambal kerentanan yang dieksploitasi, dan memastikan tidak ada lagi jejak penyerang.</li>
  <li><strong>Pemulihan (Recovery):</strong> Sistem yang terkena dampak dikembalikan ke operasi normal. Ini melibatkan pemulihan data dari cadangan (backup) dan pengujian menyeluruh untuk memastikan sistem aman dan berfungsi seperti semula.</li>
  <li><strong>Pelajaran yang Diambil (Lessons Learned):</strong> Setelah insiden selesai ditangani, tim melakukan evaluasi. Apa yang berjalan baik? Apa yang bisa ditingkatkan? Hasil analisis ini digunakan untuk memperkuat pertahanan dan menyempurnakan proses di fase persiapan.</li>
</ol>',
        'categories' => [4], // Panduan Teknis
        'tags' => [1, 4], // Tips Keamanan, Keamanan Jaringan
      ],
      [
        'title' => 'Membangun Tim CSIRT Internal: Apa Saja yang Dibutuhkan?',
        'excerpt' => 'Membentuk tim CSIRT internal adalah investasi strategis untuk keamanan jangka panjang. Proses ini memerlukan perencanaan matang, mulai dari mendefinisikan mandat, merekrut personel dengan keahlian yang tepat, hingga menyediakan teknologi pendukung.',
        'body' => '<h2>Langkah-Langkah Membangun CSIRT</h2>
<p>Membangun tim respons insiden yang efektif dari awal adalah tugas yang kompleks. Ini bukan hanya tentang merekrut orang, tetapi juga membangun proses dan mengadopsi teknologi yang tepat. Berikut adalah elemen-elemen kunci yang perlu dipertimbangkan:</p>
<h3>1. Dapatkan Dukungan Manajemen</h3>
<p>Langkah pertama dan paling krusial adalah mendapatkan dukungan penuh dari pimpinan eksekutif. Dukungan ini tidak hanya dalam bentuk anggaran, tetapi juga otoritas bagi tim CSIRT untuk mengambil tindakan yang diperlukan saat terjadi insiden, bahkan jika itu berarti menghentikan sementara layanan bisnis.</p>
<h3>2. Tentukan Misi dan Mandat</h3>
<p>Definisikan dengan jelas apa yang menjadi tanggung jawab CSIRT. Siapa konstituen yang mereka layani (misalnya, seluruh organisasi, atau hanya unit bisnis tertentu)? Layanan apa yang akan mereka tawarkan (misalnya, hanya respons reaktif atau juga layanan proaktif seperti <em>penetration testing</em>)?</p>
<h3>3. Struktur Tim dan Peran</h3>
<p>Tentukan peran yang dibutuhkan dalam tim. Beberapa peran umum meliputi:</p>
<ul>
  <li><strong>Incident Handler:</strong> Analis yang berada di garis depan untuk menangani insiden.</li>
  <li><strong>Forensic Investigator:</strong> Spesialis yang menganalisis bukti digital secara mendalam.</li>
  <li><strong>Malware Analyst:</strong> Ahli yang membedah kode berbahaya untuk memahami perilakunya.</li>
  <li><strong>Team Lead/Manager:</strong> Mengoordinasikan tim dan berkomunikasi dengan manajemen.</li>
</ul>
<h3>4. Pilih Teknologi dan Alat yang Tepat</h3>
<p>Lengkapi tim dengan perangkat yang mereka butuhkan. Ini bisa berupa platform SIEM (<em>Security Information and Event Management</em>), alat forensik, platform SOAR (<em>Security Orchestration, Automation, and Response</em>), dan sistem tiket untuk melacak insiden.</p>',
        'categories' => [4], // Panduan Teknis
        'tags' => [6], // Pemerintahan
      ],
      [
        'title' => 'Tools Penting yang Digunakan oleh Tim CSIRT Modern',
        'excerpt' => 'Efektivitas tim CSIRT sangat bergantung pada perangkat teknologi yang mereka gunakan. Dari platform SIEM untuk deteksi dini hingga alat forensik digital untuk investigasi mendalam, inilah beberapa tools esensial bagi CSIRT.',
        'body' => '<h2>Arsenal Teknologi untuk Tim Respons Insiden</h2>
<p>Sebuah tim CSIRT tidak bisa bekerja dengan tangan kosong. Mereka membutuhkan serangkaian alat canggih untuk mendeteksi, menganalisis, dan merespons ancaman siber secara efisien. Berikut adalah kategori utama dari <em>tools</em> yang menjadi andalan tim CSIRT modern:</p>
<h3>1. SIEM (Security Information and Event Management)</h3>
<p>Ini adalah otaknya pusat komando keamanan. Platform seperti <strong>Splunk</strong>, <strong>QRadar</strong>, atau <strong>Elastic SIEM</strong> mengumpulkan dan menganalisis data log dari seluruh infrastruktur TI. SIEM membantu mendeteksi aktivitas mencurigakan secara <em>real-time</em> dengan menghubungkan titik-titik data yang tampaknya tidak berhubungan.</p>
<h3>2. EDR (Endpoint Detection and Response)</h3>
<p>Alat seperti <strong>CrowdStrike Falcon</strong> atau <strong>SentinelOne</strong> memberikan visibilitas mendalam ke dalam aktivitas di setiap perangkat (endpoint) seperti laptop dan server. EDR dapat mendeteksi malware canggih dan memungkinkan analis untuk mengisolasi perangkat yang terinfeksi dari jarak jauh.</p>
<h3>3. Analisis Forensik Digital</h3>
<p>Ketika insiden terjadi, alat forensik sangat penting untuk mengumpulkan dan menganalisis bukti digital. <em>Tools</em> seperti <strong>EnCase</strong>, <strong>FTK Imager</strong>, atau <em>open-source tool</em> <strong>Autopsy</strong> digunakan untuk membuat salinan bit-per-bit dari hard drive dan memeriksa artefak digital yang ditinggalkan oleh penyerang.</p>
<h3>4. Platform Analisis Malware (Sandbox)</h3>
<p>Untuk memahami file atau email yang mencurigakan, CSIRT menggunakan <em>sandbox</em>. Ini adalah lingkungan terisolasi di mana mereka dapat menjalankan file berbahaya dan mengamati perilakunya tanpa membahayakan jaringan utama. Contohnya adalah <strong>Cuckoo Sandbox</strong> atau layanan online seperti <strong>VirusTotal</strong>.</p>',
        'categories' => [4], // Panduan Teknis
        'tags' => [4], // Keamanan Jaringan
      ],
      [
        'title' => 'Perbedaan CSIRT, SOC, dan CERT: Mana yang Tepat?',
        'excerpt' => 'Istilah CSIRT, SOC, dan CERT sering digunakan secara bergantian, padahal ketiganya memiliki fokus dan fungsi yang berbeda. Memahami perbedaan ini penting untuk membangun strategi keamanan siber yang komprehensif dan tepat sasaran.',
        'body' => '<h2>Mengurai Terminologi Keamanan Siber</h2>
<p>Dalam dunia keamanan siber, akronim seperti CSIRT, SOC, dan CERT sering terdengar. Meskipun semuanya berurusan dengan keamanan, mereka memiliki peran yang berbeda. Mari kita bedah perbedaannya.</p>
<h3>CSIRT (Computer Security Incident Response Team)</h3>
<p>Fokus utama CSIRT adalah <strong>respons</strong>. Seperti namanya, tim ini dibentuk khusus untuk merespons insiden keamanan yang sudah terjadi. Mereka reaktif, terstruktur, dan bertujuan untuk memulihkan kondisi secepat mungkin setelah serangan. Mandat mereka seringkali lebih luas, mencakup koordinasi dengan pihak eksternal seperti penegak hukum.</p>
<h3>SOC (Security Operations Center)</h3>
<p>SOC lebih berfokus pada <strong>deteksi dan pemantauan</strong>. Tim SOC bekerja 24/7 untuk memonitor jaringan, sistem, dan aplikasi secara proaktif untuk mencari tanda-tanda aktivitas berbahaya. Mereka adalah garis pertahanan pertama yang mengidentifikasi potensi ancaman sebelum berkembang menjadi insiden besar. Jika SOC mendeteksi insiden, mereka seringkali akan meneruskannya (eskalasi) ke CSIRT untuk penanganan lebih lanjut.</p>
<h3>CERT (Computer Emergency Response Team)</h3>
<p>Istilah CERT sebenarnya adalah merek dagang dari Carnegie Mellon University. Secara umum, CERT seringkali merujuk pada tim respons insiden tingkat nasional atau sektor tertentu (misalnya, CERT untuk sektor keuangan). Mereka berfungsi sebagai pusat koordinasi untuk berbagi informasi ancaman dan praktik terbaik di antara berbagai organisasi. Jadi, <em>CERT adalah jenis CSIRT</em>, tetapi biasanya dengan cakupan yang lebih luas (nasional atau sektoral).</p>
<p>Secara sederhana: <strong>SOC</strong> memantau dan mendeteksi, <strong>CSIRT</strong> merespons dan memulihkan, sementara <strong>CERT</strong> seringkali merupakan CSIRT dengan skala koordinasi yang lebih besar.</p>',
        'categories' => [1, 4], // Tips Keamanan, Panduan Teknis
        'tags' => [4], // Keamanan Jaringan
      ],
      [
        'title' => 'Keterampilan Kunci yang Harus Dimiliki Anggota Tim CSIRT',
        'excerpt' => 'Menjadi anggota CSIRT bukan hanya tentang penguasaan teknis. Dibutuhkan perpaduan unik antara keahlian teknis (hard skills) yang mendalam dan keterampilan interpersonal (soft skills) yang kuat untuk berhasil di bawah tekanan.',
        'body' => '<h2>Kombinasi Hard Skills dan Soft Skills</h2>
<p>Anggota tim CSIRT adalah profesional khusus yang membutuhkan seperangkat keterampilan beragam. Tekanan tinggi dan sifat pekerjaan yang selalu berubah menuntut lebih dari sekadar pengetahuan buku teks. Berikut adalah keterampilan yang paling dicari:</p>
<h3>Hard Skills (Keterampilan Teknis)</h3>
<ul>
  <li><strong>Jaringan Komputer:</strong> Pemahaman mendalam tentang protokol TCP/IP, routing, switching, dan arsitektur jaringan adalah hal mendasar.</li>
  <li><strong>Sistem Operasi:</strong> Keahlian dalam sistem operasi Windows, Linux, dan macOS, termasuk cara kerja kernel, manajemen proses, dan sistem file.</li>
  <li><strong>Analisis Log:</strong> Kemampuan untuk membaca, memfilter, dan menginterpretasikan log dari berbagai sumber (firewall, server, aplikasi) untuk menemukan jejak penyerang.</li>
  <li><strong>Dasar-dasar Forensik Digital:</strong> Mengetahui cara mengumpulkan dan mengawetkan bukti digital (<em>chain of custody</em>) dan menggunakan alat forensik dasar.</li>
  <li><strong>Pemrograman & Skrip:</strong> Kemampuan menulis skrip (misalnya dengan Python atau PowerShell) untuk mengotomatiskan tugas-tugas repetitif dan analisis data.</li>
</ul>
<h3>Soft Skills (Keterampilan Interpersonal)</h3>
<ul>
  <li><strong>Kemampuan Analitis & Pemecahan Masalah:</strong> Mampu berpikir kritis untuk menghubungkan berbagai petunjuk dan memecahkan teka-teki insiden.</li>
  <li><strong>Komunikasi yang Efektif:</strong> Kemampuan untuk menjelaskan masalah teknis yang kompleks kepada audiens non-teknis (seperti manajemen) dengan jelas dan ringkas.</li>
  <li><strong>Ketenangan di Bawah Tekanan:</strong> Insiden siber bisa sangat menegangkan. Kemampuan untuk tetap tenang, fokus, dan membuat keputusan yang rasional sangatlah penting.</li>
  <li><strong>Kerja Tim:</strong> Penanganan insiden adalah upaya tim. Anggota harus bisa berkolaborasi secara efektif dengan rekan satu tim dan departemen lain.</li>
</ul>',
        'categories' => [1], // Tips Keamanan
        'tags' => [1], // Tips Keamanan
      ],
      [
        'title' => 'Peran Penting CSIRT dalam Arsitektur Zero Trust',
        'excerpt' => 'Zero Trust merombak paradigma keamanan dari "percaya tapi verifikasi" menjadi "jangan pernah percaya, selalu verifikasi". Dalam model ini, peran tim CSIRT menjadi semakin krusial sebagai elemen validasi dan respons.',
        'body' => '<h2>CSIRT dalam Paradigma Keamanan Modern</h2>
<p>Zero Trust adalah model keamanan yang mengasumsikan bahwa tidak ada pengguna atau perangkat yang dapat dipercaya secara default, baik di dalam maupun di luar jaringan. Setiap permintaan akses harus diverifikasi secara ketat. Lalu, di mana peran CSIRT dalam model ini?</p>
<h3>Verifikasi Berkelanjutan (Continuous Verification)</h3>
<p>Arsitektur Zero Trust sangat bergantung pada data untuk membuat keputusan akses. Tim CSIRT menyediakan data intelijen ancaman dan analisis insiden yang sangat berharga. Data ini digunakan oleh sistem Zero Trust untuk menilai risiko secara <em>real-time</em>. Misalnya, jika CSIRT mengidentifikasi bahwa sebuah laptop terinfeksi malware, informasi ini dapat secara otomatis memicu pencabutan akses laptop tersebut ke semua sumber daya perusahaan.</p>
<h3>Respons Cepat terhadap Anomali</h3>
<p>Prinsip "selalu verifikasi" berarti akan ada lebih banyak sinyal dan anomali yang perlu diinvestigasi. CSIRT adalah tim yang bertugas menyelidiki anomali ini. Ketika kebijakan Zero Trust memblokir akses yang tidak biasa, CSIRT akan menganalisis mengapa itu terjadi. Apakah itu upaya serangan yang sah atau hanya <em>false positive</em>? Kemampuan CSIRT untuk merespons dengan cepat memastikan bahwa keamanan tetap terjaga tanpa menghambat produktivitas secara tidak perlu.</p>
<h3>Menyempurnakan Kebijakan Akses</h3>
<p>Temuan dari investigasi insiden oleh CSIRT menjadi umpan balik yang sangat penting untuk menyempurnakan kebijakan akses dalam arsitektur Zero Trust. Jika CSIRT menemukan bahwa penyerang berhasil mengeksploitasi kebijakan yang terlalu longgar, mereka dapat merekomendasikan pengetatan aturan akses untuk mencegah serangan serupa di masa depan. Dengan demikian, CSIRT menciptakan siklus perbaikan berkelanjutan untuk postur keamanan Zero Trust.</p>',
        'categories' => [4], // Panduan Teknis
        'tags' => [4], // Keamanan Jaringan
      ],
      [
        'title' => 'Tantangan Umum yang Dihadapi oleh Tim CSIRT Saat Ini',
        'excerpt' => 'Tim CSIRT menghadapi berbagai tantangan kompleks, mulai dari volume serangan yang terus meningkat, kekurangan talenta ahli, hingga kelelahan mental (burnout) akibat tekanan kerja yang tinggi. Mengatasi tantangan ini adalah kunci.',
        'body' => '<h2>Menavigasi Lanskap Ancaman yang Kompleks</h2>
<p>Pekerjaan tim CSIRT tidak pernah mudah, dan tantangannya terus berkembang seiring dengan semakin canggihnya penjahat siber. Berikut adalah beberapa tantangan utama yang dihadapi oleh tim respons insiden di seluruh dunia:</p>
<h3>1. Volume Peringatan yang Berlebihan (Alert Fatigue)</h3>
<p>Sistem keamanan modern menghasilkan ribuan peringatan setiap hari. Tim CSIRT seringkali kewalahan untuk memilah mana peringatan yang benar-benar berbahaya dan mana yang merupakan <em>false positive</em>. Hal ini dapat menyebabkan kelelahan (<em>alert fatigue</em>) dan risiko terlewatnya insiden yang nyata.</p>
<h3>2. Serangan yang Semakin Canggih</h3>
<p>Penyerang sekarang menggunakan teknik yang sangat canggih, seperti serangan <em>fileless</em> yang tidak meninggalkan jejak di disk, atau ransomware yang menyebar dengan cepat. Menghadapi ancaman seperti ini membutuhkan keahlian dan alat yang sangat khusus.</p>
<h3>3. Kekurangan Tenaga Ahli (Talent Gap)</h3>
<p>Ada kesenjangan besar antara permintaan dan ketersediaan para profesional keamanan siber yang berkualitas. Banyak organisasi kesulitan untuk merekrut dan mempertahankan anggota tim CSIRT yang berpengalaman, membuat tim yang ada seringkali kekurangan staf.</p>
<h3>4. Burnout dan Tekanan Mental</h3>
<p>Sifat pekerjaan yang reaktif dan bertekanan tinggi membuat anggota CSIRT rentan terhadap <em>burnout</em>. Mereka harus selalu siaga dan sering bekerja di luar jam kerja untuk menangani insiden kritis. Menjaga kesejahteraan mental tim adalah tantangan manajemen yang signifikan.</p>
<h3>5. Visibilitas yang Terbatas</h3>
<p>Dengan adopsi cloud, kerja jarak jauh, dan perangkat IoT, permukaan serangan (<em>attack surface</em>) menjadi sangat luas dan terfragmentasi. Tim CSIRT seringkali kesulitan mendapatkan visibilitas penuh atas seluruh aset digital organisasi, membuat deteksi dan respons menjadi lebih sulit.</p>',
        'categories' => [2], // Berita Siber
        'tags' => [1, 2], // Tips Keamanan, Ransomware
      ],
      [
        'title' => 'Mengukur Keberhasilan Tim CSIRT: Metrik dan KPI Penting',
        'excerpt' => 'Bagaimana kita tahu jika sebuah tim CSIRT bekerja secara efektif? Keberhasilan mereka dapat diukur melalui serangkaian metrik dan Key Performance Indicators (KPI) yang jelas, seperti Mean Time to Detect (MTTD).',
        'body' => '<h2>Melampaui Sekadar "Menyelesaikan Masalah"</h2>
<p>Efektivitas sebuah tim CSIRT tidak bisa hanya dinilai secara kualitatif. Untuk menunjukkan nilai mereka kepada bisnis dan untuk perbaikan berkelanjutan, penting untuk melacak metrik dan KPI yang kuantitatif. Berikut adalah beberapa metrik yang paling sering digunakan:</p>
<h3>1. Mean Time to Detect (MTTD)</h3>
<p>Ini adalah waktu rata-rata yang dibutuhkan dari saat serangan dimulai hingga tim berhasil mendeteksinya. MTTD yang rendah menunjukkan bahwa tim memiliki visibilitas dan kemampuan deteksi yang baik. Semakin cepat sebuah serangan terdeteksi, semakin kecil potensi kerusakannya.</p>
<h3>2. Mean Time to Respond (MTTR)</h3>
<p>Juga dikenal sebagai Mean Time to Remediate. Ini mengukur waktu rata-rata dari saat insiden terdeteksi hingga berhasil ditangani dan diatasi sepenuhnya. MTTR yang rendah adalah indikator utama efisiensi dan efektivitas proses respons insiden.</p>
<h3>3. Jumlah Insiden Berulang</h3>
<p>Melacak jumlah insiden yang sama atau serupa yang terjadi berulang kali dapat menunjukkan masalah yang lebih dalam. Jika insiden yang sama terus muncul, mungkin ada kerentanan mendasar yang belum ditangani atau fase "Lessons Learned" dari siklus insiden tidak dijalankan dengan baik.</p>
<h3>4. Dampak Bisnis dari Insiden</h3>
<p>Meskipun lebih sulit diukur, metrik ini mencoba mengkuantifikasi kerugian akibat insiden, baik dalam bentuk finansial (misalnya, biaya pemulihan, denda) maupun non-finansial (misalnya, waktu henti layanan, kerusakan reputasi). Tujuan CSIRT adalah untuk meminimalkan dampak ini dari waktu ke waktu.</p>
<p>Dengan melacak KPI ini secara konsisten, tim CSIRT tidak hanya dapat membuktikan nilai mereka, tetapi juga mengidentifikasi area mana dalam proses mereka yang memerlukan perbaikan.</p>',
        'categories' => [4], // Panduan Teknis
        'tags' => [1], // Tips Keamanan
      ],
      [
        'title' => 'Masa Depan CSIRT: Integrasi AI dan Otomatisasi',
        'excerpt' => 'Masa depan CSIRT terletak pada sinergi antara keahlian manusia dan kecerdasan buatan (AI). Otomatisasi akan menangani tugas-tugas repetitif, memungkinkan analis fokus pada ancaman paling kompleks dan strategis.',
        'body' => '<h2>Evolusi Tim Respons Insiden di Era AI</h2>
<p>Seiring dengan volume dan kompleksitas serangan siber yang terus meningkat, tim CSIRT tidak bisa lagi hanya mengandalkan proses manual. Evolusi berikutnya dalam respons insiden adalah integrasi mendalam antara Kecerdasan Buatan (AI) dan Otomatisasi, yang seringkali diwujudkan dalam platform SOAR (<em>Security Orchestration, Automation, and Response</em>).</p>
<h3>Peran AI dalam Mendeteksi Ancaman</h3>
<p>AI dan <em>machine learning</em> unggul dalam menganalisis data dalam jumlah besar untuk menemukan pola yang tidak terlihat oleh manusia. Dalam konteks CSIRT, AI dapat:</p>
<ul>
  <li>Menganalisis perilaku jaringan untuk mendeteksi anomali yang mengindikasikan serangan baru (<em>zero-day</em>).</li>
  <li>Memperkaya data peringatan secara otomatis dengan intelijen ancaman dari berbagai sumber.</li>
  <li>Memprioritaskan peringatan, sehingga analis dapat fokus pada ancaman yang paling kritis terlebih dahulu.</li>
</ul>
<h3>Otomatisasi untuk Respons Lebih Cepat</h3>
<p>Otomatisasi mengambil alih tugas-tugas yang repetitif dan memakan waktu dalam proses respons. Contohnya:</p>
<ul>
  <li><strong>Triase Otomatis:</strong> Ketika sebuah email phishing dilaporkan, sistem otomatis dapat menganalisis lampiran dan URL di <em>sandbox</em>.</li>
  <li><strong>Penahanan Cepat:</strong> Jika EDR mendeteksi malware, sebuah <em>playbook</em> otomatis dapat langsung mengisolasi mesin yang terinfeksi dari jaringan untuk mencegah penyebaran.</li>
  <li><strong>Pengumpulan Bukti:</strong> Mengumpulkan log relevan dari berbagai sistem secara otomatis saat insiden teridentifikasi.</li>
</ul>
<h3>Manusia Tetap di Pusat Kendali</h3>
<p>Penting untuk diingat bahwa AI dan otomatisasi adalah alat untuk <strong>membantu</strong>, bukan menggantikan, analis manusia. Kreativitas, intuisi, dan kemampuan pemecahan masalah kompleks dari seorang analis tetap tidak tergantikan. Masa depan CSIRT adalah model hibrida, di mana mesin melakukan pekerjaan berat, membebaskan manusia untuk melakukan apa yang terbaik dari mereka: berpikir secara strategis dan mengalahkan musuh yang juga manusia.</p>',
        'categories' => [2, 4], // Berita Siber, Panduan Teknis
        'tags' => [4, 2], // Keamanan Jaringan, Ransomware
      ],
    ];

    // Loop through the array and create posts
    foreach ($posts as $post) {
      $status = 'Published';
      $publishedAt = $status === 'Published' ? now()->subDays(rand(1, 30)) : null;

      $newPost = Post::create([
        'title'          => $post['title'],
        'slug'           => Str::slug($post['title']),
        'image'          => 'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 9999),
        'excerpt'        => $post['excerpt'],
        'body'           => $post['body'],
        'status'         => $status,
        'views_count'    => rand(50, 1500),
        'published_at'   => $publishedAt,
        'published_by'   => 'Admin CSIRT',
        'rating'         => round(fake()->randomFloat(1, 3.5, 5.0), 1),
        'ratings_count'  => rand(20, 500),
        'created_at'     => $publishedAt->copy()->subDays(rand(1, 5)),
        'updated_at'     => $publishedAt->copy()->addDays(rand(0, 2)),
      ]);

      // Attach the relationships
      if (!empty($post['categories'])) {
        $newPost->categories()->attach($post['categories']);
      }

      if (!empty($post['tags'])) {
        $newPost->tags()->attach($post['tags']);
      }
    }
  }
}
