<?php

namespace Tests\Feature;

use App\MediaKind;
use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_renders_markdown_safely_and_includes_article_metadata_and_schema(): void
    {
        Storage::fake('public');
        $coverPath = UploadedFile::fake()->image('cover.jpg')->store('blog', 'public');

        $post = BlogPost::factory()->published()->create([
            'title' => 'Konfigurasi VLAN MikroTik',
            'category' => 'Networking',
            'excerpt' => 'Panduan ringkas memisahkan trafik VLAN.',
            'cover_path' => $coverPath,
            'body' => "## Langkah awal\n\n- Siapkan bridge\n- Tambahkan `vlan-id=10`\n\n[Klik](javascript:alert(1))",
        ]);

        $response = $this->get(route('blog.show', $post));

        $response->assertOk()
            ->assertSee('<h2>Langkah awal</h2>', false)
            ->assertSee('<li>Siapkan bridge</li>', false)
            ->assertSee('<code>vlan-id=10</code>', false)
            ->assertDontSee('javascript:alert(1)', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:image" content="'.$post->coverUrl().'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<meta name="twitter:image" content="'.$post->coverUrl().'">', false);

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $this->assertArrayHasKey(1, $matches);
        $schema = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('BlogPosting', $schema['@type']);
        $this->assertSame('Konfigurasi VLAN MikroTik', $schema['headline']);
        $this->assertSame($post->coverUrl(), $schema['image']);
    }

    public function test_blog_archive_lists_published_posts_and_filters_by_category(): void
    {
        BlogPost::factory()->published()->create(['title' => 'Artikel MikroTik', 'category' => 'Networking']);
        BlogPost::factory()->published()->create(['title' => 'Artikel OTDR', 'category' => 'Fiber Optic']);
        BlogPost::factory()->create(['title' => 'Draf Tersembunyi', 'category' => 'Networking']);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Artikel MikroTik')
            ->assertSee('Artikel OTDR')
            ->assertDontSee('Draf Tersembunyi');

        $this->get(route('blog.index', ['kategori' => 'Fiber Optic']))
            ->assertOk()
            ->assertSee('Artikel OTDR')
            ->assertDontSee('Artikel MikroTik');
    }

    public function test_home_limits_blog_preview_to_six_posts_and_links_to_archive(): void
    {
        BlogPost::factory()->published()->count(7)->sequence(
            fn ($sequence): array => ['title' => 'Artikel '.($sequence->index + 1)]
        )->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Artikel 7')
            ->assertDontSee('Artikel 1')
            ->assertSee(route('blog.index'), false)
            ->assertSee('Lihat semua artikel');
    }

    public function test_work_detail_uses_first_project_image_as_open_graph_image(): void
    {
        $work = WorkItem::factory()->published()->create();
        $media = $work->media()->create([
            'kind' => MediaKind::Image,
            'path' => 'work/sample/preview.png',
            'label' => 'Pratinjau proyek',
            'sort_order' => 0,
        ]);

        $this->get(route('work.show', $work))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.$media->fileUrl().'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    public function test_guests_cannot_upload_or_delete_custom_cv(): void
    {
        $this->put(route('admin.cv.update'), [])->assertRedirect(route('login'));
        $this->delete(route('admin.cv.destroy'))->assertRedirect(route('login'));
    }

    public function test_owner_can_upload_download_replace_and_remove_custom_cv(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->put(route('admin.cv.update'), ['cv' => UploadedFile::fake()->create('not-pdf.txt', 10, 'text/plain')])
            ->assertSessionHasErrors('cv');

        $firstPdf = UploadedFile::fake()->create('cv-v1.pdf', 100, 'application/pdf');
        $this->actingAs($owner)
            ->put(route('admin.cv.update'), ['cv' => $firstPdf])
            ->assertRedirect(route('admin.account.edit'))
            ->assertSessionHas('status', 'Berkas CV berhasil diperbarui.');

        $firstPath = SiteProfile::current()->cv_path;
        $this->assertNotNull($firstPath);
        Storage::disk('local')->assertExists($firstPath);

        $this->get(route('cv.download'))
            ->assertOk()
            ->assertDownload('Tian-Maysa-CV.pdf');

        $secondPdf = UploadedFile::fake()->create('cv-v2.pdf', 120, 'application/pdf');
        $this->actingAs($owner)
            ->put(route('admin.cv.update'), ['cv' => $secondPdf])
            ->assertRedirect(route('admin.account.edit'));

        Storage::disk('local')->assertMissing($firstPath);
        $secondPath = SiteProfile::current()->cv_path;
        Storage::disk('local')->assertExists($secondPath);

        $this->actingAs($owner)
            ->delete(route('admin.cv.destroy'))
            ->assertRedirect(route('admin.account.edit'));

        Storage::disk('local')->assertMissing($secondPath);
        $this->assertNull(SiteProfile::current()->cv_path);
    }

    public function test_admin_work_crud_flashes_status_and_unknown_routes_render_custom_404(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)->post(route('admin.works.store'), [
            'title' => 'Proyek Uji',
            'category' => 'Networking',
            'summary' => 'Ringkasan singkat.',
            'role' => 'Perancang sistem.',
            'status' => 'draft',
        ])->assertSessionHas('status', 'Bukti kerja disimpan.');

        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman yang dicari tidak ditemukan.');
    }

    public function test_blog_archive_handles_array_category_query_without_crashing(): void
    {
        BlogPost::factory()->published()->create(['title' => 'Artikel Stabil']);

        $this->get(route('blog.index', ['kategori' => ['Networking']]))
            ->assertOk()
            ->assertSee('Artikel Stabil');
    }

    public function test_work_reordering_works_even_when_items_share_the_same_sort_order(): void
    {
        $owner = User::factory()->create();
        $first = WorkItem::factory()->published()->create(['title' => 'Proyek Pertama', 'sort_order' => 1]);
        $second = WorkItem::factory()->published()->create(['title' => 'Proyek Kedua', 'sort_order' => 1]);

        $this->actingAs($owner)
            ->post(route('admin.works.move', [$second, 'naik']))
            ->assertRedirect();

        $this->assertLessThan($first->fresh()->sort_order, $second->fresh()->sort_order);
    }

    public function test_education_editor_handles_missing_institution_key_on_validation_error(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)
            ->followingRedirects()
            ->from(route('admin.content.edit', 'pendidikan'))
            ->put(route('admin.content.update', 'pendidikan'), [
                'content' => [
                    'education_title' => 'Pendidikan',
                    'education' => [
                        ['logo_path' => 'images/education/utmj.png', 'program' => 'TI'],
                    ],
                ],
            ]);

        $response->assertOk()->assertSee('Periksa kolom berikut:');
    }

    public function test_empty_blog_archive_is_noindex_and_becomes_indexable_when_posts_exist(): void
    {
        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        BlogPost::factory()->published()->create();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index, follow">', false);
    }

    public function test_profile_schema_includes_work_education_and_certification_entities(): void
    {
        $response = $this->get(route('home'));

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $this->assertArrayHasKey(1, $matches);
        $schema = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

        $person = $schema['mainEntity'];
        $this->assertSame('Tarmoc', $person['worksFor'][0]['name']);
        $this->assertSame('Universitas Teknologi Muhammadiyah', $person['alumniOf'][0]['name']);
        $this->assertSame('BNSP Komputer', $person['hasCredential'][0]['name']);
    }
}
