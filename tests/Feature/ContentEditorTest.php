<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\SiteProfile;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_read_or_change_the_editor(): void
    {
        $this->get(route('admin.content.index'))->assertRedirect(route('login'));
        $this->get(route('admin.content.edit', 'beranda'))->assertRedirect(route('login'));
        $this->put(route('admin.content.update', 'beranda'), ['content' => ['name' => 'Changed']])->assertRedirect(route('login'));
        $this->get(route('home'))->assertSee('Tian Maysa');
    }

    public function test_owner_can_open_all_menu_editors(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['beranda', 'tentang', 'portofolio', 'pendidikan', 'pengalaman', 'blog', 'kontak'] as $section) {
            $this->get(route('admin.content.edit', $section))->assertOk()->assertSee('Simpan perubahan');
        }
    }

    /** @return array<string, array{string, array<string, mixed>, string}> */
    public static function editableSections(): array
    {
        return [
            'home' => ['beranda', ['name' => 'Tian Baru', 'monogram' => 'TB', 'greeting' => 'Salam jaringan', 'role' => 'Network Engineer', 'lead' => 'Perkenalan baru', 'highlights' => [['value' => '100', 'label' => 'Koneksi teruji', 'context' => 'Pengujian lapangan']]], 'Tian Baru'],
            'about' => ['tentang', ['about_title' => 'Tentang Tian', 'about' => 'Kisah baru dari lapangan', 'skills_title' => 'Peralatan saya', 'skills' => [['name' => 'Router', 'items' => "MikroTik\nVLAN"]]], 'Kisah baru dari lapangan'],
            'portfolio' => ['portofolio', ['portfolio_title' => 'Proyek pilihan Tian', 'portfolio_intro' => 'Dokumentasi baru'], 'Proyek pilihan Tian'],
            'education' => ['pendidikan', ['education_title' => 'Belajar sepanjang hayat', 'education' => [['institution' => 'Kampus baru', 'program' => 'Teknologi jaringan', 'period' => '2026']], 'certifications' => [['name' => 'Sertifikasi baru', 'year' => '2026']]], 'Kampus baru'],
            'experience' => ['pengalaman', ['experience_title' => 'Pengalaman baru', 'experiences' => [['organization' => 'Tim baru', 'role' => 'Engineer', 'period' => '2026', 'points' => "Diagnosis\nDokumentasi"]]], 'Tim baru'],
            'blog intro' => ['blog', ['blog_title' => 'Tulisan pribadi', 'blog_intro' => 'Catatan dari Tian'], 'Tulisan pribadi'],
            'contact' => ['kontak', ['contact_title' => 'Kontak baru', 'contact_intro' => 'Sapa saya', 'email' => 'tian@example.com', 'location' => 'Jakarta', 'socials' => [['label' => 'GitHub Tian', 'url' => 'https://github.com/example']]], 'tian@example.com'],
        ];
    }

    /** @param array<string, mixed> $content */
    #[DataProvider('editableSections')]
    public function test_changes_to_each_menu_are_saved_and_visible(string $section, array $content, string $visibleText): void
    {
        BlogPost::factory()->published()->create();

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', $section), ['content' => $content])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.content.edit', $section));

        $this->assertNotNull(SiteProfile::current()->content);
        $this->get(route('home'))->assertOk()->assertSee($visibleText);
    }

    public function test_clearing_a_list_keeps_it_empty_and_preserves_other_sections(): void
    {
        $profile = SiteProfile::current();
        $profile->update(['content' => ['name' => 'Nama tersimpan', 'education' => [['institution' => 'Sekolah lama', 'program' => 'TKJ', 'period' => '2020']]]]);
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'pendidikan'), ['content' => ['education_title' => 'Pendidikan']])
            ->assertSessionHasNoErrors();

        $this->assertSame([], $profile->fresh()->content['education']);
        $this->assertSame('Nama tersimpan', $profile->fresh()->content['name']);
        $this->get(route('home'))->assertSee('Nama tersimpan')->assertDontSee('Sekolah lama')->assertDontSee('Universitas Teknologi Muhammadiyah');
    }

    public function test_list_order_and_newline_items_are_preserved(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'tentang'), ['content' => ['skills' => [
                8 => ['name' => 'Pertama', 'items' => " TCP/IP \n\n VLAN "],
                2 => ['name' => 'Kedua', 'items' => 'Fiber'],
            ]]])
            ->assertSessionHasNoErrors();

        $skills = SiteProfile::current()->content['skills'];
        $this->assertSame(['TCP/IP', 'VLAN'], $skills[0]['items']);
        $this->assertSame('Kedua', $skills[1]['name']);
        $this->get(route('home'))->assertSeeInOrder(['Pertama', 'TCP/IP', 'VLAN', 'Kedua', 'Fiber']);
    }

    public function test_rejects_cross_section_keys_and_javascript_links(): void
    {
        $this->actingAs(User::factory()->create());
        $this->put(route('admin.content.update', 'blog'), ['content' => ['blog_title' => 'Test', 'photo_path' => 'private/file']])->assertSessionHasErrors('content');
        $this->put(route('admin.content.update', 'kontak'), ['content' => ['email' => 'tian@example.com', 'socials' => [['label' => 'Unsafe', 'url' => 'javascript:alert(1)']]]])->assertSessionHasErrors('content.socials.0.url');
        $this->assertNull(SiteProfile::current()->content);
    }

    public function test_rejects_incomplete_rows_and_unknown_menu(): void
    {
        $this->actingAs(User::factory()->create());
        $this->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [['institution' => 'Kampus']]]])->assertSessionHasErrors(['content.education.0.program', 'content.education.0.period']);
        $this->get('/admin/konten/unknown')->assertNotFound();
        $this->put('/admin/konten/unknown')->assertNotFound();
    }

    public function test_user_text_is_escaped_on_home_and_work_detail_pages(): void
    {
        $script = '<script>alert("x")</script>';
        SiteProfile::current()->update(['content' => ['name' => $script, 'about' => $script]]);
        $work = WorkItem::factory()->published()->create();
        $this->get(route('home'))->assertSee($script)->assertDontSee($script, false);
        $this->get(route('work.show', $work))->assertSee($script)->assertDontSee($script, false);
    }
}
