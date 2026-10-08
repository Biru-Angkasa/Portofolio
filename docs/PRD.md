# PRD - Website Portofolio Tian Maysa

**Status:** Siap implementasi MVP  
**Sumber konten:** CV "Tian Maysa CV ATS.pdf" yang diberikan pengguna  
**Platform:** Laravel, Blade, Vite, CSS/Tailwind bawaan proyek bila tersedia  
**Bahasa situs:** Indonesia

## 1. Ringkasan

Bangun website portofolio profesional satu halaman untuk Tian Maysa, seorang Network & Technical Support Engineer dengan spesialisasi fiber optic. Situs membantu rekruter, calon klien, dan rekan kerja memahami bidang keahlian, rekam pengalaman, serta cara menghubunginya dalam waktu singkat.

Website harus terasa seperti portofolio teknis yang kredibel: konten ringkas, bukti pengalaman jelas, visual terinspirasi topologi jaringan/serat optik, dan akses mudah di ponsel. Semua klaim publik harus bersumber dari CV atau dikonfirmasi pengguna.

## 2. Sasaran dan ukuran keberhasilan

### Sasaran

1. Pengunjung memahami identitas dan fokus keahlian dalam 5 detik pertama.
2. Pengunjung dapat melihat pengalaman, kemampuan, pendidikan, dan sertifikasi tanpa membuka CV.
3. Pengunjung dapat menghubungi Tian lewat email atau Instagram dalam maksimal dua klik.
4. Situs mudah diperbarui melalui data konten terpusat tanpa perlu basis data pada MVP.

### Kriteria keberhasilan MVP

- Seluruh bagian utama terbaca nyaman pada layar ponsel 360 px dan desktop 1440 px.
- Navigasi jangkar berfungsi dan tidak tertutup header.
- Seluruh informasi faktual sesuai CV; tidak ada proyek, hasil, foto, testimoni, atau tautan palsu.
- Tautan email dan Instagram berfungsi.
- Halaman memiliki judul, deskripsi, Open Graph dasar, dan struktur heading yang benar.
- `npm run build` serta pengujian Laravel yang relevan berhasil.

## 3. Pengguna utama

- **Rekruter / hiring manager:** ingin menilai posisi, pengalaman, kemampuan, dan sertifikat secara cepat.
- **Calon klien / kolaborator:** ingin mengetahui cakupan troubleshooting, networking, dan fiber optic serta kanal kontak.
- **Tian sebagai pemilik situs:** ingin mengubah teks dan menambahkan studi kasus kelak tanpa membongkar layout.

## 4. Ruang lingkup MVP

### Dalam cakupan

- Satu halaman utama `/` dengan navigasi: Beranda, Tentang, Pengalaman, Keahlian, Sorotan Kerja, Pendidikan, Kontak.
- Hero dengan nama, jabatan, ringkasan singkat, lokasi Jakarta Pusat, CTA "Lihat pengalaman" dan "Hubungi saya".
- Ringkasan profil singkat berdasarkan CV.
- Linimasa pengalaman kerja.
- Kelompok keahlian: Networking, Fiber Optic, Technical Support, Electronic, Content & Documentation.
- Tiga sorotan kerja berbasis tanggung jawab nyata di CV, dengan label yang jujur sebagai *sorotan pengalaman*, bukan proyek independen.
- Pendidikan dan sertifikasi.
- Kontak melalui `mailto:` dan Instagram.
- Tampilan responsif, aksesibilitas dasar, SEO dasar, dan animasi halus yang menghormati `prefers-reduced-motion`.

### Di luar cakupan MVP

- Dashboard admin, login, CMS, database konten, formulir kontak, blog, dan multi bahasa.
- Galeri proyek dengan gambar, tautan demo, atau repositori yang belum diberikan.
- Unduhan CV publik sebelum pemilik meninjau nomor telepon dan informasi pribadi di PDF.
- Integrasi analitik, peta, chat, atau layanan pihak ketiga.

## 5. Struktur dan konten halaman

### 5.1 Header

- Logo teks: `Tian Maysa` atau monogram `TM`.
- Navigasi jangkar ke bagian utama.
- CTA kecil `Kontak`.
- Pada ponsel, menu mudah dibuka dengan keyboard dan memiliki `aria-expanded` yang benar.

### 5.2 Hero

- Eyebrow: `Network & Technical Support Engineer`.
- Headline yang disarankan: `Menjaga jaringan tetap terhubung. Menyelesaikan masalah teknis dengan tepat.`
- Subjudul: pengalaman di after-sales service, troubleshooting perangkat dan jaringan, konfigurasi MikroTik, serta infrastruktur fiber optic.
- Lokasi: `Jakarta Pusat, DKI Jakarta`.
- Sorotan angka: `50+ pelanggan per minggu` dalam konteks peran di Tarmoc, dan `hingga ±50 titik ODP` dalam konteks PKL di Telkom. Jangan tampilkan sebagai total karier atau statistik yang terus bertambah.

### 5.3 Tentang

Paragraf singkat tentang pendekatan teknis: diagnosis masalah, perbaikan perangkat, pemeliharaan jaringan, dukungan pelanggan secara langsung dan remote, serta dokumentasi. Sebutkan kemampuan tambahan membuat video edukasi dan konten promosi produk sebagai pendukung komunikasi teknis.

### 5.4 Pengalaman

**Tarmoc - Technical Support & After Sales Service Engineer**  
Desember 2023 - sekarang, sesuai CV; konfirmasi status terbaru sebelum publikasi.

- Menangani after-sales dan troubleshooting untuk 50+ pelanggan per minggu.
- Menganalisis dan memperbaiki gangguan perangkat elektronik, hardware, dan jaringan.
- Memberi konsultasi teknis langsung maupun remote.
- Menguji produk sebelum distribusi, mengonfigurasi perangkat berbasis MikroTik, serta memelihara jaringan internal dan perangkat pelanggan.
- Membuat dokumentasi teknis, tutorial produk, dan konten promosi digital.

**PT Telkom Indonesia - PKL, Fiber Termination Management (FTM)**  
Agustus 2022 - September 2022.

- Membantu pengelolaan dan pemeliharaan jaringan fiber optic.
- Melakukan tracing dan penataan jalur, penggantian modul SFP pada OLT/router/switch, serta validasi koneksi pelanggan melalui ODP.
- Mendukung operasional teknis hingga ±50 titik ODP.

### 5.5 Keahlian

- **Networking:** konfigurasi dasar MikroTik, IPv4 subnetting, TCP/IP, UDP, VPN, tunneling, VLAN, PPPoE, firewall, hotspot, wireless.
- **Fiber Optic:** desain topologi, splicer, OTDR, OPM, VFL, CWDM, manajemen kabel, perhitungan daya optik (dBm).
- **Technical Support:** troubleshooting hardware dan elektronik, perbaikan dan pengujian perangkat, after-sales, garansi, dukungan pelanggan, diagnosis masalah.
- **Electronic:** soldering, perbaikan PCB dasar, identifikasi komponen, troubleshooting rangkaian dasar.
- **Content & Documentation:** video editing dengan CapCut, konten TikTok produk teknis, tutorial produk, video edukasi, dokumentasi teknis.

Gunakan label kemampuan, bukan persentase atau skor kemahiran yang tidak ada dasarnya.

### 5.6 Sorotan kerja

Tampilkan tiga kartu naratif berdasarkan CV:

1. **After-sales & troubleshooting:** dukungan pelanggan, diagnosis perangkat, penanganan langsung/remote, pengujian produk.
2. **Operasional fiber optic:** tracing jalur, validasi ODP, penggantian SFP, pemeliharaan infrastruktur.
3. **Edukasi produk teknis:** tutorial penggunaan produk, video edukasi, dan dokumentasi hasil perbaikan.

Setiap kartu harus diberi keterangan bahwa ini adalah lingkup pekerjaan/pengalaman. Jangan mengarang klien, metrik tambahan, screenshot, atau hasil proyek.

### 5.7 Pendidikan dan sertifikasi

- Universitas Teknologi Muhammadiyah - Sistem Teknologi Informasi, 2024 - sekarang (sesuai CV; hindari menyebut semester karena cepat usang).
- SMK Muhammadiyah 04 Jakarta - Teknik Komputer dan Jaringan, 2020 - 2023.
- BNSP Komputer, 2023.
- BNSP Fiber Optik Telkom, 2023.

### 5.8 Kontak dan footer

- Email: `fresstneend@gmail.com`.
- Instagram: `@yaannmys`.
- Lokasi: Jakarta Pusat, DKI Jakarta.
- Jangan tampilkan nomor telepon secara publik pada MVP. Nomor ada di CV, tetapi pemilik perlu memutuskan apakah ingin mengeksposnya.
- Footer memuat nama dan tautan kembali ke atas.

## 6. Arah desain

- Kesan: modern, tenang, presisi, dan profesional; mencerminkan infrastruktur jaringan tanpa menjadi dashboard yang rumit.
- Palet: dasar navy/charcoal gelap, teks off-white, aksen cyan atau electric blue, garis tipis seperti jalur jaringan. Pertahankan kontras yang memadai.
- Tipografi: sans serif yang jelas dan mudah dibaca. Headline kuat, body nyaman, label teknis kecil sebagai aksen.
- Visual: motif node, garis topologi, atau fiber glow yang dibuat dengan CSS/SVG ringan. Hindari foto stok orang, perangkat, dan sertifikat palsu. Jika tidak ada foto asli, gunakan komposisi tipografi dan grafis abstrak.
- Tata letak: hero menonjol, whitespace cukup, kartu dan linimasa rapi, informasi teknis dapat dipindai cepat.
- Interaksi: hover/focus terlihat, transisi pendek, smooth scroll hanya bila motion diizinkan.

## 7. Arsitektur teknis

- Laravel standar dengan route `/`, Blade components/partials untuk bagian halaman, Vite untuk aset.
- Data konten disimpan terpusat, misalnya `config/portfolio.php` atau struktur PHP sejenis. Hindari database untuk konten statis MVP.
- HTML semantik, navigasi aksesibel, tautan eksternal aman, dan escaping Blade untuk data.
- Tidak memerlukan API, autentikasi, atau dependensi UI besar.
- Sertakan README singkat: cara instalasi, menjalankan lokal, dan memperbarui konten.

## 8. Kebutuhan nonfungsional dan pengujian

- Responsif pada 360, 768, dan 1440 px; tidak ada overflow horizontal.
- Kontras teks dan fokus keyboard jelas; menu ponsel dapat digunakan dengan keyboard.
- Semua gambar dekoratif, jika ada, tidak mengganggu pembaca layar.
- Meta title, meta description, Open Graph, favicon sederhana, dan `lang="id"`.
- Tidak ada konten bergantung pada JavaScript untuk dapat dibaca.
- Verifikasi route `/` mengembalikan 200 dan memuat bagian penting.
- Jalankan `php artisan test` dan `npm run build`; perbaiki kegagalan yang terkait perubahan.

## 9. Urutan implementasi

1. Siapkan struktur konten dan layout Blade.
2. Bangun komponen hero, pengalaman, keahlian, sorotan kerja, pendidikan, dan kontak.
3. Terapkan desain responsif, interaksi navigasi, aksesibilitas, dan SEO.
4. Tambahkan pengujian relevan serta README.
5. Tinjau seluruh teks terhadap PRD dan CV; cek tampilan desktop/ponsel.

## 10. Keputusan konten yang perlu dikonfirmasi sebelum publikasi

- Apakah posisi di Tarmoc dan kuliah masih berlangsung.
- Apakah email dan akun Instagram di CV masih aktif dan boleh ditampilkan.
- Apakah nomor telepon atau file CV asli akan dipublikasikan.
- Apakah ada foto, dokumentasi pekerjaan, tautan video, atau proyek nyata yang boleh ditambahkan pada fase berikutnya.

Ketiadaan konfirmasi ini tidak menghalangi pembuatan MVP lokal. Konten publik final perlu ditinjau pemilik sebelum deployment.
