# Portofolio Tian Maysa

Situs portofolio untuk Network & Technical Support Engineer, dibangun dengan Laravel, Blade, Vite, dan Tailwind CSS.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
```

Di Windows, salin environment dengan `copy .env.example .env` jika `cp` tidak tersedia.

## Menjalankan lokal

```bash
npm run dev
php artisan serve
```

Buka `http://localhost:8000`. `npm run dev` memuat aset saat pengembangan. Untuk aset produksi, jalankan `npm run build`.

## Akun admin

Tidak ada registrasi publik dan tidak ada kata sandi bawaan. Buat akun admin secara interaktif:

```bash
php artisan portfolio:admin
```

Masuk melalui `/login`.

## Fitur admin

- **Isi situs (`/admin/konten`)**: ubah teks Beranda, Tentang Saya & Keahlian, Pengantar Portofolio, Pendidikan & Sertifikasi (termasuk logo), Pengalaman, Pengantar Blog, dan Kontak & Media Sosial tanpa mengedit kode. Data bawaan berasal dari `config/portfolio.php` hingga disimpan melalui editor.
- **Proyek & foto (`/admin`)**: unggah dan crop foto profil (JPEG, PNG, WebP; maks. 5 MB), serta kelola bukti kerja beserta lampiran gambar, PDF, atau tautan HTTPS.
- **Artikel blog (`/admin/blog`)**: tulis artikel berformat Markdown ringan atau teks biasa dengan status draf/terbit dan gambar sampul. Artikel terbit tampil di beranda (6 terbaru) serta di halaman arsip `/blog`.
- **Pengaturan akun & CV (`/admin/akun`)**: ubah kata sandi admin serta unggah atau hapus berkas CV PDF publik (bila tidak ada unggahan kustom, `/cv/unduh` memakai `public/Tian-Maysa-CV.pdf`).

Sebelum mengunggah gambar atau CV, samarkan nama pelanggan, nomor seri, alamat IP, nomor telepon pribadi, wajah orang lain, dan data perusahaan yang tidak boleh dipublikasikan.

## Pengujian & format kode

```bash
php artisan test
vendor/bin/pint --dirty --format agent
```
