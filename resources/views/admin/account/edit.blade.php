@extends('layouts.admin')
@section('title', 'Pengaturan akun')

@section('content')
    <div class="flex flex-col gap-3">
        <h1 class="section-title">Pengaturan akun</h1>
        <p class="text-sm text-soft">Akun: {{ $account->email }}</p>
    </div>
    <section class="portfolio-card flex w-full max-w-xl flex-col gap-6 p-6 md:p-8">
        <div class="flex flex-col gap-2"><h2 class="text-xl font-semibold">Ganti kata sandi</h2><p class="text-sm leading-7 text-soft">Masukkan kata sandi saat ini, lalu pilih kata sandi baru minimal 8 karakter.</p></div>
        <form method="POST" action="{{ route('admin.account.update') }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')
            <label class="flex flex-col gap-2 text-sm">
                Kata sandi saat ini
                <input type="password" name="current_password" required autocomplete="current-password" class="editor-input" @if ($errors->has('current_password')) aria-invalid="true" aria-describedby="current-password-error" @endif>
                @error('current_password')<span id="current-password-error" class="text-xs text-red-700">{{ $message }}</span>@enderror
            </label>
            <label class="flex flex-col gap-2 text-sm">
                Kata sandi baru
                <input type="password" name="password" required minlength="8" maxlength="128" autocomplete="new-password" class="editor-input" @if ($errors->has('password')) aria-invalid="true" aria-describedby="new-password-error" @endif>
                @error('password')<span id="new-password-error" class="text-xs text-red-700">{{ $message }}</span>@enderror
            </label>
            <label class="flex flex-col gap-2 text-sm">
                Ulangi kata sandi baru
                <input type="password" name="password_confirmation" required minlength="8" maxlength="128" autocomplete="new-password" class="editor-input">
            </label>
            <button type="submit" class="portfolio-button w-fit">Simpan kata sandi</button>
        </form>
    </section>

    <section class="portfolio-card flex w-full max-w-xl flex-col gap-6 p-6 md:p-8">
        <div class="flex flex-col gap-2">
            <h2 class="text-xl font-semibold">Berkas CV publik (PDF)</h2>
            <p class="text-sm leading-7 text-soft">Berkas ini diunduh pengunjung melalui tombol <strong>Unduh CV</strong> di beranda. Sebelum mengunggah, pastikan nomor telepon atau informasi pribadi yang tidak ingin dipublikasikan sudah disamarkan.</p>
            <p class="text-xs text-soft">
                Status saat ini:
                @if ($profile->hasCustomCv())
                    <span class="font-semibold text-accent">Menggunakan berkas CV unggahan admin</span>
                @elseif ($hasDefaultCv)
                    <span class="font-semibold text-accent">Menggunakan berkas bawaan situs (Tian-Maysa-CV.pdf)</span>
                @else
                    <span class="font-semibold text-red-700">Belum ada berkas CV tersedia</span>
                @endif
            </p>
        </div>
        <form method="POST" action="{{ route('admin.cv.update') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <label class="flex flex-col gap-2 text-sm">
                Unggah CV baru (PDF, maksimal 5 MB)
                <input type="file" name="cv" accept="application/pdf" required class="editor-input" @if ($errors->has('cv')) aria-invalid="true" aria-describedby="cv-error" @endif>
                @error('cv')<span id="cv-error" class="text-xs text-red-700">{{ $message }}</span>@enderror
            </label>
            <div class="flex flex-wrap items-center gap-4">
                <button type="submit" class="portfolio-button w-fit">Unggah CV</button>
                @if ($profile->hasCustomCv() || $hasDefaultCv)
                    <a href="{{ route('cv.download') }}" class="editor-secondary" target="_blank" rel="noopener noreferrer">Unduh CV aktif ↗</a>
                @endif
            </div>
        </form>
        @if ($profile->hasCustomCv())
            <form method="POST" action="{{ route('admin.cv.destroy') }}" data-confirm-delete="Hapus CV kustom yang diunggah dan kembali ke berkas bawaan?">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-fit text-sm text-red-700">Hapus CV kustom</button>
            </form>
        @endif
    </section>
@endsection
