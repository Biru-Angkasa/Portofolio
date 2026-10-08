# Prompt Cursor - Redesign Portofolio Tian Maysa

Salin bagian di bawah garis ke Cursor saat folder `D:\tian-maysa-portfolio` terbuka.

---

Kamu sedang mengerjakan website Laravel yang SUDAH ADA di `D:\tian-maysa-portfolio`. Baca `AGENTS.md` dan aturan proyek yang berlaku, lalu audit implementasi saat ini sebelum mengubahnya. Jangan membuat proyek baru. Implementasikan revisi sampai selesai, bukan hanya memberi rencana atau mockup.

## Konteks hasil saat ini

Situs sudah memakai Laravel + Blade + Tailwind, dengan konten di `config/portfolio.php`. Struktur halaman ada di `resources/views/portfolio`, layout di `resources/views/layouts/portfolio.blade.php`, dan tema utama di `resources/css/app.css`. Saat ini visualnya dark navy/cyan, hero memakai SVG topologi abstrak, dan hampir semua bagian berupa teks atau kartu. Belum ada foto profil, galeri bukti kerja, atau cara mengunggahnya. Ada pula catatan internal seperti "Bukan total karier" dan "konfirmasi sebelum publikasi" yang tampil ke pengunjung. Pertahankan fakta yang valid, tetapi rapikan penyajiannya.

## Hasil yang saya mau

Portofolio yang terlihat profesional, terang, personal, dan didukung bukti nyata. Saya ingin bisa menambahkan foto profil serta foto, PDF, atau tautan video/dokumentasi pekerjaan di kemudian hari melalui antarmuka admin, tanpa mengedit Blade atau PHP setiap kali. Bukti yang belum saya unggah tidak boleh diganti contoh palsu.

## Arah desain yang wajib

- Ubah ke tema terang: latar putih hangat/off-white, teks charcoal atau navy gelap, satu aksen biru tua atau hijau kebiruan yang tenang. Kontras tetap tinggi.
- Gaya seperti portofolio profesional/editorial: tipografi jelas, grid yang proporsional, whitespace lapang, garis pemisah halus, dan foto asli menjadi fokus saat tersedia.
- Hero: nama Tian Maysa, jabatan dan nilai kerja yang ringkas, CTA menuju bukti kerja dan kontak. Sediakan area foto profil dengan rasio potret yang bagus; sebelum foto diunggah tampilkan fallback monogram yang sederhana dan pantas, tanpa wajah buatan atau stock photo.
- Hindari gaya generik hasil generator: dark cyber theme, neon/glow, gradient besar, glassmorphism, orb, SVG topologi sebagai visual utama, dashboard palsu, kartu seragam di setiap section, pill/badge berlebihan, ikon acak, animasi berlebihan, serta kalimat promosi kosong.
- Buat variasi layout yang masuk akal: pengalaman sebagai timeline/daftar editorial; keahlian dikelompokkan ringkas; bukti kerja sebagai galeri visual dengan foto, judul, konteks, dan caption. Tidak semua konten harus dibungkus card.
- Tulis ulang microcopy agar natural dan profesional dalam bahasa Indonesia. Jangan tampilkan frasa internal PRD seperti "lingkup pekerjaan, bukan proyek terpisah", "bukan total karier", atau "konfirmasi sebelum publikasi" kepada pengunjung. Tetap jujur soal konteks metrik dan pengalaman.
- Responsif di 360/768/1440 px, aksesibel dengan keyboard, focus state terlihat, alt text bermakna, dan hormati `prefers-reduced-motion`.

## Sistem foto profil dan bukti kerja

1. Buat area admin yang dilindungi login untuk pemilik situs. Tidak ada registrasi publik. Jangan hardcode email/password admin di repo, Blade, atau seed data. Gunakan pendekatan autentikasi Laravel yang cocok dengan versi terpasang dan ikuti `AGENTS.md`.
2. Di admin, pemilik bisa mengunggah, mengganti, dan menghapus foto profil. Validasi gambar (JPEG/PNG/WebP), ukuran file yang masuk akal, serta tampilkan preview/crop yang rapi. Jika belum ada foto, fallback monogram tetap terlihat baik.
3. Di admin, pemilik bisa membuat, mengedit, mengurutkan, menyimpan sebagai draft, memublikasikan, dan menghapus item bukti kerja. Minimal field: judul, kategori, ringkasan/konteks pekerjaan, peran/tindakan, tanggal atau periode opsional, caption, dan status publikasi. Sediakan kemampuan melampirkan lebih dari satu media per item bila sederhana untuk dirawat.
4. Media yang didukung: gambar JPEG/PNG/WebP, PDF, dan tautan HTTPS ke video atau dokumentasi eksternal. Jangan menerima file executable, SVG upload, HTML mentah, atau iframe arbitrer. Validasi MIME, ukuran, URL, dan tipe konten di server; hapus file lama saat diganti/dihapus jika aman dilakukan.
5. Gambar publik tampil dengan rasio dan resolusi tepat, `loading="lazy"` untuk media di bawah hero, alt text wajib saat publikasi, serta caption yang memberi konteks. PDF diberi tombol buka/unduh yang jelas; tautan eksternal diberi label dan atribut keamanan yang sesuai.
6. Halaman publik hanya menampilkan item berstatus terbit. Jika belum ada bukti, jangan buat kartu dummy atau gambar palsu: sembunyikan galeri atau tampilkan transisi yang tetap rapi tanpa klaim palsu. Saat ada bukti, pengguna bisa membuka detail item dan melihat media beserta konteksnya.
7. Jaga kerahasiaan data pelanggan/perusahaan: beri catatan di admin untuk menyamarkan nama, serial number, IP, wajah orang lain, dan informasi sensitif sebelum unggah. Jangan otomatis memublikasikan file CV asli atau nomor telepon.
8. Konten pengalaman/skill yang sudah ada tetap dapat berasal dari `config/portfolio.php`; konten media dan bukti baru boleh memakai SQLite yang sudah ada. Buat migrasi/model/relasi seperlunya tanpa arsitektur berlebihan. Jangan tambah dependency hanya untuk efek visual.

## Konten dan informasi

- Pertahankan fakta dari CV yang sudah ada. Jangan mengarang proyek, sertifikasi, klien, metrik, hasil, tautan, testimoni, atau foto.
- Sorotan pekerjaan yang sekarang boleh menjadi pengantar area bukti, tetapi jangan menyatakannya sebagai case study lengkap sebelum bukti nyata tersedia.
- Metrik 50+ pelanggan per minggu harus jelas sebagai konteks pekerjaan di Tarmoc; ±50 titik ODP harus jelas sebagai konteks PKL Telkom.
- Perbarui meta title, description, Open Graph, dan theme color agar sesuai desain baru. Gunakan foto profil sebagai `og:image` hanya jika benar-benar sudah tersedia; sediakan fallback yang layak bila belum.
- Pastikan email dan Instagram yang sudah ada tetap berfungsi. Status pekerjaan dan kuliah yang masih bertuliskan "sekarang" tetap perlu diverifikasi pemilik sebelum situs dipublikasikan; jangan menyisipkan peringatan editorial ke halaman publik.

## Yang harus kamu kerjakan

1. Audit halaman dan komponen yang ada, lalu jelaskan singkat perubahan yang akan dibuat.
2. Implementasikan redesign responsif dan alur foto profil/bukti kerja end-to-end pada proyek yang sama.
3. Tambahkan dokumentasi singkat di README: cara membuat akun admin secara aman, login, mengunggah foto profil, menambah bukti, dan memublikasikannya. Jangan tulis kredensial nyata.
4. Buat tes yang berarti untuk akses admin, upload/validasi media, draft vs publikasi, dan halaman publik. Jalankan tes relevan, formatter PHP sesuai aturan proyek, dan `npm run build`.
5. Buka hasilnya di browser dan periksa desktop serta ponsel. Pastikan tidak ada broken image, layout meluber, menu rusak, atau bagian kosong yang terlihat seperti placeholder.
6. Di akhir, laporkan apa yang sudah jadi, file utama yang berubah, hasil tes/build, dan langkah spesifik yang harus saya lakukan untuk memasukkan foto profil serta bukti pertama.

Jika ada keputusan kecil yang belum ditentukan, pilih solusi sederhana yang bisa dirawat dan lanjutkan. Hanya minta input saya untuk hal yang benar-benar membutuhkan aset atau fakta pribadi yang belum diberikan; sementara itu selesaikan bagian lain yang tidak bergantung pada input tersebut.
