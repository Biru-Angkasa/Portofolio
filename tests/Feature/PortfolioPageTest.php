<?php

namespace Tests\Feature;

use App\Models\SiteProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertDontSee('Bukti kerja');
        $response->assertDontSee('hanya muncul jika berkas');
    }

    public function test_home_omits_phone_link_cv_download_and_internal_notes(): void
    {
        $response = $this->get(route('home'));

        $response->assertDontSee('tel:', false);
        $response->assertDontSee('Unduh CV');
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
}
