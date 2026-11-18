<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use App\Models\User;
use App\ProfilePhotoCropper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ProfilePhotoCropTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_read_originals_or_submit_a_crop(): void
    {
        $this->get(route('admin.profile.original'))->assertRedirect(route('login'));
        $this->post(route('admin.profile.update'), ['use_existing_photo' => 1, 'alt' => 'Potret', 'crop' => ['x' => 0, 'y' => 0, 'zoom' => 2]])->assertRedirect(route('login'));
    }

    public function test_both_photo_editors_offer_the_same_crop_controls(): void
    {
        $this->actingAs(User::factory()->create());

        foreach ([route('admin.works.index'), route('admin.content.edit', 'beranda')] as $url) {
            $this->get($url)->assertSee('data-photo-crop', false)->assertSee('data-crop-stage', false)
                ->assertSee('Geser kiri–kanan')->assertSee('Geser atas–bawah')->assertSee('Reset posisi');
        }
    }

    public function test_upload_saves_the_selected_pixels_and_keeps_the_original_private(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $photo = $this->coloredPhoto();
        $original = $photo->get();
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.profile.update'), ['photo' => $photo, 'alt' => 'Potret baru', 'crop' => ['x' => 0, 'y' => 0, 'zoom' => 2]])
            ->assertSessionHasNoErrors()->assertSessionHas('status', 'Foto profil disimpan.');

        $profile = SiteProfile::current();
        $this->assertSame(['x' => 0, 'y' => 0, 'zoom' => 2], $profile->photo_crop);
        $output = imagecreatefromstring(Storage::disk('public')->get($profile->photo_path));
        $this->assertSame(200, imagesx($output));
        $this->assertSame(200, imagesy($output));
        $this->assertSame(0xFF0000, imagecolorat($output, 100, 100) & 0xFFFFFF);
        Storage::disk('local')->assertExists($profile->photo_original_path);
        Storage::disk('public')->assertMissing($profile->photo_original_path);
        $this->get(route('admin.profile.original'))->assertStreamedContent($original);
        $this->get(route('home'))->assertSee('src="'.$profile->photoUrl().'"', false)->assertDontSee($profile->photo_original_path, false);
    }

    public function test_existing_crop_can_be_changed_using_pixels_outside_the_previous_crop(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.profile.update'), ['photo' => $this->coloredPhoto(), 'alt' => 'Potret', 'crop' => ['x' => 0, 'y' => 0, 'zoom' => 2]])->assertSessionHasNoErrors();
        $before = SiteProfile::current();

        $this->post(route('admin.profile.update'), ['use_existing_photo' => 1, 'alt' => 'Potret diatur ulang', 'crop' => ['x' => 1, 'y' => 1, 'zoom' => 2]])->assertSessionHasNoErrors();

        $after = $before->fresh();
        $this->assertSame($before->photo_original_path, $after->photo_original_path);
        $output = imagecreatefromstring(Storage::disk('public')->get($after->photo_path));
        $this->assertSame(0xFFFF00, imagecolorat($output, 100, 100) & 0xFFFFFF);
        Storage::disk('public')->assertMissing($before->photo_path);
        Storage::disk('local')->assertExists($before->photo_original_path);
    }

    public function test_photos_uploaded_before_the_crop_feature_can_be_edited(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('profile/legacy.png', $this->coloredPhoto()->get());
        $profile = SiteProfile::current();
        $profile->update(['photo_path' => 'profile/legacy.png', 'photo_alt' => 'Lama']);
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.profile.original'))->assertStreamedContent(Storage::disk('public')->get('profile/legacy.png'));

        $this->post(route('admin.profile.update'), ['use_existing_photo' => 1, 'alt' => 'Diatur ulang', 'crop' => ['x' => 1, 'y' => 1, 'zoom' => 2]])->assertSessionHasNoErrors();

        $profile->refresh();
        Storage::disk('local')->assertExists($profile->photo_original_path);
        Storage::disk('public')->assertMissing('profile/legacy.png');
        $output = imagecreatefromstring(Storage::disk('public')->get($profile->photo_path));
        $this->assertSame(0xFFFF00, imagecolorat($output, 100, 100) & 0xFFFFFF);
    }

    public function test_replacing_and_removing_a_photo_cleans_up_its_public_and_private_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs(User::factory()->create());
        $this->post(route('admin.profile.update'), ['photo' => $this->coloredPhoto(), 'alt' => 'Pertama', 'crop' => ['x' => 0, 'y' => 0, 'zoom' => 2]])->assertSessionHasNoErrors();
        $first = SiteProfile::current();

        $this->post(route('admin.profile.update'), ['photo' => $this->coloredPhoto(), 'alt' => 'Kedua', 'crop' => ['x' => 1, 'y' => 1, 'zoom' => 2]])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($first->photo_path);
        Storage::disk('local')->assertMissing($first->photo_original_path);
        $second = $first->fresh();
        Storage::disk('public')->assertExists($second->photo_path);
        Storage::disk('local')->assertExists($second->photo_original_path);

        $this->delete(route('admin.profile.destroy'))->assertRedirect();

        Storage::disk('public')->assertMissing($second->photo_path);
        Storage::disk('local')->assertMissing($second->photo_original_path);
        $this->assertNull($second->fresh()->photo_path);
        $this->assertNull($second->fresh()->photo_original_path);
        $this->assertNull($second->fresh()->photo_crop);
        $this->get(route('admin.profile.original'))->assertNotFound();
    }

    /** @return array<string, array{array<string, mixed>, string}> */
    public static function invalidCrops(): array
    {
        return [
            'negative x' => [['x' => -0.1, 'y' => 0.5, 'zoom' => 1], 'crop.x'],
            'x beyond image' => [['x' => 1.1, 'y' => 0.5, 'zoom' => 1], 'crop.x'],
            'y beyond image' => [['x' => 0.5, 'y' => 1.1, 'zoom' => 1], 'crop.y'],
            'zoom out beyond image' => [['x' => 0.5, 'y' => 0.5, 'zoom' => 0.9], 'crop.zoom'],
            'excessive zoom' => [['x' => 0.5, 'y' => 0.5, 'zoom' => 3.1], 'crop.zoom'],
            'invalid coordinate' => [['x' => 'invalid', 'y' => 0.5, 'zoom' => 1], 'crop.x'],
            'missing zoom' => [['x' => 0.5, 'y' => 0.5], 'crop.zoom'],
            'unexpected path' => [['x' => 0.5, 'y' => 0.5, 'zoom' => 1, 'path' => '../other.png'], 'crop'],
        ];
    }

    /** @param array<string, mixed> $crop */
    #[DataProvider('invalidCrops')]
    public function test_invalid_crops_preserve_the_existing_photo(array $crop, string $field): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('profile/existing.png', $this->coloredPhoto()->get());
        $profile = SiteProfile::current();
        $profile->update(['photo_path' => 'profile/existing.png']);

        $this->actingAs(User::factory()->create())->post(route('admin.profile.update'), ['photo' => $this->coloredPhoto(), 'alt' => 'Baru', 'crop' => $crop])->assertSessionHasErrors($field);

        $this->assertSame('profile/existing.png', $profile->fresh()->photo_path);
        Storage::disk('public')->assertExists('profile/existing.png');
        Storage::disk('local')->assertDirectoryEmpty('profile-originals');
    }

    public function test_existing_photo_mode_requires_a_photo_and_complete_crop_settings(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.profile.update'), ['use_existing_photo' => 1, 'alt' => 'Potret'])->assertSessionHasErrors('crop');
        $this->post(route('admin.profile.update'), ['use_existing_photo' => 1, 'alt' => 'Potret', 'crop' => ['x' => 0.5, 'y' => 0.5, 'zoom' => 1]])->assertSessionHasErrors('photo');
    }

    public function test_oversized_image_dimensions_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())->post(route('admin.profile.update'), ['photo' => UploadedFile::fake()->image('wide.png', 4097, 10), 'alt' => 'Potret'])
            ->assertSessionHasErrors(['photo' => 'Ukuran foto maksimal 4096 × 4096 piksel.']);
    }

    public function test_processing_failure_preserves_the_previous_photo_and_cleans_new_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('profile/previous.png', $this->coloredPhoto()->get());
        $profile = SiteProfile::current();
        $profile->update(['photo_path' => 'profile/previous.png']);
        $this->mock(ProfilePhotoCropper::class)->shouldReceive('render')->once()->andThrow(new RuntimeException('Crop failed'));
        Exceptions::fake();

        $this->actingAs(User::factory()->create())->post(route('admin.profile.update'), ['photo' => $this->coloredPhoto(), 'alt' => 'Baru', 'crop' => ['x' => 0.5, 'y' => 0.5, 'zoom' => 2]])->assertServerError();

        $this->assertSame('profile/previous.png', $profile->fresh()->photo_path);
        Storage::disk('public')->assertExists('profile/previous.png');
        Storage::disk('local')->assertDirectoryEmpty('profile-originals');
        Exceptions::assertReported(RuntimeException::class);
    }

    /** @return array<string, array{int, array{int, int, int}}> */
    public static function phoneOrientations(): array
    {
        return [
            'normal' => [1, [0, 0, 255]],
            'mirror horizontal' => [2, [255, 0, 0]],
            'rotate 180' => [3, [0, 255, 0]],
            'mirror vertical' => [4, [255, 255, 0]],
            'transpose' => [5, [0, 255, 0]],
            'rotate clockwise' => [6, [255, 0, 0]],
            'transverse' => [7, [0, 0, 255]],
            'rotate anticlockwise' => [8, [255, 255, 0]],
        ];
    }

    /** @param array{int, int, int} $expectedColor */
    #[DataProvider('phoneOrientations')]
    public function test_phone_photo_orientation_matches_the_crop_preview(int $orientation, array $expectedColor): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $square = imagecreatefromstring($this->coloredPhoto()->get());
        $landscape = imagecreatetruecolor(400, 200);
        imagecopyresampled($landscape, $square, 0, 0, 0, 0, 400, 200, 400, 400);
        ob_start();
        imagejpeg($landscape, null, 100);
        $jpeg = (string) ob_get_clean();
        $exif = "Exif\x00\x00II\x2a\x00\x08\x00\x00\x00\x01\x00\x12\x01\x03\x00\x01\x00\x00\x00".pack('v', $orientation)."\x00\x00\x00\x00\x00\x00";
        $jpeg = substr($jpeg, 0, 2)."\xff\xe1".pack('n', strlen($exif) + 2).$exif.substr($jpeg, 2);

        $this->actingAs(User::factory()->create())->post(route('admin.profile.update'), ['photo' => UploadedFile::fake()->createWithContent('phone.jpg', $jpeg), 'alt' => 'Potret', 'crop' => ['x' => 1, 'y' => 0, 'zoom' => 2]])->assertSessionHasNoErrors();

        $output = imagecreatefromstring(Storage::disk('public')->get(SiteProfile::current()->photo_path));
        $color = imagecolorat($output, 50, 50);
        $actualColor = array_map(fn (int $channel): int => $channel < 128 ? 0 : 255, [($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF]);
        $this->assertSame($expectedColor, $actualColor);
    }

    private function coloredPhoto(): UploadedFile
    {
        $image = imagecreatetruecolor(400, 400);
        imagefilledrectangle($image, 0, 0, 199, 199, imagecolorallocate($image, 255, 0, 0));
        imagefilledrectangle($image, 200, 0, 399, 199, imagecolorallocate($image, 0, 0, 255));
        imagefilledrectangle($image, 0, 200, 199, 399, imagecolorallocate($image, 0, 255, 0));
        imagefilledrectangle($image, 200, 200, 399, 399, imagecolorallocate($image, 255, 255, 0));
        ob_start();
        imagepng($image);
        $contents = (string) ob_get_clean();

        return UploadedFile::fake()->createWithContent('photo.png', $contents);
    }
}
