<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkItemRequest;
use App\Http\Requests\Admin\UpdateWorkItemRequest;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\Http\RedirectResponse;
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

        return redirect()->route('admin.works.edit', $workItem);
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

        return redirect()->route('admin.works.edit', $workItem);
    }

    public function destroy(WorkItem $workItem): RedirectResponse
    {
        $workItem->delete();

        return redirect()->route('admin.works.index');
    }

    public function move(WorkItem $workItem, string $direction): RedirectResponse
    {
        abort_unless(in_array($direction, ['naik', 'turun'], true), 404);

        $swap = WorkItem::query()
            ->where('sort_order', $direction === 'naik' ? '<' : '>', $workItem->sort_order)
            ->orderBy('sort_order', $direction === 'naik' ? 'desc' : 'asc')
            ->orderBy('id', $direction === 'naik' ? 'desc' : 'asc')
            ->first();

        if ($swap !== null) {
            $current = $workItem->sort_order;

            $workItem->update(['sort_order' => $swap->sort_order]);
            $swap->update(['sort_order' => $current]);
        }

        return back();
    }
}
