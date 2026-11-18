<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContentRequest;
use App\Models\SiteProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ContentController extends Controller
{
    public function index(): View
    {
        return view('admin.content.index', ['sections' => config('portfolio-editor')]);
    }

    public function edit(string $section): View
    {
        $definition = config('portfolio-editor.'.$section);
        abort_unless(is_array($definition), 404);
        $profile = SiteProfile::current();

        return view('admin.content.edit', [
            'section' => $section,
            'definition' => $definition,
            'portfolio' => $profile->portfolio(),
            'profile' => $profile,
        ]);
    }

    public function update(UpdateContentRequest $request, string $section): RedirectResponse
    {
        $uploadedPaths = [];
        $obsoletePaths = [];

        try {
            DB::transaction(function () use ($request, $section, &$uploadedPaths, &$obsoletePaths): void {
                $profile = SiteProfile::current();
                $profile = SiteProfile::query()->lockForUpdate()->findOrFail($profile->id);
                $content = $request->contentAttributes();

                if ($section === 'pendidikan') {
                    $previousPaths = array_column($profile->portfolio()['education'], 'logo_path');

                    foreach ($content['education'] as &$education) {
                        if (($education['logo'] ?? null) instanceof UploadedFile) {
                            $path = $education['logo']->store('education-logos', 'public');

                            if ($path === false) {
                                throw new RuntimeException('Logo tidak dapat disimpan.');
                            }

                            $uploadedPaths[] = $path;
                            $education['logo_path'] = 'storage/'.$path;
                        }

                        unset($education['logo']);
                    }
                    unset($education);

                    $obsoletePaths = array_values(array_filter(
                        array_diff($previousPaths, array_column($content['education'], 'logo_path')),
                        fn (?string $path): bool => $path !== null && str_starts_with($path, 'storage/education-logos/'),
                    ));
                }

                $profile->update(['content' => array_replace($profile->content ?? [], $content)]);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);

            throw $exception;
        }

        Storage::disk('public')->delete(array_map(fn (string $path): string => substr($path, strlen('storage/')), $obsoletePaths));

        return redirect()->route('admin.content.edit', $section)->with('status', 'Perubahan disimpan dan sudah tampil di situs.');
    }
}
