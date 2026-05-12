# CSIRT Kabupaten Bojonegoro

Website resmi **Computer Security Incident Response Team (CSIRT) Kabupaten Bojonegoro** — platform pengelolaan insiden keamanan siber, publikasi artikel, dan informasi layanan CSIRT untuk lingkungan Pemerintah Daerah Kabupaten Bojonegoro.

## Fitur Utama

- Manajemen laporan insiden keamanan siber
- Publikasi berita dan artikel keamanan siber
- Panel administrasi berbasis peran (role-based)
- Pembuatan ringkasan artikel dengan bantuan AI
- Server-Side Rendering (SSR) untuk performa optimal

## Tech Stack

| Lapisan          | Teknologi                                                             |
| ---------------- | --------------------------------------------------------------------- |
| Backend          | [Laravel 11](https://laravel.com) (PHP 8.2+)                          |
| Frontend         | [Vue 3](https://vuejs.org) + [Inertia.js](https://inertiajs.com)      |
| UI Component     | [PrimeVue 4](https://primevue.org)                                    |
| Styling          | [Tailwind CSS 3](https://tailwindcss.com)                             |
| Rich Text Editor | [Tiptap 3](https://tiptap.dev)                                        |
| Icons            | [Tabler Icons](https://tabler.io/icons), [Lucide](https://lucide.dev) |
| Build Tool       | [Vite 6](https://vitejs.dev)                                          |
| Auth             | Laravel Breeze + Sanctum                                              |
| Routing (JS)     | [Ziggy](https://github.com/tightenco/ziggy)                           |

## Dokumentasi

Panduan lengkap tersedia di folder [`docs/`](docs/):

| Topik | File | Deskripsi |
|-------|------|-----------|
| **Setup & Instalasi** | [docs/SETUP.md](docs/SETUP.md) | Prerequisites, instalasi awal, VS Code setup |
| **Pengembangan Lokal** | [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) | Dev commands, code styling, kontribusi |
| **Production & Deployment** | [docs/PRODUCTION.md](docs/PRODUCTION.md) | Build production, SSR, migration cases |
| **Testing** | [docs/TESTING.md](docs/TESTING.md) | PHP dan JavaScript test commands |
| **Kriptografi & Keamanan Data** | [docs/CRYPTOGRAPHY.md](docs/CRYPTOGRAPHY.md) | Enkripsi pegawai, masking, reveal mechanism |

### ⚠️ PERINGATAN KRITIS

**Jangan pernah menjalankan `php artisan key:generate` di production yang sudah memiliki data.** APP_KEY adalah satu-satunya kunci untuk mendekripsi data sensitif pegawai (NIP, NIK, telepon, email). Mengganti key akan membuat semua data terenkripsi tidak bisa dibaca. Lihat [docs/CRYPTOGRAPHY.md](docs/CRYPTOGRAPHY.md) untuk detail.

---

## Lisensi

Proyek ini dikembangkan untuk keperluan internal Pemerintah Kabupaten Bojonegoro.

## Dukungan

Untuk pertanyaan teknis atau laporan insiden, hubungi tim CSIRT Kabupaten Bojonegoro:

- Website: [csirt.bojonegorokab.go.id](https://csirt.bojonegorokab.go.id)
- Email: csirt@bojonegorokab.go.id
