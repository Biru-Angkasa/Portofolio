<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use App\WorkStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_article_has_its_own_indexable_canonical_url(): void
    {
        $post = BlogPost::factory()->published()->create(['title' => 'Belajar VLAN']);

        $response = $this->get('https://portfolio.example/blog/'.$post->id.'?source=search');

        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical" href="https://portfolio.example/blog/'.$post->id.'">', false);
        $response->assertSee('<meta property="og:url" content="https://portfolio.example/blog/'.$post->id.'">', false);
        $response->assertSee('<title>Belajar VLAN — Tian Maysa</title>', false);
    }

    public function test_guests_cannot_manage_blog_posts(): void
    {
        $post = BlogPost::factory()->create();
        $this->get(route('admin.blog.index'))->assertRedirect(route('login'));
        $this->post(route('admin.blog.store'), [])->assertRedirect(route('login'));
        $this->put(route('admin.blog.update', $post), [])->assertRedirect(route('login'));
        $this->delete(route('admin.blog.destroy', $post))->assertRedirect(route('login'));
        $this->assertModelExists($post);
    }

    public function test_drafts_are_hidden_from_home_and_direct_urls(): void
    {
        $draft = BlogPost::factory()->create(['title' => 'Artikel pribadi']);
        $published = BlogPost::factory()->published()->create(['title' => 'Catatan jaringan']);
        $this->get(route('home'))->assertSee('Catatan jaringan')->assertDontSee('Artikel pribadi');
        $this->get(route('blog.show', $draft))->assertNotFound();
        $response = $this->get(route('blog.show', $published))->assertOk();
        foreach (explode("\n\n", $published->body) as $paragraph) {
            $response->assertSee($paragraph);
        }
    }

    public function test_owner_can_create_publish_and_delete_an_article(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.blog.store'), ['title' => 'Belajar VLAN', 'category' => 'Jaringan', 'excerpt' => 'Catatan singkat', 'body' => "Paragraf satu\n\nParagraf dua", 'status' => 'draft'])
            ->assertSessionHasNoErrors();
        $post = BlogPost::query()->sole();
        $this->assertSame(WorkStatus::Draft, $post->status);
        $this->get(route('admin.blog.edit', $post))->assertOk()->assertSee('Paragraf satu');

        $this->put(route('admin.blog.update', $post), ['title' => 'Belajar VLAN', 'category' => 'Jaringan', 'excerpt' => 'Catatan singkat', 'body' => 'Artikel terbit', 'status' => 'published'])->assertSessionHasNoErrors();
        $this->get(route('blog.show', $post))->assertSee('Artikel terbit');
        $this->delete(route('admin.blog.destroy', $post))->assertRedirect(route('admin.blog.index'));
        $this->assertModelMissing($post);
        $this->get(route('blog.show', $post))->assertNotFound();
    }

    public function test_invalid_article_is_not_stored(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.blog.store'), [])
            ->assertSessionHasErrors(['title', 'category', 'excerpt', 'body', 'status']);
        $this->assertDatabaseCount('blog_posts', 0);
        $this->post(route('admin.blog.store'), ['title' => 'Test', 'category' => 'Test', 'excerpt' => 'Test', 'body' => 'Test', 'status' => 'invalid'])->assertSessionHasErrors('status');
    }

    public function test_cover_replacement_removal_and_article_deletion_clean_up_files(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $attributes = ['title' => 'Foto jaringan', 'category' => 'Jaringan', 'excerpt' => 'Foto perangkat', 'body' => 'Isi', 'status' => 'published'];
        $this->post(route('admin.blog.store'), [...$attributes, 'cover' => UploadedFile::fake()->image('cover.jpg')])->assertSessionHasNoErrors();
        $post = BlogPost::query()->sole();
        $first = $post->cover_path;
        Storage::disk('public')->assertExists($first);
        $this->get(route('blog.show', $post))->assertSee($post->coverUrl(), false);
        $this->put(route('admin.blog.update', $post), [...$attributes, 'cover' => UploadedFile::fake()->image('second.png')])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($first);
        $second = $post->fresh()->cover_path;
        Storage::disk('public')->assertExists($second);
        $this->put(route('admin.blog.update', $post), [...$attributes, 'remove_cover' => true])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($second);
        $this->assertNull($post->fresh()->cover_path);
        $this->put(route('admin.blog.update', $post), [...$attributes, 'cover' => UploadedFile::fake()->image('third.webp')])->assertSessionHasNoErrors();
        $third = $post->fresh()->cover_path;
        $this->delete(route('admin.blog.destroy', $post));
        Storage::disk('public')->assertMissing($third);
    }

    public function test_active_uploads_are_rejected_and_article_text_is_escaped(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.blog.store'), ['title' => 'Test', 'category' => 'Test', 'excerpt' => 'Test', 'body' => 'Test', 'status' => 'published', 'cover' => UploadedFile::fake()->create('cover.svg', 1, 'image/svg+xml')])
            ->assertSessionHasErrors('cover');
        $script = '<script>alert("x")</script>';
        $post = BlogPost::factory()->published()->create(['title' => $script, 'category' => $script, 'excerpt' => $script, 'body' => $script]);
        $this->get(route('home'))->assertSee($script)->assertDontSee($script, false);
        $this->get(route('blog.show', $post))->assertSee($script)->assertDontSee($script, false);
    }
}
