<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_indexed_project_url_permanently_redirects_directly_to_home(): void
    {
        $this->get('https://portfolio.example/bukti/4')
            ->assertMovedPermanently()
            ->assertRedirect('https://portfolio.example');
    }

    public function test_beranda_permanently_redirects_to_the_canonical_home(): void
    {
        $this->get('https://portfolio.example/beranda?source=search')
            ->assertMovedPermanently()
            ->assertRedirect('https://portfolio.example');
    }
}
