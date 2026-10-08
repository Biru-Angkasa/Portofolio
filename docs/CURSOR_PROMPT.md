# Prompt untuk Cursor

Salin seluruh prompt berikut ke Cursor setelah membuka folder proyek Laravel `D:\tian-maysa-portfolio`.

---

Kamu sedang berada di proyek Laravel baru `D:\tian-maysa-portfolio`. Baca `AGENTS.md` dan `docs/PRD.md` di dalam repo terlebih dahulu, termasuk langkah setup yang diwajibkan `AGENTS.md`. Implementasikan website portofolio Tian Maysa sesuai PRD sampai aplikasi dapat dijalankan, diuji, dan ditinjau secara visual. Jangan berhenti pada rencana atau mockup.

Tujuan: website portofolio satu halaman berbahasa Indonesia untuk Network & Technical Support Engineer dengan spesialisasi fiber optic. Gunakan Laravel, Blade, Vite, dan CSS/Tailwind yang kompatibel dengan instalasi yang sudah ada. Pertahankan proyek ringan: konten statis terpusat di file PHP, tanpa database, login, CMS, atau formulir kontak pada MVP.

Persyaratan desain:
- Bangun tampilan profesional dan khas: navy/charcoal, aksen cyan, elemen grafis topologi jaringan/fiber optic yang ringan lewat CSS atau SVG, tipografi kuat, spacing lapang, dan layout responsif.
- Jangan pakai foto stok, logo perusahaan tanpa izin, gambar sertifikat palsu, atau proyek buatan. Jika foto asli belum ada, gunakan visual abstrak dan tipografi.
- Bagian halaman: header/navigasi, hero, tentang, pengalaman, keahlian, sorotan kerja, pendidikan/sertifikasi, kontak, footer.
- Buat menu ponsel yang dapat dipakai dengan keyboard, focus state jelas, heading semantik, dan dukungan `prefers-reduced-motion`.
- Optimalkan tampilan layar 360 px, 768 px, dan 1440 px. Pastikan tidak ada overflow horizontal.

Aturan konten:
- Ambil fakta hanya dari `docs/PRD.md` (yang diringkas dari CV). Jangan mengarang proyek, prestasi, metrik, sertifikat, tautan, atau testimoni.
- Perlakukan tiga kartu sorotan sebagai pengalaman/lingkup kerja, bukan proyek independen.
- Tampilkan email `fresstneend@gmail.com` dan Instagram `@yaannmys` sebagai CTA. Jangan tampilkan nomor telepon atau unduhan CV pada MVP.
- Gunakan konteks benar untuk metrik: 50+ pelanggan per minggu di Tarmoc; hingga ±50 titik ODP pada PKL Telkom.
- Hindari label semester kuliah yang cepat usang. Status pekerjaan dan kuliah mengikuti PRD untuk prototipe lokal, tetapi tandai sebagai perlu konfirmasi sebelum publikasi.

Persyaratan teknis:
- Route `/` harus merender halaman portofolio. Pisahkan layout dan section ke Blade components/partials yang mudah dirawat.
- Simpan teks dan daftar pengalaman/skill/sertifikasi secara terpusat, misalnya di `config/portfolio.php`, agar konten mudah diedit.
- Tambahkan title, meta description, Open Graph dasar, favicon sederhana, dan `lang="id"`.
- Gunakan escaping Blade, link eksternal aman, dan jangan menaruh rahasia di frontend.
- Buat README yang menjelaskan instalasi, `npm install`, `npm run dev`, `php artisan serve`, cara build, dan lokasi file konten.

Verifikasi sebelum selesai:
1. Jalankan `php artisan test` dan `npm run build`; perbaiki kegagalan terkait implementasi.
2. Buka halaman lokal dan periksa tampilan desktop serta ponsel. Cek navigasi, CTA, menu ponsel, dan overflow.
3. Laporkan file yang diubah, perintah pengujian beserta hasilnya, dan keputusan yang masih perlu konfirmasi sebelum situs dipublikasikan.

Kerjakan langsung di proyek ini. Jangan membuat proyek Laravel baru atau mengubah folder lain.
