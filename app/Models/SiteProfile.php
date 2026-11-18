<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['photo_path', 'photo_alt', 'photo_original_path', 'photo_crop', 'cv_path', 'content'])]
class SiteProfile extends Model
{
    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['content' => 'array', 'photo_crop' => 'array'];
    }

    /** @return array<string, mixed> */
    public function portfolio(): array
    {
        $portfolio = array_replace(config('portfolio'), $this->content ?? []);
        $portfolio['socials'] = array_map(fn (array $social): array => [
            ...$social,
            'platform' => $this->socialPlatform($social),
        ], $portfolio['socials']);
        $portfolio['meta'] = [
            'title' => 'Web Portofolio '.$portfolio['name'].' — '.$portfolio['role'],
            'description' => $portfolio['role'].' di Jakarta. Berpengalaman di MikroTik, fiber optic, after-sales, dan pengembangan aplikasi internal. Lihat portofolio.',
        ];

        return $portfolio;
    }

    /** @param array{label: string, url?: ?string, platform?: ?string} $social */
    private function socialPlatform(array $social): string
    {
        $platform = $social['platform'] ?? 'auto';
        $options = config('portfolio-editor.kontak.collections.socials.fields.platform.options');

        if ($platform !== 'auto' && isset($options[$platform])) {
            return $platform;
        }

        $host = strtolower((string) parse_url($social['url'] ?? '', PHP_URL_HOST));
        $domains = [
            'instagram' => ['instagram.com'],
            'whatsapp' => ['wa.me', 'whatsapp.com'],
            'facebook' => ['facebook.com', 'fb.com', 'fb.me'],
            'linkedin' => ['linkedin.com'],
            'github' => ['github.com'],
            'youtube' => ['youtube.com', 'youtu.be'],
            'x' => ['x.com', 'twitter.com'],
            'tiktok' => ['tiktok.com'],
            'telegram' => ['t.me', 'telegram.me'],
        ];

        foreach ($domains as $key => $hosts) {
            foreach ($hosts as $domain) {
                if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                    return $key;
                }
            }
        }

        return 'website';
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        return url('/storage/'.ltrim($this->photo_path, '/'));
    }

    public function hasCustomCv(): bool
    {
        return is_string($this->cv_path) && Storage::disk('local')->exists($this->cv_path);
    }

    public function cvDownloadName(): string
    {
        $name = trim((string) ($this->portfolio()['name'] ?? 'Tian Maysa'));
        $sanitized = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-');

        return ($sanitized !== '' ? $sanitized : 'Portfolio').'-CV.pdf';
    }
}
