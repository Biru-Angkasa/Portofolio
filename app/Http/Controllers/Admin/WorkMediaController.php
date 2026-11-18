<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkMediaRequest;
use App\MediaKind;
use App\Models\WorkItem;
use App\Models\WorkMedia;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class WorkMediaController extends Controller
{
    public function store(StoreWorkMediaRequest $request, WorkItem $workItem): RedirectResponse
    {
        $kind = MediaKind::from($request->validated('kind'));
        $sortOrder = (int) $workItem->media()->max('sort_order') + 1;

        $attributes = [
            'kind' => $kind,
            'label' => $request->validated('label'),
            'caption' => $request->validated('caption'),
            'sort_order' => $sortOrder,
        ];

        if ($kind === MediaKind::Link) {
            $attributes['url'] = $request->validated('url');
        } else {
            $path = $request->file('file')->store('work/'.$workItem->id, 'public');

            if ($path === false) {
                throw new RuntimeException('Berkas media tidak dapat disimpan.');
            }

            $attributes['path'] = $path;
        }

        $workItem->media()->create($attributes);

        return back()->with('status', 'Media dilampirkan.');
    }

    public function destroy(WorkItem $workItem, WorkMedia $workMedia): RedirectResponse
    {
        abort_unless($workMedia->work_item_id === $workItem->id, 404);

        $workMedia->delete();

        return back()->with('status', 'Media dihapus.');
    }
}
