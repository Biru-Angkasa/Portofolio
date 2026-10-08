<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminWorkTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.works.index'))->assertRedirect(route('login'));
        $this->post(route('admin.works.store'), [])->assertRedirect(route('login'));
    }

    public function test_login_rejects_a_wrong_password_and_accepts_the_owner(): void
    {
        User::factory()->create([
            'email' => 'owner@example.com',
            'password' => 'secret-secret-12',
        ]);

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'wrong-password-1',
        ])->assertSessionHasErrors('email');

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'secret-secret-12',
        ])->assertRedirect(route('admin.works.index'));
    }

    public function test_draft_is_hidden_and_published_work_is_visible(): void
    {
        $draft = WorkItem::factory()->create(['title' => 'Catatan draf pribadi']);
        $published = WorkItem::factory()->published()->create(['title' => 'Dokumentasi ODP']);

        $this->get(route('home'))
            ->assertSee('Dokumentasi ODP')
            ->assertDontSee('Catatan draf pribadi');

        $this->get(route('work.show', $draft))->assertNotFound();
        $this->get(route('work.show', $published))->assertSee('Dokumentasi ODP');
    }

    public function test_owner_can_upload_an_image_and_replacing_the_profile_photo_deletes_the_old_file(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();

        $this->actingAs($owner)->from(route('admin.works.index'))->post(route('admin.profile.update'), [
            'photo' => UploadedFile::fake()->image('tian.jpg', 600, 800),
            'alt' => 'Potret Tian Maysa',
        ])->assertRedirect(route('admin.works.index'))->assertSessionHas('status', 'Foto profil disimpan.');

        $this->get(route('admin.works.index'))
            ->assertSee('Foto profil disimpan.')
            ->assertSee('Lihat hasil di portofolio')
            ->assertSee(route('home'), false);

        $firstPath = SiteProfile::current()->photo_path;
        Storage::disk('public')->assertExists($firstPath);

        $this->actingAs($owner)->post(route('admin.profile.update'), [
            'photo' => UploadedFile::fake()->image('tian-baru.png', 600, 800),
            'alt' => 'Potret Tian Maysa',
        ]);

        Storage::disk('public')->assertMissing($firstPath);
        $secondPath = SiteProfile::current()->photo_path;

        $this->get('http://127.0.0.1:8001/')
            ->assertSee('http://127.0.0.1:8001/storage/'.$secondPath, false)
            ->assertDontSee('/storage/'.$firstPath, false)
            ->assertSee('Potret Tian Maysa')
            ->assertSee('property="og:image"', false)
            ->assertDontSee('Monogram Tian Maysa', false);
    }

    public function test_profile_photo_can_be_removed(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();

        $this->actingAs($owner)->post(route('admin.profile.update'), [
            'photo' => UploadedFile::fake()->image('tian.jpg'),
            'alt' => 'Potret Tian Maysa',
        ]);

        $path = SiteProfile::current()->photo_path;

        $this->actingAs($owner)->delete(route('admin.profile.destroy'));

        Storage::disk('public')->assertMissing($path);
        $this->get(route('home'))->assertDontSee('property="og:image"', false);
    }

    #[DataProvider('rejectedProfileUploads')]
    public function test_profile_upload_rejects_disallowed_files(string $name, string $contents): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)->post(route('admin.profile.update'), [
            'photo' => UploadedFile::fake()->createWithContent($name, $contents),
            'alt' => 'Potret Tian Maysa',
        ])->assertSessionHasErrors('photo');
    }

    public function test_https_link_is_published_and_plain_http_is_rejected(): void
    {
        $owner = User::factory()->create();
        $work = WorkItem::factory()->published()->create();

        $this->actingAs($owner)->post(route('admin.media.store', $work), [
            'kind' => 'link',
            'label' => 'Video lapangan',
            'url' => 'http://example.com/video',
        ])->assertSessionHasErrors('url');

        $this->actingAs($owner)->post(route('admin.media.store', $work), [
            'kind' => 'link',
            'label' => 'Video lapangan',
            'url' => 'https://example.com/video',
        ])->assertRedirect();

        $this->get(route('work.show', $work))
            ->assertSee('https://example.com/video', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_deleting_work_removes_its_uploaded_file(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $work = WorkItem::factory()->create();

        $this->actingAs($owner)->post(route('admin.media.store', $work), [
            'kind' => 'image',
            'label' => 'Jalur ODP',
            'file' => UploadedFile::fake()->image('odp.jpg'),
        ])->assertRedirect();

        $path = $work->media()->first()->path;
        Storage::disk('public')->assertExists($path);

        $this->actingAs($owner)->delete(route('admin.works.destroy', $work));

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('work_items', ['id' => $work->id]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function rejectedProfileUploads(): array
    {
        return [
            'svg' => ['face.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'],
            'html' => ['page.html', '<html><script>alert(1)</script></html>'],
            'exe' => ['tool.exe', 'MZ not a photo'],
        ];
    }
}
