<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PortfolioRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_download_returns_the_supplied_pdf(): void
    {
        $response = $this->get(route('cv.download'));

        $response->assertDownload('Tian-Maysa-CV.pdf');
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_empty_blog_and_its_navigation_links_are_hidden_on_home_and_project_pages(): void
    {
        BlogPost::factory()->create();
        $work = WorkItem::factory()->published()->create();

        $response = $this->get(route('home'));

        $response->assertDontSee('id="blog"', false);
        $response->assertDontSee('href="'.route('home').'#blog"', false);
        $response->assertDontSee('Tulisan pertama sedang disiapkan.');
        $this->get(route('work.show', $work))->assertDontSee('href="'.route('home').'#blog"', false);
    }

    public function test_published_blog_and_navigation_appear_automatically(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->get(route('home'));

        $response->assertSee('id="blog"', false);
        $response->assertSee('href="'.route('home').'#blog"', false);
        $response->assertSee($post->title);
        $this->get(route('blog.show', $post))->assertSee('href="'.route('home').'#blog"', false);
    }

    public function test_experience_precedes_education_and_portfolio(): void
    {
        $response = $this->get(route('home'));

        $response->assertSeeInOrder(['id="pengalaman"', 'id="pendidikan"', 'id="bukti"'], false);
        $response->assertSeeInOrder(['#pengalaman', '#pendidikan', '#bukti']);
    }

    public function test_owner_can_save_edit_and_clear_project_outcomes(): void
    {
        $this->actingAs(User::factory()->create());
        $attributes = [
            'title' => 'Admin Workspace',
            'category' => 'After-sales',
            'summary' => 'Pencatatan komplain dan retur.',
            'role' => 'Merancang alur dan menguji aplikasi.',
            'status' => 'published',
            'outcome' => 'Pencatatan komplain terpusat di satu dashboard.',
        ];

        $response = $this->post(route('admin.works.store'), $attributes);

        $work = WorkItem::query()->sole();
        $response->assertRedirect(route('admin.works.edit', $work));
        $this->assertSame($attributes['outcome'], $work->outcome);
        $this->get(route('home'))->assertSee($attributes['outcome']);
        $this->get(route('work.show', $work))->assertSee('Hasil proyek')->assertSee($attributes['outcome']);
        $this->get(route('admin.works.edit', $work))->assertSee($attributes['outcome']);

        $this->put(route('admin.works.update', $work), [...$attributes, 'outcome' => 'Riwayat tindak lanjut mudah dipantau.'])->assertSessionHasNoErrors();
        $this->assertSame('Riwayat tindak lanjut mudah dipantau.', $work->fresh()->outcome);

        $this->put(route('admin.works.update', $work), [...$attributes, 'outcome' => ''])->assertSessionHasNoErrors();
        $this->assertNull($work->fresh()->outcome);
        $this->get(route('work.show', $work))->assertDontSee('Hasil proyek');
    }

    /** @return array<string, array{mixed, string}> */
    public static function invalidOutcomes(): array
    {
        return [
            'too long' => [str_repeat('a', 501), 'Hasil proyek maksimal 500 karakter.'],
            'invalid type' => [['unexpected'], 'Hasil proyek harus berupa teks.'],
        ];
    }

    #[DataProvider('invalidOutcomes')]
    public function test_invalid_outcomes_are_rejected_without_changing_project(mixed $outcome, string $message): void
    {
        $work = WorkItem::factory()->create(['outcome' => 'Hasil sebelumnya.']);

        $response = $this->actingAs(User::factory()->create())->put(route('admin.works.update', $work), [
            'title' => $work->title,
            'category' => $work->category,
            'summary' => $work->summary,
            'role' => $work->role,
            'status' => 'published',
            'outcome' => $outcome,
        ]);

        $response->assertSessionHasErrors(['outcome' => $message]);
        $this->assertSame('Hasil sebelumnya.', $work->fresh()->outcome);
    }

    public function test_project_outcome_text_is_escaped(): void
    {
        $work = WorkItem::factory()->published()->create(['outcome' => '<script>alert(1)</script>']);

        $this->get(route('home'))->assertSee($work->outcome)->assertDontSee($work->outcome, false);
        $this->get(route('work.show', $work))->assertSee($work->outcome)->assertDontSee($work->outcome, false);
    }

    public function test_content_migration_corrects_saved_copy_and_preserves_contact_education_and_employment_dates(): void
    {
        $profile = SiteProfile::current();
        $preserved = [
            'email' => 'fresstheend@gmail.com',
            'education' => [['institution' => 'Universitas Teknologi Muhammadiyah', 'program' => 'Sistem Teknologi Informasi', 'period' => '2024 – sekarang']],
            'certifications' => [['name' => 'Python Fundamental', 'year' => '2025']],
        ];
        $profile->update([
            'photo_alt' => 'China',
            'photo_path' => 'profile/portrait.jpg',
            'content' => [
                ...$preserved,
                'lead' => 'Pengembangan dengan bantuan AI.',
                'about' => 'Tentang saya dengan bantuan AI.',
                'socials' => [['platform' => 'linkedin', 'label' => 'Linkedln', 'url' => 'https://www.linkedin.com/in/tian-maysa/']],
                'experiences' => [['organization' => 'Tarmoc', 'role' => 'Technical Support', 'period' => 'Maret 2024 – sekarang', 'points' => ['Mengembangkan dengan bantuan AI.']]],
                'skills' => [
                    ...array_slice(config('portfolio.skills'), 0, 5),
                    ['name' => 'Digital Solutions & AI', 'items' => ['Pengembangan aplikasi internal dengan bantuan AI']],
                    ['name' => 'Analisis & Digitalisasi Proses', 'items' => ['Pemetaan alur kerja after-sales']],
                ],
            ],
        ]);
        $works = WorkItem::factory()->published()->count(4)->sequence(
            ['title' => 'Katalog Digital Produk TARMOC', 'period' => 'Oktober 2026'],
            ['title' => 'Admin Workspaces - Manajemen Komplain dan Retur', 'period' => '2026'],
            ['title' => 'Garansi System - Pengelolaan Klaim Garansi', 'period' => null],
            ['title' => 'Konten Workspaces - Produksi Konten', 'period' => null, 'summary' => 'Produksi konten dengan metodelogi Agile Scrum.'],
        )->create(['role' => 'Mengembangkan dengan bantuan AI.']);
        $migration = require database_path('migrations/2026_10_15_160247_revise_portfolio_content.php');

        $migration->up();

        $content = $profile->fresh()->content;
        foreach ($preserved as $key => $value) {
            $this->assertSame($value, $content[$key]);
        }
        $this->assertSame('Maret 2024 – sekarang', $content['experiences'][0]['period']);
        $this->assertSame('https://www.linkedin.com/in/tian-maysa/', $content['socials'][0]['url']);
        $this->assertSame('LinkedIn', $content['socials'][0]['label']);
        $this->assertSame('Foto Tian Maysa', $profile->fresh()->photo_alt);
        $this->assertCount(6, $content['skills']);
        foreach ($works as $work) {
            $this->assertSame('2026', $work->fresh()->period);
            $this->assertNotEmpty($work->fresh()->outcome);
        }

        $response = $this->get(route('home'));

        $response->assertSee('Admin Workspace - Manajemen Komplain dan Retur');
        $response->assertSee('Konten Workspace - Produksi Konten');
        $response->assertSee('metodologi Agile Scrum');
        $response->assertSee('alt="Foto Tian Maysa"', false);
        $response->assertSee('Python dasar');
        $response->assertSee('Prompt engineering & requirement specification');
        $response->assertDontSee('Workspaces');
        $response->assertDontSee('metodelogi');
        $response->assertDontSee('Linkedln');
        $this->assertSame(0, substr_count($response->getContent(), 'dengan bantuan AI'));
    }
}
