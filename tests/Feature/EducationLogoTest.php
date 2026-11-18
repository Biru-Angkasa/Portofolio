<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EducationLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_upload_education_logos(): void
    {
        Storage::fake('public');

        $this->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo' => UploadedFile::fake()->image('logo.png')]),
        ]]])->assertRedirect(route('login'));

        Storage::disk('public')->assertDirectoryEmpty('education-logos');
    }

    public function test_owner_can_upload_a_clickable_logo_and_edit_it_again_without_reuploading(): void
    {
        Storage::fake('public');
        $profile = SiteProfile::current();
        $profile->update(['content' => ['name' => 'Nama tersimpan']]);
        $this->actingAs(User::factory()->create());

        $this->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo' => UploadedFile::fake()->image('logo.png'), 'url' => 'https://campus.example.com/']),
        ]]])->assertSessionHasNoErrors()->assertRedirect(route('admin.content.edit', 'pendidikan'));

        $education = $profile->fresh()->content['education'][0];
        Storage::disk('public')->assertExists(substr($education['logo_path'], strlen('storage/')));
        $this->assertSame('Nama tersimpan', $profile->fresh()->content['name']);
        $this->assertFalse(array_key_exists('logo', $education));
        $this->get(route('home'))->assertSee('src="'.url('/'.$education['logo_path']).'"', false)
            ->assertSee('href="https://campus.example.com/"', false)
            ->assertSee('Kunjungi situs Kampus contoh (tab baru)');
        $this->get(route('admin.content.edit', 'pendidikan'))->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="content[education][0][logo]"', false)
            ->assertSee('data-logo-preview', false);

        $education['period'] = '2025 – sekarang';
        $this->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [$education]]])->assertSessionHasNoErrors();

        $this->assertSame($education['logo_path'], $profile->fresh()->content['education'][0]['logo_path']);
        Storage::disk('public')->assertExists(substr($education['logo_path'], strlen('storage/')));
    }

    public function test_replacing_a_logo_removes_only_its_previous_upload(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('education-logos/old.png', 'old-logo');
        Storage::disk('public')->put('education-logos/other.png', 'other-logo');
        $profile = SiteProfile::current();
        $other = $this->educationRow(['institution' => 'Sekolah lain', 'logo_path' => 'storage/education-logos/other.png']);
        $profile->update(['content' => ['education' => [
            $this->educationRow(['logo_path' => 'storage/education-logos/old.png']), $other,
        ]]]);

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo_path' => 'storage/education-logos/old.png', 'logo' => UploadedFile::fake()->image('new.png')]), $other,
        ]]])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('education-logos/old.png');
        Storage::disk('public')->assertExists(['education-logos/other.png', substr($profile->fresh()->content['education'][0]['logo_path'], strlen('storage/'))]);
        $this->assertSame($other['logo_path'], $profile->fresh()->content['education'][1]['logo_path']);
    }

    public function test_reordering_education_keeps_each_logo_with_its_institution(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('education-logos/first.png', 'first');
        Storage::disk('public')->put('education-logos/second.png', 'second');
        $first = $this->educationRow(['institution' => 'Pertama', 'logo_path' => 'storage/education-logos/first.png']);
        $second = $this->educationRow(['institution' => 'Kedua', 'logo_path' => 'storage/education-logos/second.png']);
        $profile = SiteProfile::current();
        $profile->update(['content' => ['education' => [$first, $second]]]);

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [8 => $second, 2 => $first]]])->assertSessionHasNoErrors();

        $this->assertSame([$second, $first], $profile->fresh()->content['education']);
        Storage::disk('public')->assertExists(['education-logos/first.png', 'education-logos/second.png']);
    }

    public function test_clearing_the_logo_keeps_education_and_deletes_the_upload(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('education-logos/old.png', 'old-logo');
        $profile = SiteProfile::current();
        $profile->update(['content' => ['education' => [$this->educationRow(['logo_path' => 'storage/education-logos/old.png'])]]]);

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo_path' => '', 'url' => 'https://campus.example.com/']),
        ]]])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('education-logos/old.png');
        $this->get(route('home'))->assertSee('Kampus contoh')->assertDontSee('Logo Kampus contoh')->assertDontSee('href="https://campus.example.com/"', false);
    }

    public function test_removing_education_deletes_its_uploaded_logo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('education-logos/old.png', 'old-logo');
        $profile = SiteProfile::current();
        $profile->update(['content' => ['education' => [$this->educationRow(['logo_path' => 'storage/education-logos/old.png'])]]]);

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => []]])->assertSessionHasNoErrors();

        $this->assertSame([], $profile->fresh()->content['education']);
        Storage::disk('public')->assertMissing('education-logos/old.png');
    }

    public function test_a_logo_without_a_destination_is_shown_without_a_link(): void
    {
        SiteProfile::current()->update(['content' => ['education' => [$this->educationRow(['logo_path' => 'images/education/utmj.png'])]]]);

        $this->get(route('home'))->assertSee('Logo Kampus contoh')->assertDontSee('Kunjungi situs Kampus contoh (tab baru)');
    }

    public function test_unsafe_logo_links_are_rejected_before_files_are_stored(): void
    {
        Storage::fake('public');
        $profile = SiteProfile::current();

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo' => UploadedFile::fake()->image('logo.png'), 'url' => 'javascript:alert(1)']),
        ]]])->assertSessionHasErrors('content.education.0.url');

        $this->assertNull($profile->fresh()->content);
        Storage::disk('public')->assertDirectoryEmpty('education-logos');
    }

    public function test_a_forged_existing_logo_path_is_rejected(): void
    {
        $profile = SiteProfile::current();

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo_path' => '../private/file.png']),
        ]]])->assertSessionHasErrors('content.education.0.logo_path');

        $this->assertNull($profile->fresh()->content);
    }

    /** @return array<string, array{string, string}> */
    public static function invalidLogos(): array
    {
        return [
            'script' => ['logo.php', '<?php echo "unsafe";'],
            'svg' => ['logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
            'renamed script' => ['logo.png', '<?php echo "unsafe";'],
        ];
    }

    #[DataProvider('invalidLogos')]
    public function test_non_image_logo_uploads_are_rejected(string $filename, string $contents): void
    {
        Storage::fake('public');
        $profile = SiteProfile::current();
        $file = UploadedFile::fake()->createWithContent($filename, $contents);
        $upload = new UploadedFile($file->getPathname(), $filename, null, null, true);

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo' => $upload]),
        ]]])->assertSessionHasErrors('content.education.0.logo');

        $this->assertNull($profile->fresh()->content);
        Storage::disk('public')->assertDirectoryEmpty('education-logos');
    }

    public function test_logo_uploads_larger_than_two_megabytes_are_rejected(): void
    {
        Storage::fake('public');
        $profile = SiteProfile::current();

        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'pendidikan'), ['content' => ['education' => [
            $this->educationRow(['logo' => UploadedFile::fake()->image('large.png')->size(2049)]),
        ]]])->assertSessionHasErrors(['content.education.0.logo' => 'Ukuran logo maksimal 2 MB.']);

        $this->assertNull($profile->fresh()->content);
        Storage::disk('public')->assertDirectoryEmpty('education-logos');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function educationRow(array $attributes = []): array
    {
        return array_replace(['institution' => 'Kampus contoh', 'program' => 'Teknologi informasi', 'period' => '2024 – sekarang'], $attributes);
    }
}
