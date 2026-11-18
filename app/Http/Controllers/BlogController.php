<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\WorkStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $profile = SiteProfile::current();
        $portfolio = $profile->portfolio();
        $rawCategory = $request->query('kategori');
        $activeCategory = is_string($rawCategory) ? trim($rawCategory) : '';

        $categories = BlogPost::query()
            ->published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $posts = BlogPost::query()
            ->published()
            ->when($activeCategory !== '', fn ($query) => $query->where('category', $activeCategory))
            ->latest('id')
            ->paginate(9, ['id', 'title', 'category', 'excerpt', 'cover_path', 'created_at'])
            ->withQueryString();

        $portfolio['meta']['title'] = 'Blog — '.$portfolio['name'];
        $portfolio['meta']['description'] = $portfolio['blog_intro'];

        return view('portfolio.blog-index', [
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'profile' => $profile,
            'portfolio' => $portfolio,
            'canonicalUrl' => $activeCategory !== '' ? route('blog.index', ['kategori' => $activeCategory]) : route('blog.index'),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->status === WorkStatus::Published, 404);

        $profile = SiteProfile::current();
        $portfolio = $profile->portfolio();
        $portfolio['meta']['title'] = $blogPost->title.' — '.$portfolio['name'];
        $portfolio['meta']['description'] = $blogPost->excerpt;

        $relatedPosts = BlogPost::query()
            ->published()
            ->where('id', '!=', $blogPost->id)
            ->latest('id')
            ->limit(2)
            ->get(['id', 'title', 'category', 'excerpt', 'created_at']);

        return view('portfolio.blog', [
            'post' => $blogPost,
            'relatedPosts' => $relatedPosts,
            'profile' => $profile,
            'portfolio' => $portfolio,
            'canonicalUrl' => route('blog.show', $blogPost),
            'ogType' => 'article',
            'ogImage' => $blogPost->coverUrl() ?? $profile->photoUrl(),
        ]);
    }
}
