<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog.index', ['posts' => BlogPost::query()->latest('id')->paginate(12)]);
    }

    public function create(): View
    {
        return view('admin.blog.form', ['post' => new BlogPost]);
    }

    public function store(SaveBlogPostRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->only(['title', 'category', 'excerpt', 'body', 'status']);

        if ($request->hasFile('cover')) {
            $attributes['cover_path'] = $request->file('cover')->store('blog', 'public');
        }

        $post = BlogPost::query()->create($attributes);

        return redirect()->route('admin.blog.edit', $post)->with('status', 'Artikel disimpan.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog.form', ['post' => $blogPost]);
    }

    public function update(SaveBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $attributes = $request->safe()->only(['title', 'category', 'excerpt', 'body', 'status']);
        $previous = $blogPost->cover_path;

        if ($request->hasFile('cover')) {
            $attributes['cover_path'] = $request->file('cover')->store('blog', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $attributes['cover_path'] = null;
        }

        $blogPost->update($attributes);

        if (array_key_exists('cover_path', $attributes) && is_string($previous)) {
            Storage::disk('public')->delete($previous);
        }

        return redirect()->route('admin.blog.edit', $blogPost)->with('status', 'Artikel diperbarui.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()->route('admin.blog.index')->with('status', 'Artikel dihapus.');
    }
}
