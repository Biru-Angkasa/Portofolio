<form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" data-photo-crop data-original-url="{{ route('admin.profile.original') }}" data-crop-settings="{{ json_encode($profile->photo_crop ?? []) }}" class="flex min-w-0 flex-col gap-5">
    @csrf
    <input type="hidden" name="use_existing_photo" value="0" data-crop-existing>
    <input type="hidden" name="crop[x]" value="0.5" data-crop-value="x" disabled>
    <input type="hidden" name="crop[y]" value="0.5" data-crop-value="y" disabled>
    <input type="hidden" name="crop[zoom]" value="1" data-crop-value="zoom" disabled>
    @if ($profile->photo_path)
        <div class="flex flex-wrap items-center gap-4">
            <div class="profile-photo-shape size-24 overflow-hidden border border-line bg-paper"><img src="{{ $profile->photoUrl() }}" alt="{{ $profile->photo_alt }}" class="h-full w-full object-cover"></div>
            <button type="button" data-crop-current class="editor-secondary">Atur ulang foto saat ini</button>
        </div>
    @endif
    <label class="flex flex-col gap-2 text-sm">Pilih foto (JPEG, PNG, WebP; maksimal 5 MB)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required data-crop-file class="editor-input"></label>
    <p data-crop-status role="status" class="text-xs text-soft">Pilih foto untuk mengatur bagian yang tampil.</p>
    <p data-crop-error role="alert" hidden class="text-sm text-red-700"></p>
    <div data-crop-editor hidden class="grid min-w-0 gap-6 md:grid-cols-[minmax(0,24rem)_minmax(0,1fr)]">
        <div class="flex min-w-0 flex-col gap-3">
            <div data-crop-stage tabindex="0" role="group" aria-label="Posisi foto: geser dengan mouse atau jari, atau gunakan tombol panah" class="photo-crop-stage focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                <img data-crop-image alt="Foto yang sedang diatur" draggable="false">
                <div class="photo-crop-window profile-photo-shape" aria-hidden="true"></div>
            </div>
            <p class="text-xs leading-6 text-soft">Geser foto untuk mengatur posisi. Bagian terang akan tampil di profil; bagian gelap akan terpotong.</p>
        </div>
        <div class="flex min-w-0 flex-col justify-center gap-5">
            <label class="flex flex-col gap-3 text-sm"><span class="flex justify-between gap-3">Zoom <output data-crop-zoom>1.00×</output></span><input type="range" min="1" max="3" step="0.01" value="1" data-crop-control="zoom" class="w-full accent-accent"></label>
            <label class="flex flex-col gap-3 text-sm">Geser kiri–kanan<input type="range" min="0" max="100" step="0.1" value="50" data-crop-control="x" class="w-full accent-accent"></label>
            <label class="flex flex-col gap-3 text-sm">Geser atas–bawah<input type="range" min="0" max="100" step="0.1" value="50" data-crop-control="y" class="w-full accent-accent"></label>
            <button type="button" data-crop-reset class="editor-secondary w-fit">Reset posisi</button>
        </div>
    </div>
    <label class="flex flex-col gap-2 text-sm">Deskripsi foto<input type="text" name="alt" value="{{ old('alt', $profile->photo_alt ?? 'Potret '.$profile->portfolio()['name']) }}" required maxlength="160" class="editor-input"></label>
    @error('photo')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
    @error('alt')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
    @foreach (['crop', 'crop.x', 'crop.y', 'crop.zoom'] as $cropField)
        @error($cropField)<p class="text-sm text-red-700">{{ $message }}</p>@enderror
    @endforeach
    <button type="submit" data-crop-save class="portfolio-button w-fit">Simpan foto</button>
</form>
