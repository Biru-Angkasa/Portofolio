<?php

namespace App\Http\Controllers;

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

        return view('portfolio.work', [
            'portfolio' => config('portfolio'),
            'profile' => SiteProfile::current(),
            'work' => $workItem,
        ]);
    }
}
