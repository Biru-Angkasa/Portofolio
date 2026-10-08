<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProfilePhotoRequest;
use App\Models\SiteProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function update(StoreProfilePhotoRequest $request): RedirectResponse
    {
        $profile = SiteProfile::current();
        $path = $request->file('photo')->store('profile', 'public');
        $previous = $profile->photo_path;

        $profile->update([
            'photo_path' => $path,
            'photo_alt' => $request->validated('alt'),
        ]);

        if (is_string($previous)) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('status', 'Foto profil disimpan.');
    }

    public function destroy(): RedirectResponse
    {
        $profile = SiteProfile::current();

        if (is_string($profile->photo_path)) {
            Storage::disk('public')->delete($profile->photo_path);
        }

        $profile->update([
            'photo_path' => null,
            'photo_alt' => null,
        ]);

        return back();
    }
}
