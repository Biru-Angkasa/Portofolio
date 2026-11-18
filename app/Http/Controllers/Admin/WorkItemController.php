<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkItemRequest;
use App\Http\Requests\Admin\UpdateWorkItemRequest;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkItemController extends Controller
{
    public function index(): View
    {
        return view('admin.works.index', [
            'profile' => SiteProfile::current(),
            'works' => WorkItem::query()->withCount('media')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.works.create');
    }

    public function store(StoreWorkItemRequest $request): RedirectResponse
    {
        $workItem = WorkItem::query()->create([
            ...$request->itemAttributes(),
            'sort_order' => (int) WorkItem::query()->max('sort_order') + 1,
        ]);

        return redirect()->route('admin.works.edit', $workItem)->with('status', 'Bukti kerja disimpan.');
    }

    public function edit(WorkItem $workItem): View
    {
        $workItem->load('media');

        return view('admin.works.edit', [
            'work' => $workItem,
        ]);
    }

    public function update(UpdateWorkItemRequest $request, WorkItem $workItem): RedirectResponse
    {
        $workItem->update($request->itemAttributes());

        return redirect()->route('admin.works.edit', $workItem)->with('status', 'Bukti kerja diperbarui.');
    }

    public function destroy(WorkItem $workItem): RedirectResponse
    {
        $workItem->delete();

        return redirect()->route('admin.works.index')->with('status', 'Bukti kerja dihapus.');
    }

    public function move(WorkItem $workItem, string $direction): RedirectResponse
    {
        abort_unless(in_array($direction, ['naik', 'turun'], true), 404);

        DB::transaction(function () use ($workItem, $direction): void {
            $items = WorkItem::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->values();

            $currentIndex = $items->search(fn (WorkItem $item): bool => $item->id === $workItem->id);

            if ($currentIndex === false) {
                return;
            }

            $targetIndex = $direction === 'naik' ? $currentIndex - 1 : $currentIndex + 1;

            if ($targetIndex < 0 || $targetIndex >= $items->count()) {
                return;
            }

            $ordered = $items->all();
            [$ordered[$currentIndex], $ordered[$targetIndex]] = [$ordered[$targetIndex], $ordered[$currentIndex]];

            foreach ($ordered as $position => $item) {
                $nextOrder = $position + 1;

                if ($item->sort_order !== $nextOrder) {
                    $item->update(['sort_order' => $nextOrder]);
                }
            }
        });

        return back()->with('status', 'Urutan bukti kerja diperbarui.');
    }
}
