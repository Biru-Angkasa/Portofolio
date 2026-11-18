<?php

namespace App\Http\Controllers;

use App\Models\SiteProfile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvDownloadController extends Controller
{
    /**
     * Download the active CV PDF or return 404 when no file exists.
     */
    public function __invoke(): BinaryFileResponse|StreamedResponse
    {
        $profile = SiteProfile::current();
        $filename = $profile->cvDownloadName();

        if ($profile->hasCustomCv()) {
            return Storage::disk('local')->download($profile->cv_path, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        $defaultPath = public_path('Tian-Maysa-CV.pdf');
        abort_unless(is_file($defaultPath), 404);

        return response()->download($defaultPath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
