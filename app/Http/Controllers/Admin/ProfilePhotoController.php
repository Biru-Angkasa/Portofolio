<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProfilePhotoRequest;
use App\Models\SiteProfile;
use App\ProfilePhotoCropper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProfilePhotoController extends Controller
{
    public function update(StoreProfilePhotoRequest $request, ProfilePhotoCropper $cropper): RedirectResponse
    {
        $profile = SiteProfile::current();
        $previous = $profile->photo_path;
        $previousOriginal = $profile->photo_original_path;
        $originalPath = $previousOriginal;
        $newOriginal = null;
        $path = null;
        $crop = $request->validated('crop');
        $crop = is_array($crop) ? array_map(fn (mixed $value): float => (float) $value, $crop) : null;

        try {
            if ($request->hasFile('photo')) {
                $newOriginal = $request->file('photo')->store('profile-originals', 'local');

                if ($newOriginal === false) {
                    throw new RuntimeException('Foto asli tidak dapat disimpan.');
                }

                $originalPath = $newOriginal;
            } elseif ($originalPath === null && is_string($previous) && Storage::disk('public')->exists($previous)) {
                $newOriginal = 'profile-originals/'.Str::uuid().'.'.pathinfo($previous, PATHINFO_EXTENSION);

                if (! Storage::disk('local')->put($newOriginal, Storage::disk('public')->get($previous))) {
                    throw new RuntimeException('Foto asli tidak dapat disimpan.');
                }

                $originalPath = $newOriginal;
            }

            if (! is_string($originalPath) || ! Storage::disk('local')->exists($originalPath)) {
                throw ValidationException::withMessages(['photo' => 'Foto asli tidak tersedia. Pilih gambar terlebih dahulu.']);
            }

            if ($crop !== null) {
                $path = 'profile/'.Str::uuid().'.png';

                if (! Storage::disk('public')->put($path, $cropper->render(Storage::disk('local')->path($originalPath), $crop))) {
                    throw new RuntimeException('Hasil crop tidak dapat disimpan.');
                }
            } else {
                $path = $request->file('photo')->store('profile', 'public');

                if ($path === false) {
                    throw new RuntimeException('Foto profil tidak dapat disimpan.');
                }
            }

            $profile->update([
                'photo_path' => $path,
                'photo_alt' => $request->validated('alt'),
                'photo_original_path' => $originalPath,
                'photo_crop' => $crop,
            ]);
        } catch (Throwable $exception) {
            if (is_string($path)) {
                Storage::disk('public')->delete($path);
            }

            if (is_string($newOriginal)) {
                Storage::disk('local')->delete($newOriginal);
            }

            throw $exception;
        }

        if (is_string($previous)) {
            Storage::disk('public')->delete($previous);
        }

        if (is_string($previousOriginal) && $previousOriginal !== $originalPath) {
            Storage::disk('local')->delete($previousOriginal);
        }

        return back()->with('status', 'Foto profil disimpan.');
    }

    public function original(): StreamedResponse
    {
        $profile = SiteProfile::current();
        $disk = $profile->photo_original_path !== null ? 'local' : 'public';
        $path = $profile->photo_original_path ?? $profile->photo_path;
        abort_unless(is_string($path) && Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->response($path, null, ['Cache-Control' => 'private, no-store']);
    }

    public function destroy(): RedirectResponse
    {
        $profile = SiteProfile::current();

        $previous = $profile->photo_path;
        $previousOriginal = $profile->photo_original_path;
        $profile->update([
            'photo_path' => null,
            'photo_alt' => null,
            'photo_original_path' => null,
            'photo_crop' => null,
        ]);

        if (is_string($previous)) {
            Storage::disk('public')->delete($previous);
        }

        if (is_string($previousOriginal)) {
            Storage::disk('local')->delete($previousOriginal);
        }

        return back();
    }
}
