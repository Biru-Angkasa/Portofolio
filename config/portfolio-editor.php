<?php

return [
    'beranda' => [
        'label' => 'Beranda',
        'description' => 'Atur sapaan, nama, profesi, ringkasan, dan angka pencapaian.',
        'fields' => [
            'greeting' => ['label' => 'Sapaan', 'type' => 'text', 'max' => 160],
            'name' => ['label' => 'Nama', 'type' => 'text', 'max' => 160, 'required' => true],
            'monogram' => ['label' => 'Inisial untuk pengganti foto', 'type' => 'text', 'max' => 4, 'required' => true],
            'role' => ['label' => 'Profesi / jabatan', 'type' => 'text', 'max' => 200],
            'lead' => ['label' => 'Perkenalan singkat', 'type' => 'textarea', 'max' => 2000],
        ],
        'collections' => [
            'highlights' => [
                'label' => 'Pencapaian singkat',
                'fields' => [
                    'value' => ['label' => 'Angka / nilai', 'type' => 'text', 'max' => 40],
                    'label' => ['label' => 'Keterangan', 'type' => 'text', 'max' => 160],
                    'context' => ['label' => 'Konteks', 'type' => 'text', 'max' => 300],
                ],
            ],
        ],
    ],
    'tentang' => [
        'label' => 'Tentang Saya',
        'description' => 'Ceritakan diri kamu dan isi daftar keahlian. Satu keahlian per baris.',
        'fields' => [
            'about_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
            'about' => ['label' => 'Tentang diri kamu', 'type' => 'textarea', 'max' => 10000],
            'skills_title' => ['label' => 'Judul keahlian', 'type' => 'text', 'max' => 160],
        ],
        'collections' => [
            'skills' => [
                'label' => 'Kelompok keahlian',
                'fields' => [
                    'name' => ['label' => 'Nama kelompok', 'type' => 'text', 'max' => 160],
                    'items' => ['label' => 'Keahlian (satu per baris)', 'type' => 'lines', 'max' => 5000],
                ],
            ],
        ],
    ],
    'portofolio' => [
        'label' => 'Portofolio',
        'description' => 'Atur pengantar portofolio. Tambahkan foto, dokumen, dan proyek melalui Kelola proyek.',
        'fields' => [
            'portfolio_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
            'portfolio_intro' => ['label' => 'Pengantar', 'type' => 'textarea', 'max' => 2000],
        ],
        'collections' => [],
    ],
    'pendidikan' => [
        'label' => 'Pendidikan',
        'description' => 'Tambah, ubah, atau hapus pendidikan dan sertifikasi kamu.',
        'fields' => [
            'education_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
        ],
        'collections' => [
            'education' => [
                'label' => 'Riwayat pendidikan',
                'help' => 'Unggah logo dan isi link tujuan agar logo bisa diklik. Biarkan upload kosong untuk mempertahankan logo saat ini.',
                'fields' => [
                    'institution' => ['label' => 'Sekolah / universitas', 'type' => 'text', 'max' => 200],
                    'program' => ['label' => 'Jurusan / program', 'type' => 'text', 'max' => 200],
                    'period' => ['label' => 'Periode', 'type' => 'text', 'max' => 80],
                    'logo' => ['label' => 'Logo (JPEG, PNG, WebP; maksimal 2 MB)', 'type' => 'image', 'max' => 2048, 'required' => false],
                    'logo_path' => ['label' => 'Logo tersimpan', 'type' => 'hidden', 'max' => 500, 'required' => false],
                    'url' => ['label' => 'Link tujuan logo', 'type' => 'url', 'max' => 2000, 'required' => false],
                ],
            ],
            'certifications' => [
                'label' => 'Sertifikasi',
                'fields' => [
                    'name' => ['label' => 'Nama sertifikasi', 'type' => 'text', 'max' => 200],
                    'year' => ['label' => 'Tahun', 'type' => 'text', 'max' => 40],
                ],
            ],
        ],
    ],
    'pengalaman' => [
        'label' => 'Pengalaman',
        'description' => 'Isi pengalaman kerja, magang, atau kegiatan. Tulis satu tanggung jawab per baris.',
        'fields' => [
            'experience_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
        ],
        'collections' => [
            'experiences' => [
                'label' => 'Daftar pengalaman',
                'fields' => [
                    'organization' => ['label' => 'Perusahaan / organisasi', 'type' => 'text', 'max' => 200],
                    'role' => ['label' => 'Jabatan / peran', 'type' => 'text', 'max' => 300],
                    'period' => ['label' => 'Periode', 'type' => 'text', 'max' => 80],
                    'points' => ['label' => 'Tanggung jawab (satu per baris)', 'type' => 'lines', 'max' => 10000],
                ],
            ],
        ],
    ],
    'blog' => [
        'label' => 'Blog',
        'description' => 'Atur pengantar blog. Tulis artikel baru melalui Kelola artikel.',
        'fields' => [
            'blog_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
            'blog_intro' => ['label' => 'Pengantar', 'type' => 'textarea', 'max' => 2000],
        ],
        'collections' => [],
    ],
    'kontak' => [
        'label' => 'Kontak',
        'description' => 'Atur email, lokasi, dan tautan media sosial yang tampil di situs.',
        'fields' => [
            'contact_title' => ['label' => 'Judul bagian', 'type' => 'text', 'max' => 160],
            'contact_intro' => ['label' => 'Pengantar', 'type' => 'textarea', 'max' => 2000],
            'email' => ['label' => 'Email', 'type' => 'email', 'max' => 200, 'required' => true],
            'location' => ['label' => 'Lokasi', 'type' => 'text', 'max' => 200],
        ],
        'collections' => [
            'socials' => [
                'label' => 'Media sosial',
                'help' => 'Pilih ikon, isi nama akun dan tautannya. Untuk WhatsApp cukup isi nomor 08… atau +62…; spasi dan tanda hubung boleh dipakai. Tautan yang kosong tidak ditampilkan di situs.',
                'fields' => [
                    'platform' => [
                        'label' => 'Ikon media sosial',
                        'type' => 'select',
                        'max' => 20,
                        'required' => false,
                        'default' => 'auto',
                        'options' => [
                            'auto' => 'Otomatis dari tautan',
                            'instagram' => 'Instagram',
                            'whatsapp' => 'WhatsApp',
                            'facebook' => 'Facebook',
                            'linkedin' => 'LinkedIn',
                            'github' => 'GitHub',
                            'youtube' => 'YouTube',
                            'x' => 'X / Twitter',
                            'tiktok' => 'TikTok',
                            'telegram' => 'Telegram',
                            'website' => 'Website / tautan lainnya',
                        ],
                    ],
                    'label' => ['label' => 'Nama / akun', 'type' => 'text', 'max' => 160],
                    'url' => ['label' => 'Alamat tautan (https://...)', 'type' => 'url', 'max' => 2000, 'required' => false],
                ],
            ],
        ],
    ],
];
