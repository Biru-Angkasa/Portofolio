# Portofolio Tian Maysa

Situs satu halaman untuk Network & Technical Support Engineer, dibangun dengan Laravel, Blade, Vite, dan Tailwind CSS. Teks pengalaman ada di `config/portfolio.php`. Foto profil dan bukti kerja disimpan lewat admin.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
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

Tidak ada registrasi publik dan tidak ada kata sandi bawaan. Buat akun sendiri:

```bash
php artisan portfolio:admin
```

Perintah itu menanyakan nama, email, dan kata sandi (minimal 12 karakter) secara interaktif. Masuk di `/login`. Jangan menyimpan kata sandi itu di repo atau di README.

## Foto profil dan bukti kerja

1. Masuk ke `/login`, lalu buka `/admin`.
2. Unggah foto profil JPEG, PNG, atau WebP (maksimal 5 MB) beserta teks alternatif. Pratinjau dipotong rapi ke rasio potret. Hapus foto kapan saja; situs kembali menampilkan monogram.
3. Tambah bukti kerja: judul, kategori, ringkasan, peran, periode opsional, caption, lalu simpan sebagai draf atau terbit.
4. Di halaman edit, lampirkan gambar, PDF, atau tautan HTTPS. Setiap gambar butuh teks alternatif. Urutan bukti diubah dengan tombol naik dan turun.
5. Hanya item berstatus terbit yang muncul di situs publik. Draf tetap tersembunyi.

Sebelum mengunggah, samarkan nama pelanggan, nomor seri, alamat IP, wajah orang lain, dan data perusahaan yang tidak boleh dipublikasikan. Jangan unggah CV asli atau nomor telepon.

## Memperbarui teks

Pengalaman, keahlian, pendidikan, dan kontak ada di `config/portfolio.php`.
