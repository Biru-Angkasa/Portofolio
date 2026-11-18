<?php

namespace App\Providers;

use App\Models\BlogPost;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.portfolio', function (BladeView $view): void {
            $hasPublishedPosts = BlogPost::query()->published()->exists();
            $navigation = [];

            foreach (['beranda', 'tentang', 'pengalaman', 'pendidikan', 'portofolio', 'blog', 'kontak'] as $section) {
                if ($section === 'blog' && ! $hasPublishedPosts) {
                    continue;
                }

                $navigation[$section] = config('portfolio-editor.'.$section);
            }

            $view->with('navigation', $navigation);
        });
    }
}
