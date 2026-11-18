<?php

namespace Tests\Feature;

use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_project_remains_accessible_but_is_excluded_from_search_results(): void
    {
        $work = WorkItem::factory()->published()->create([
            'title' => 'Dokumentasi ODP',
            'summary' => 'Validasi koneksi pelanggan.',
        ]);

        $response = $this->get('https://portfolio.example/bukti/'.$work->id.'?source=search');

        $response->assertSee('Dokumentasi ODP');
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
        $response->assertSee('<title>Dokumentasi ODP — Tian Maysa</title>', false);
        $response->assertSee('<meta name="description" content="Validasi koneksi pelanggan.">', false);
        $response->assertSee('<link rel="canonical" href="https://portfolio.example/bukti/'.$work->id.'">', false);
        $response->assertSee('<meta property="og:url" content="https://portfolio.example/bukti/'.$work->id.'">', false);
    }

    public function test_project_metadata_escapes_title_and_summary(): void
    {
        $work = WorkItem::factory()->published()->create([
            'title' => 'ODP <script>alert(1)</script>',
            'summary' => 'Koneksi "aman" <teruji>',
        ]);

        $response = $this->get(route('work.show', $work));

        $response->assertSee('<title>ODP &lt;script&gt;alert(1)&lt;/script&gt; — Tian Maysa</title>', false);
        $response->assertSee('<meta name="description" content="Koneksi &quot;aman&quot; &lt;teruji&gt;">', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
