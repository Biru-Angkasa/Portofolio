<?php

namespace App\Http\Controllers;

use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function __invoke(): View
    {
        return view('portfolio.show', [
            'portfolio' => config('portfolio'),
            'profile' => SiteProfile::current(),
            'works' => WorkItem::query()
                ->published()
                ->with('media')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
