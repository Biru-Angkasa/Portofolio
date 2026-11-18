<?php

namespace Tests\Feature;

use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_fourth_project_url_renders_its_project_page_without_redirecting(): void
    {
        $work = WorkItem::factory()->published()->create([
            'id' => 4,
            'title' => 'Konten Workspace - Produksi Konten',
        ]);

        $this->get('https://portfolio.example/bukti/'.$work->id)
            ->assertOk()
            ->assertSee('Konten Workspace - Produksi Konten');
    }

    public function test_beranda_permanently_redirects_to_the_canonical_home(): void
    {
        $this->get('https://portfolio.example/beranda?source=search')
            ->assertMovedPermanently()
            ->assertRedirect('https://portfolio.example');
    }
}
