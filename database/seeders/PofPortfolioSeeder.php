<?php

namespace Database\Seeders;

use App\MediaKind;
use App\Models\WorkItem;
use App\WorkStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PofPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $work = WorkItem::query()->firstOrCreate([
                'title' => 'Kalkulator PoF TARMOC',
            ], [
                'category' => 'Aplikasi Teknis',
                'summary' => 'Kalkulator untuk Tarmoc Media Converter POF yang membantu pelanggan memperkirakan tegangan adaptor minimum, panjang kabel maksimum, atau daya perangkat berdasarkan data uji. Dilengkapi grafik interaktif untuk membandingkan adaptor.',
                'role' => 'Menganalisis kebutuhan pelanggan, merancang tiga mode perhitungan, serta mengimplementasikan dan menguji kalkulator beserta grafik interaktif',
                'period' => '2026',
                'caption' => 'Dibangun menggunakan Laravel, Blade, Alpine.js, dan Tailwind CSS. Pengguna mengisi dua besaran untuk mencari besaran ketiga melalui mode Cari adaptor, Cari jarak, dan Cari daya. Hasil diperbarui tanpa memuat ulang halaman, disertai perbandingan adaptor dan detail perhitungan. Estimasi menggunakan data uji TARMOC dengan pemeriksaan batas kerja perangkat dan cakupan model; hasil bukan pengukuran lapangan.',
                'outcome' => 'Pelanggan dapat membandingkan kebutuhan adaptor, jarak kabel, dan daya perangkat dalam satu kalkulator tanpa login, dengan grafik serta penjelasan hasil yang mudah dibaca.',
                'status' => WorkStatus::Published,
                'sort_order' => (int) WorkItem::query()->max('sort_order') + 1,
            ]);

            $work->media()->firstOrCreate([
                'kind' => MediaKind::Image,
                'path' => 'work/pof/calculator.png',
            ], [
                'label' => 'Kalkulator PoF TARMOC dan hasil estimasi adaptor',
                'caption' => 'Contoh Cari adaptor untuk kebutuhan perangkat 8 W pada kabel 500 m. Angka pada gambar merupakan hasil estimasi aplikasi untuk input tersebut.',
                'sort_order' => 0,
            ]);

            $work->media()->firstOrCreate([
                'kind' => MediaKind::Image,
                'path' => 'work/pof/adapter-chart.png',
            ], [
                'label' => 'Grafik perbandingan adaptor pada kalkulator PoF TARMOC',
                'caption' => 'Grafik tegangan perangkat sepanjang kabel, dengan perbandingan adaptor dan penanda jarak yang dipilih.',
                'sort_order' => 1,
            ]);

            $work->media()->firstOrCreate([
                'kind' => MediaKind::Link,
                'url' => 'https://kalkulator-pof.opsys.web.id/',
            ], [
                'label' => 'Buka Kalkulator PoF TARMOC',
                'sort_order' => 2,
            ]);
        });
    }
}
