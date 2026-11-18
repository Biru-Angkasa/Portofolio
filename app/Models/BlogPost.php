<?php

namespace App\Models;

use App\WorkStatus;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['title', 'category', 'excerpt', 'body', 'status', 'cover_path'])]
class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (BlogPost $post): void {
            if (is_string($post->cover_path)) {
                Storage::disk('public')->delete($post->cover_path);
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['status' => WorkStatus::class];
    }

    /** @param Builder<BlogPost> $query */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', WorkStatus::Published);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? url('/storage/'.ltrim($this->cover_path, '/')) : null;
    }

    public function renderedBody(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'renderer' => [
                'soft_break' => "<br>\n",
            ],
        ]);
    }

    public function readingTimeMinutes(): int
    {
        $plain = preg_replace('/[`#*_>\[\]()!-]+/u', ' ', $this->body) ?? $this->body;
        $words = preg_split('/\s+/u', trim($plain), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(($words === false ? 0 : count($words)) / 200));
    }
}
