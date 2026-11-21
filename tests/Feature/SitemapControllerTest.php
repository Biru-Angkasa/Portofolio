<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_home_published_works_and_published_articles_with_absolute_urls(): void
    {
        $work = WorkItem::factory()->published()->create([
            'title' => 'Kalkulator PoF TARMOC',
            'updated_at' => '2026-10-02 12:00:00',
        ]);
        WorkItem::factory()->create();
        $post = BlogPost::factory()->published()->create(['updated_at' => '2026-10-01 12:00:00']);
        BlogPost::factory()->create();

        $response = $this->get('https://portfolio.example/sitemap.xml');

        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertSame('http://www.sitemaps.org/schemas/sitemap/0.9', $xml->getDocNamespaces()['']);
        $this->assertSame([
            'https://portfolio.example/',
            'https://portfolio.example/bukti/'.$work->id.'/kalkulator-pof-tarmoc',
            'https://portfolio.example/blog/'.$post->id,
        ], array_map(fn ($url): string => (string) $url->loc, iterator_to_array($xml->url, false)));
        $this->assertSame('2026-10-02T12:00:00+00:00', (string) $xml->url[1]->lastmod);
        $this->assertSame('2026-10-01T12:00:00+00:00', (string) $xml->url[2]->lastmod);
    }

    public function test_sitemap_still_lists_home_without_published_works_or_articles(): void
    {
        $response = $this->get('https://portfolio.example/sitemap.xml');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertCount(1, $xml->url);
        $this->assertSame('https://portfolio.example/', (string) $xml->url[0]->loc);
        $this->assertFalse(isset($xml->url[0]->lastmod));
    }

    public function test_sitemap_is_publicly_cacheable_without_starting_a_browser_session(): void
    {
        $response = $this->get('https://portfolio.example/sitemap.xml');

        $response->assertHeader('Cache-Control', 'max-age=300, public');
        $response->assertHeaderMissing('Set-Cookie');
    }

    public function test_home_last_modified_tracks_public_content_and_ignores_newer_drafts(): void
    {
        SiteProfile::current()->forceFill(['updated_at' => '2026-10-01 12:00:00'])->save();
        WorkItem::factory()->published()->create(['updated_at' => '2026-10-02 12:00:00']);
        BlogPost::factory()->published()->create(['updated_at' => '2026-10-03 12:00:00']);
        WorkItem::factory()->create(['updated_at' => '2026-10-04 12:00:00']);
        BlogPost::factory()->create(['updated_at' => '2026-10-05 12:00:00']);

        $response = $this->get('https://portfolio.example/sitemap.xml');

        $xml = simplexml_load_string($response->getContent());
        $this->assertSame('2026-10-03T12:00:00+00:00', (string) $xml->url[0]->lastmod);
    }
}
