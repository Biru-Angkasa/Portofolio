<?php

namespace Tests\Feature;

use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_project_is_indexable_with_slugged_canonical_url_and_schema(): void
    {
        $work = WorkItem::factory()->published()->create([
            'title' => 'Dokumentasi ODP',
            'summary' => 'Validasi koneksi pelanggan.',
        ]);

        $response = $this->get('https://portfolio.example/bukti/'.$work->id.'?source=search');

        $expectedUrl = 'https://portfolio.example/bukti/'.$work->id.'/dokumentasi-odp';
        $response->assertSee('Dokumentasi ODP');
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<title>Dokumentasi ODP — Tian Maysa</title>', false);
        $response->assertSee('<meta name="description" content="Validasi koneksi pelanggan.">', false);
        $response->assertSee('<link rel="canonical" href="'.$expectedUrl.'">', false);
        $response->assertSee('<meta property="og:url" content="'.$expectedUrl.'">', false);

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $this->assertArrayHasKey(1, $matches);
        $schema = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('CreativeWork', $schema['@type']);
        $this->assertSame('Dokumentasi ODP', $schema['name']);
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
