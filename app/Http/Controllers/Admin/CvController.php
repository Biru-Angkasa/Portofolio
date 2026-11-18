<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCvRequest;
use App\Models\SiteProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CvController extends Controller
{
    public function update(UpdateCvRequest $request): RedirectResponse
    {
        $profile = SiteProfile::current();
        $previous = $profile->cv_path;
        $path = $request->file('cv')->store('cv', 'local');

        if ($path === false) {
            throw new RuntimeException('Berkas CV tidak dapat disimpan.');
        }

        $profile->update(['cv_path' => $path]);

        if (is_string($previous) && $previous !== $path) {
            Storage::disk('local')->delete($previous);
        }

        return redirect()->route('admin.account.edit')->with('status', 'Berkas CV berhasil diperbarui.');
    }

    public function destroy(): RedirectResponse
    {
        $profile = SiteProfile::current();
        $previous = $profile->cv_path;

        $profile->update(['cv_path' => null]);

        if (is_string($previous)) {
            Storage::disk('local')->delete($previous);
        }

        return redirect()->route('admin.account.edit')->with('status', 'Berkas CV kustom dihapus; menggunakan berkas bawaan.');
    }
}
