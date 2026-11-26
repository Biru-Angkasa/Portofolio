<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SocialLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_has_accessible_instagram_icon_and_keeps_email_button(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="mailto:fresstheend@gmail.com" class="portfolio-button', false)
            ->assertSee('aria-label="Instagram @yaanmys"', false)
            ->assertSee('data-social-icon="instagram"', false)
            ->assertSee('href="https://www.instagram.com/yaanmys/"', false)
            ->assertSee('data-social-link="whatsapp"', false)
            ->assertSee('data-social-link="linkedin"', false)
            ->assertSee('data-social-link="github"', false)
            ->assertDontSee('data-social-link="facebook"', false);
    }

    public function test_admin_offers_platform_choices_and_blank_whatsapp_and_facebook_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.content.edit', 'kontak'))
            ->assertOk()
            ->assertSee('Ikon media sosial')
            ->assertSee('WhatsApp')
            ->assertSee('Facebook')
            ->assertSee('LinkedIn')
            ->assertSee('TikTok')
            ->assertSee('Tautan yang kosong tidak ditampilkan');
    }

    public function test_owner_can_save_icons_and_links_and_keep_empty_accounts_hidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'kontak'), ['content' => [
                'email' => 'tian@example.com',
                'socials' => [
                    ['platform' => 'whatsapp', 'label' => 'WhatsApp Tian', 'url' => 'https://wa.me/628123456789'],
                    ['platform' => 'facebook', 'label' => 'Facebook Tian', 'url' => 'https://facebook.com/example'],
                    ['platform' => 'youtube', 'label' => 'YouTube belum diisi', 'url' => ''],
                ],
            ]])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.content.edit', 'kontak'));

        $this->assertSame('whatsapp', SiteProfile::current()->content['socials'][0]['platform']);
        $this->get(route('home'))
            ->assertSee('data-social-icon="whatsapp"', false)
            ->assertSee('data-social-icon="facebook"', false)
            ->assertSee('href="https://wa.me/628123456789"', false)
            ->assertSee('href="mailto:tian@example.com"', false)
            ->assertDontSee('YouTube belum diisi')
            ->assertDontSee('data-social-link="youtube"', false);
    }

    /** @return array<string, array{string, string}> */
    public static function oldSocialLinks(): array
    {
        return [
            'Instagram' => ['https://www.instagram.com/tian/', 'instagram'],
            'WhatsApp' => ['https://wa.me/628123456789', 'whatsapp'],
            'Facebook mobile' => ['https://m.facebook.com/tian', 'facebook'],
            'LinkedIn' => ['https://www.linkedin.com/in/tian', 'linkedin'],
            'GitHub' => ['https://github.com/tian', 'github'],
            'YouTube' => ['https://youtu.be/example', 'youtube'],
            'Twitter' => ['https://twitter.com/tian', 'x'],
            'TikTok' => ['https://www.tiktok.com/@tian', 'tiktok'],
            'Telegram' => ['https://t.me/tian', 'telegram'],
            'Custom site' => ['https://example.com/', 'website'],
            'Unrelated domain' => ['https://instagram.com.example.com/', 'website'],
        ];
    }

    #[DataProvider('oldSocialLinks')]
    public function test_existing_links_get_the_correct_icon_without_reentering_data(string $url, string $platform): void
    {
        SiteProfile::current()->update(['content' => ['socials' => [['label' => 'Akun Tian', 'url' => $url]]]]);
        $this->get(route('home'))->assertOk()->assertSee('data-social-icon="'.$platform.'"', false)->assertSee('aria-label="Akun Tian"', false);
    }

    public function test_explicit_icon_selection_works_with_a_custom_link(): void
    {
        SiteProfile::current()->update(['content' => ['socials' => [['platform' => 'instagram', 'label' => 'Instagram Tian', 'url' => 'https://example.com/redirect']]]]);
        $this->get(route('home'))->assertSee('data-social-icon="instagram"', false);
    }

    public function test_invalid_icons_and_active_links_cannot_be_saved(): void
    {
        $this->actingAs(User::factory()->create());
        $this->put(route('admin.content.update', 'kontak'), ['content' => [
            'email' => 'tian@example.com',
            'socials' => [['platform' => '<script>', 'label' => 'Akun', 'url' => 'https://example.com/']],
        ]])->assertSessionHasErrors('content.socials.0.platform');

        $this->put(route('admin.content.update', 'kontak'), ['content' => [
            'email' => 'tian@example.com',
            'socials' => [['platform' => 'facebook', 'label' => 'Akun', 'url' => 'javascript:alert(1)']],
        ]])->assertSessionHasErrors('content.socials.0.url');
        $this->assertNull(SiteProfile::current()->content);
    }

    /** @return array<string, array{string, string}> */
    public static function whatsAppNumbers(): array
    {
        return [
            'Indonesian local number' => ['081234567890', '6281234567890'],
            'International number' => ['6281234567890', '6281234567890'],
            'Plus and spaces' => ['+62 812 3456 7890', '6281234567890'],
            'Local formatted number' => ['0812-3456-7890', '6281234567890'],
            'Parentheses' => ['+62 (812) 3456-7890', '6281234567890'],
            'Other country code' => ['+1 202 555 0123', '12025550123'],
        ];
    }

    #[DataProvider('whatsAppNumbers')]
    public function test_whatsapp_numbers_are_saved_as_clickable_links_and_editable_as_numbers(string $input, string $number): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'kontak'), ['content' => [
                'email' => 'tian@example.com',
                'socials' => [['platform' => 'whatsapp', 'label' => 'WhatsApp Tian', 'url' => $input]],
            ]])->assertSessionHasNoErrors();

        $this->assertSame('https://wa.me/'.$number, SiteProfile::current()->content['socials'][0]['url']);
        $this->get(route('home'))->assertSee('href="https://wa.me/'.$number.'"', false)->assertSee('data-social-icon="whatsapp"', false);
        $this->get(route('admin.content.edit', 'kontak'))->assertOk()->assertSee('Nomor WhatsApp')->assertSee('value="'.$number.'"', false)->assertDontSee('value="https://wa.me/'.$number.'"', false);
    }

    public function test_automatic_icon_selection_recognizes_a_whatsapp_number(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'kontak'), ['content' => [
                'email' => 'tian@example.com',
                'socials' => [['platform' => 'auto', 'label' => 'WhatsApp Tian', 'url' => '081234567890']],
            ]])->assertSessionHasNoErrors();
        $this->assertSame('whatsapp', SiteProfile::current()->content['socials'][0]['platform']);
        $this->get(route('home'))->assertSee('href="https://wa.me/6281234567890"', false);
    }

    /** @return array<string, array{string}> */
    public static function invalidWhatsAppNumbers(): array
    {
        return [
            'Too short' => ['0812'],
            'Too long' => ['62812345678901234567890'],
            'Letters in number' => ['0812abc34567890'],
            'Script' => ['javascript:alert(1)'],
            'Repeated plus sign' => ['++6281234567890'],
        ];
    }

    #[DataProvider('invalidWhatsAppNumbers')]
    public function test_invalid_whatsapp_numbers_are_rejected(string $number): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'kontak'), ['content' => [
                'email' => 'tian@example.com',
                'socials' => [['platform' => 'whatsapp', 'label' => 'WhatsApp Tian', 'url' => $number]],
            ]])->assertSessionHasErrors(['content.socials.0.url' => 'Masukkan nomor WhatsApp yang valid, misalnya 081234567890 atau +6281234567890.']);
        $this->assertNull(SiteProfile::current()->content);
    }
}
