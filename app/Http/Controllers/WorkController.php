<?php

namespace App\Http\Controllers;

use App\MediaKind;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use App\WorkStatus;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function __invoke(WorkItem $workItem): View
    {
        abort_unless($workItem->status === WorkStatus::Published, 404);

        $workItem->load('media');
        $profile = SiteProfile::current();
        $portfolio = $profile->portfolio();
        $portfolio['meta']['title'] = $workItem->title.' — '.$portfolio['name'];
        $portfolio['meta']['description'] = $workItem->summary;
        $coverImage = $workItem->media->first(fn ($media): bool => $media->kind === MediaKind::Image)?->fileUrl();

        return view('portfolio.work', [
            'portfolio' => $portfolio,
            'profile' => $profile,
            'work' => $workItem,
            'canonicalUrl' => route('work.show', $workItem),
            'robots' => 'noindex, follow',
            'ogImage' => $coverImage ?? $profile->photoUrl(),
        ]);
    }
}
