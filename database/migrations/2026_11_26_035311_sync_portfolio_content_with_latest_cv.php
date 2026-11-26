<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = require config_path('portfolio.php');

        foreach (DB::table('site_profiles')->get() as $profile) {
            if ($profile->content === null) {
                continue;
            }

            $content = json_decode($profile->content, true, 512, JSON_THROW_ON_ERROR);

            $existingLogos = [];
            foreach ($content['education'] ?? [] as $item) {
                if (isset($item['institution'])) {
                    $existingLogos[$item['institution']] = [
                        'logo_path' => $item['logo_path'] ?? null,
                        'url' => $item['url'] ?? null,
                    ];
                }
            }

            $education = array_map(function (array $item) use ($existingLogos): array {
                $saved = $existingLogos[$item['institution']] ?? null;

                if ($saved !== null) {
                    if (! empty($saved['logo_path'])) {
                        $item['logo_path'] = $saved['logo_path'];
                    }

                    if (! empty($saved['url'])) {
                        $item['url'] = $saved['url'];
                    }
                }

                return $item;
            }, $defaults['education']);

            $content['lead'] = $defaults['lead'];
            $content['about'] = $defaults['about'];
            $content['highlights'] = $defaults['highlights'];
            $content['portfolio_title'] = $defaults['portfolio_title'];
            $content['portfolio_intro'] = $defaults['portfolio_intro'];
            $content['experiences'] = $defaults['experiences'];
            $content['skills'] = $defaults['skills'];
            $content['education'] = $education;
            $content['certifications'] = $defaults['certifications'];
            $content['email'] = $defaults['email'];
            $content['socials'] = $defaults['socials'];

            DB::table('site_profiles')->where('id', $profile->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }

        if (! DB::table('work_items')->exists()) {
            return;
        }

        $projectUpdates = [
            'Katalog Digital Produk TARMOC' => [
                'title' => 'Katalog Digital Produk Tarmoc',
                'summary' => 'Katalog web untuk produk jaringan dan fiber optic Tarmoc (switch, media converter, transceiver, kabel fiber optic, dan alat uji) dengan informasi harga, ketersediaan gudang, fitur unduh PDF, serta akses cepat ke tim sales dan technical support.',
            ],
            'Admin Workspace - Manajemen Komplain dan Retur' => [
                'title' => 'Admin Workspace: Manajemen RMA, Komplain & Retur',
                'summary' => 'Dashboard internal yang digunakan oleh sekitar 30 pengguna untuk pencatatan tiket komplain, proses retur/RMA, dan riwayat penanganan tim after-sales secara terpusat. Mencakup pemetaan alur kerja, implementasi aplikasi, serta functional testing dan UAT bersama tim operasional.',
                'role' => 'Melakukan requirement gathering, pemetaan alur kerja RMA/komplain, implementasi aplikasi internal, serta functional testing dan UAT bersama tim operasional',
                'outcome' => 'Digunakan oleh sekitar 30 pengguna untuk memusatkan pencatatan komplain, retur/RMA, dan riwayat tindak lanjut after-sales di satu dashboard.',
            ],
            'Garansi System - Pengelolaan Klaim Garansi' => [
                'title' => 'Garansi System: Portal Pelacakan Klaim Garansi',
                'summary' => 'Aplikasi pengelolaan klaim garansi untuk 5 pengguna internal tim after-sales, dilengkapi pembaruan status penanganan, link pelacakan yang dapat dicek langsung oleh pelanggan, serta integrasi tindak lanjut via WhatsApp. Diuji melalui UAT sebelum digunakan rutin.',
                'role' => 'Menganalisis alur penanganan garansi, merancang portal pelacakan pelanggan dan integrasi WhatsApp, serta melaksanakan UAT bersama tim after-sales',
                'outcome' => 'Digunakan oleh 5 pengguna internal after-sales; pelanggan dapat melacak status klaim garansi secara langsung beserta tindak lanjut via WhatsApp.',
            ],
            'Konten Workspace - Produksi Konten' => [
                'title' => 'Konten Workspace: Manajemen Alur Produksi Konten',
                'summary' => 'Aplikasi internal untuk 6 pengguna guna mengatur workflow produksi materi teknis dan video, mulai dari pengajuan request, pengelolaan aset, penugasan editor, versi hasil, review, revisi, hingga persetujuan.',
                'role' => 'Menganalisis kebutuhan koordinasi produksi konten, merancang alur review dan revisi, serta mengimplementasikan workspace tim',
                'outcome' => 'Digunakan oleh 6 pengguna untuk menyusun alur permintaan video, pengelolaan aset, review, revisi, dan persetujuan dalam satu workspace.',
            ],
            'JagaTempo - Pengingat Web & Android' => [
                'title' => 'JagaTempo: Aplikasi Pengingat Web & Android',
                'summary' => 'Aplikasi pengingat lintas platform berbasis Flutter (Dart) dan Supabase dengan 8 kategori jadwal, pengingat berulang, pelacakan masa berlaku, dan riwayat penyelesaian. Live di jagatempo.opsys.web.id.',
            ],
        ];

        foreach ($projectUpdates as $oldTitle => $attributes) {
            DB::table('work_items')->where('title', $oldTitle)->update([
                ...$attributes,
                'updated_at' => now(),
            ]);
        }

        $cctvTitle = 'Peremajaan & Structured Re-cabling CCTV';
        if (! DB::table('work_items')->where('title', $cctvTitle)->exists()) {
            $maxOrder = (int) DB::table('work_items')->max('sort_order');

            DB::table('work_items')->insert([
                'title' => $cctvTitle,
                'category' => 'Infrastruktur & CCTV',
                'summary' => 'Pengecekan jalur kabel CCTV eksisting yang sudah tidak layak pakai, penarikan ulang kabel baru, terminasi konektor, serta pengujian kestabilan daya dan konektivitas kamera hingga seluruh titik kembali normal.',
                'role' => 'Mengecek jalur kabel eksisting, melakukan penarikan ulang kabel terstruktur, terminasi konektor, serta menguji kestabilan daya dan konektivitas seluruh titik kamera CCTV',
                'period' => '2025',
                'caption' => 'Cakupan pekerjaan meliputi audit jalur kabel lama, penarikan kabel baru yang rapi dan terstruktur, terminasi konektor daya dan sinyal, serta verifikasi kestabilan gambar di seluruh titik kamera.',
                'outcome' => 'Seluruh titik kamera CCTV kembali beroperasi normal dengan jalur kabel rapi, suplai daya stabil, dan konektivitas teruji.',
                'status' => 'published',
                'sort_order' => $maxOrder + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * CV content sync is retained on rollback.
     */
    public function down(): void {}
};
