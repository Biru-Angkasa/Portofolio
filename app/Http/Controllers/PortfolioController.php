<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function __invoke(): View
    {
        $profile = SiteProfile::current();

        $recentPosts = BlogPost::query()
            ->published()
            ->latest('id')
            ->limit(7)
            ->get(['id', 'title', 'category', 'excerpt', 'cover_path', 'created_at']);

        return view('portfolio.show', [
            'portfolio' => $profile->portfolio(),
            'profile' => $profile,
            'canonicalUrl' => route('home'),
            'posts' => $recentPosts->take(6),
            'hasMorePosts' => $recentPosts->count() > 6,
            'works' => WorkItem::query()
                ->published()
                ->with('media')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
