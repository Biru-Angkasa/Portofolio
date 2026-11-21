<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        $works = WorkItem::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'updated_at']);

        $posts = BlogPost::query()
            ->published()
            ->orderBy('id')
            ->get(['id', 'updated_at']);

        $homeLastModified = collect([
            SiteProfile::query()->value('updated_at'),
            $works->max('updated_at'),
            $posts->max('updated_at'),
        ])->filter()->map(fn (Carbon|string $date): Carbon => Carbon::parse($date))->sort()->last();

        return response()->view('portfolio.sitemap', [
            'works' => $works,
            'posts' => $posts,
            'homeLastModified' => $homeLastModified,
        ])->withHeaders([
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
