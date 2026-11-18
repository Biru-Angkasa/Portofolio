<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('site_profiles')->get() as $profile) {
            $content = json_decode($profile->content ?? '{}', true, 512, JSON_THROW_ON_ERROR);

            if (isset($content['lead'])) {
                $content['lead'] = 'Saya menangani troubleshooting jaringan dan perangkat, konfigurasi MikroTik, serta layanan after-sales. Saya juga merancang aplikasi internal untuk mempercepat pencatatan dan koordinasi tim.';
            }

            if (isset($content['about'])) {
                $content['about'] = "Di Tarmoc, saya menangani diagnosis dan perbaikan perangkat, pengujian produk, serta dukungan pelanggan secara langsung maupun remote. Pengalaman PKL di Telkom Indonesia mengasah kemampuan saya dalam tracing jalur, validasi koneksi melalui ODP, dan pemeliharaan fiber optic.\n\nDari pekerjaan sehari-hari, saya menganalisis kebutuhan tim, merancang alur kerja, serta mengimplementasikan dan menguji aplikasi internal untuk pengelolaan komplain, retur, klaim garansi, dan produksi konten. Saya juga membuat katalog digital produk, dokumentasi teknis, serta video edukasi agar informasi lebih mudah dipahami dan digunakan.";
            }

            foreach ($content['socials'] ?? [] as $index => $social) {
                $content['socials'][$index]['label'] = str_replace('Linkedln', 'LinkedIn', $social['label']);
            }

            foreach ($content['experiences'] ?? [] as $index => $experience) {
                if ($experience['organization'] !== 'Tarmoc') {
                    continue;
                }

                $content['experiences'][$index]['points'] = [
                    'Menangani after-sales dan troubleshooting untuk 50+ pelanggan per minggu.',
                    'Mendiagnosis dan memperbaiki gangguan perangkat elektronik, hardware, dan jaringan.',
                    'Memberi konsultasi teknis langsung maupun remote.',
                    'Menguji produk sebelum distribusi, mengonfigurasi perangkat berbasis MikroTik, serta memelihara jaringan internal dan perangkat pelanggan.',
                    'Menganalisis proses kerja manual dan merancang solusi digital untuk efisiensi pencatatan dan koordinasi tim.',
                    'Merancang dan mengimplementasikan aplikasi internal (komplain, retur, garansi, katalog produk, workspace konten) untuk pemantauan tindak lanjut dan koordinasi tim.',
                    'Membuat dokumentasi teknis, tutorial produk, dan konten promosi digital.',
                    'Memproduksi ±5 video edukasi dan promosi per bulan untuk TikTok Tarmoc Official.',
                ];
            }

            if (isset($content['skills'])) {
                $skills = [];
                $digitalSkillsAdded = false;

                foreach ($content['skills'] as $skill) {
                    if (in_array($skill['name'], ['Digital Solutions & AI', 'Analisis & Digitalisasi Proses'], true)) {
                        if (! $digitalSkillsAdded) {
                            $skills[] = [
                                'name' => 'Analisis & Solusi Digital',
                                'items' => [
                                    'Analisis proses kerja manual',
                                    'Pemetaan kebutuhan aplikasi internal',
                                    'Perancangan alur kerja after-sales',
                                    'Pengembangan dan pengujian aplikasi internal',
                                    'Prompt engineering & requirement specification',
                                    'Penyesuaian fitur dengan kebutuhan tim',
                                    'Penyederhanaan pencatatan dan koordinasi tim',
                                    'Python dasar',
                                ],
                            ];
                            $digitalSkillsAdded = true;
                        }

                        continue;
                    }

                    if ($skill['name'] === 'Electronic') {
                        $skill['name'] = 'Elektronika';
                    }

                    $skills[] = $skill;
                }

                $content['skills'] = $skills;
            }

            DB::table('site_profiles')->where('id', $profile->id)->update([
                'photo_alt' => $profile->photo_alt === 'China' ? 'Foto Tian Maysa' : $profile->photo_alt,
                'content' => $profile->content === null ? null : json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        }

        $projects = [
            'Katalog Digital Produk TARMOC' => [
                'title' => 'Katalog Digital Produk TARMOC',
                'role' => 'Menganalisis kebutuhan informasi produk dan merancang katalog digital beserta navigasi dan akses unduhan PDF',
                'outcome' => 'Informasi produk, harga, dan gudang tersedia dalam satu katalog digital yang mudah diakses pelanggan.',
            ],
            'Admin Workspaces - Manajemen Komplain dan Retur' => [
                'title' => 'Admin Workspace - Manajemen Komplain dan Retur',
                'role' => 'Menganalisis kebutuhan tim after-sales, merancang alur komplain dan retur, serta mengimplementasikan aplikasi internal',
                'outcome' => 'Pencatatan komplain dan retur serta riwayat tindak lanjut terpusat di satu dashboard.',
            ],
            'Garansi System - Pengelolaan Klaim Garansi' => [
                'title' => 'Garansi System - Pengelolaan Klaim Garansi',
                'role' => 'Menganalisis kebutuhan pengelolaan klaim garansi, merancang alur penanganan, dan mengimplementasikan pelacakan status',
                'outcome' => 'Status klaim tercatat dalam satu aplikasi dan dapat dilacak pelanggan untuk memantau penanganan garansi.',
            ],
            'Konten Workspaces - Produksi Konten' => [
                'title' => 'Konten Workspace - Produksi Konten',
                'role' => 'Menganalisis kebutuhan koordinasi produksi konten, merancang alur review dan revisi, serta mengimplementasikan workspace tim',
                'outcome' => 'Permintaan video, material, versi hasil, dan persetujuan tersusun dalam satu alur kerja tim.',
            ],
        ];

        foreach ($projects as $originalTitle => $attributes) {
            DB::table('work_items')->where('title', $originalTitle)->update([...$attributes, 'period' => '2026']);
        }

        foreach (DB::table('work_items')->where('title', 'Konten Workspace - Produksi Konten')->get() as $work) {
            DB::table('work_items')->where('id', $work->id)->update([
                'summary' => str_replace('metodelogi', 'metodologi', $work->summary),
            ]);
        }
    }

    /**
     * Editorial corrections are retained on rollback; previous copy cannot be reconstructed safely.
     */
    public function down(): void {}
};
