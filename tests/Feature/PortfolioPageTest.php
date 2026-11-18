<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_exposes_a_person_profile_as_valid_json_ld(): void
    {
        $schema = $this->profileSchema($this->get('https://portfolio.example/?source=search'));

        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('ProfilePage', $schema['@type']);
        $this->assertSame('https://portfolio.example', $schema['url']);
        $this->assertSame('Person', $schema['mainEntity']['@type']);
        $this->assertSame('https://portfolio.example#person', $schema['mainEntity']['@id']);
        $this->assertSame('Tian Maysa', $schema['mainEntity']['name']);
        $this->assertSame('Network & Technical Support Engineer', $schema['mainEntity']['jobTitle']);
        $this->assertContains('Konfigurasi dasar MikroTik', $schema['mainEntity']['knowsAbout']);
        $this->assertSame(['https://www.instagram.com/yaannmys/'], $schema['mainEntity']['sameAs']);
        $this->assertArrayNotHasKey('image', $schema['mainEntity']);
    }

    public function test_profile_schema_tracks_saved_content_and_omits_empty_social_profiles(): void
    {
        SiteProfile::current()->update([
            'photo_path' => 'profile/portrait.jpg',
            'content' => [
                'name' => 'Nama Profil Terbaru',
                'role' => 'IT Support Engineer',
                'lead' => 'Menangani dukungan jaringan dan perangkat.',
                'location' => 'Jakarta',
                'skills' => [['name' => 'Jaringan', 'items' => ['VLAN', 'VPN']]],
                'socials' => [
                    ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => ''],
                    ['platform' => 'whatsapp', 'label' => 'WhatsApp', 'url' => 'https://wa.me/628123456789'],
                ],
            ],
        ]);

        $schema = $this->profileSchema($this->get('https://portfolio.example/'));

        $this->assertSame('Nama Profil Terbaru', $schema['mainEntity']['name']);
        $this->assertSame('IT Support Engineer', $schema['mainEntity']['jobTitle']);
        $this->assertSame('Menangani dukungan jaringan dan perangkat.', $schema['mainEntity']['description']);
        $this->assertSame('Jakarta', $schema['mainEntity']['homeLocation']['name']);
        $this->assertSame(['VLAN', 'VPN'], $schema['mainEntity']['knowsAbout']);
        $this->assertSame('https://portfolio.example/storage/profile/portrait.jpg', $schema['mainEntity']['image']);
        $this->assertArrayNotHasKey('sameAs', $schema['mainEntity']);
    }

    public function test_profile_content_cannot_break_out_of_the_json_ld_script(): void
    {
        $name = 'Tian </script><script>alert("profile")</script>';
        SiteProfile::current()->update(['content' => ['name' => $name]]);

        $response = $this->get(route('home'));
        $schema = $this->profileSchema($response);

        $this->assertSame($name, $schema['mainEntity']['name']);
        $response->assertDontSee($name, false);
    }

    public function test_home_is_indexable_with_current_profile_metadata_and_a_canonical_url_without_query_parameters(): void
    {
        $response = $this->get('https://portfolio.example/?source=search');

        $response->assertSee('<title>Web Portofolio Tian Maysa — Network &amp; Technical Support Engineer</title>', false);
        $response->assertSee('<meta name="description" content="Network &amp; Technical Support Engineer di Jakarta. Berpengalaman di MikroTik, fiber optic, after-sales, dan pengembangan aplikasi internal. Lihat portofolio.">', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical" href="https://portfolio.example">', false);
        $response->assertSee('<meta property="og:url" content="https://portfolio.example">', false);
        $response->assertDontSee('content="noindex', false);
    }

    public function test_home_renders_cv_profile_experience_and_contact(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('Tian Maysa');
        $response->assertSee('Network & Technical Support Engineer');
        $response->assertSee('After-sales di Tarmoc.');
        $response->assertSee('Saat PKL fiber di Telkom.');
        $response->assertSee('Tarmoc');
        $response->assertSee('PT Telkom Indonesia');
        $response->assertSee('Mendukung operasional teknis hingga ±50 titik ODP.');
        $response->assertSee('Universitas Teknologi Muhammadiyah');
        $response->assertSee('BNSP Fiber Optik Telkom');
        $response->assertSee('fresstneend@gmail.com');
        $response->assertSee('@yaannmys');
        $response->assertSee('mailto:fresstneend@gmail.com', false);
        $response->assertSee('https://www.instagram.com/yaannmys/', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('<html lang="id"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertDontSee('property="og:image"', false);
        $response->assertSee('aria-expanded="false"', false);
        $response->assertSee('aria-controls="site-nav"', false);
        $response->assertSee('Monogram Tian Maysa', false);
        $response->assertDontSee('/storage/profile', false);
        $response->assertSee('Hubungi saya');
        $response->assertSee('<canvas class="portfolio-threads" data-threads hidden aria-hidden="true">', false);
        $response->assertSee('data-threads-still', false);
        $response->assertDontSee('Bukti kerja');
        $response->assertDontSee('hanya muncul jika berkas');
    }

    public function test_home_offers_cv_download_and_omits_phone_link_and_internal_notes(): void
    {
        $response = $this->get(route('home'));

        $response->assertDontSee('tel:', false);
        $response->assertSee('Unduh CV');
        $response->assertSee('href="'.route('cv.download').'"', false);
        $response->assertDontSee('konfirmasi sebelum publikasi');
        $response->assertDontSee('Bukan total karier');
        $response->assertDontSee('Lingkup pekerjaan');
    }

    public function test_profile_photo_uses_the_request_origin_and_hides_the_monogram(): void
    {
        SiteProfile::current()->update([
            'photo_path' => 'profile/portrait.jpg',
            'photo_alt' => 'Potret Tian Maysa',
        ]);

        $response = $this->get('http://127.0.0.1:8001/');

        $response->assertSee('src="http://127.0.0.1:8001/storage/profile/portrait.jpg"', false);
        $response->assertSee('content="http://127.0.0.1:8001/storage/profile/portrait.jpg"', false);
        $response->assertSee('alt="Potret Tian Maysa"', false);
        $response->assertDontSee('Monogram Tian Maysa', false);
        $response->assertDontSee('localhost:8000/storage', false);
    }

    public function test_trusted_tunnel_proxy_uses_https_for_assets_and_photo(): void
    {
        SiteProfile::current()->update([
            'photo_path' => 'profile/portrait.jpg',
            'photo_alt' => 'Potret Tian Maysa',
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->withHeaders([
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-Port' => '443',
            ])
            ->get('http://example.trycloudflare.com/');

        $response->assertSee('href="https://example.trycloudflare.com/build/assets/app-', false);
        $response->assertSee('src="https://example.trycloudflare.com/storage/profile/portrait.jpg"', false);
        $response->assertSee('content="https://example.trycloudflare.com/storage/profile/portrait.jpg"', false);
        $response->assertDontSee('http://example.trycloudflare.com/build/', false);
    }

    /** @return array<string, mixed> */
    private function profileSchema(TestResponse $response): array
    {
        $matched = preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $this->assertSame(1, $matched);

        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
    }
}
