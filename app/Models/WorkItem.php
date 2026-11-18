<?php

namespace App\Models;

use App\WorkStatus;
use Database\Factories\WorkItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['title', 'category', 'summary', 'role', 'period', 'caption', 'outcome', 'status', 'sort_order'])]
class WorkItem extends Model
{
    /** @use HasFactory<WorkItemFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (WorkItem $item): void {
            $item->media()->get()->each->delete();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => WorkStatus::class,
        ];
    }

    /**
     * @return HasMany<WorkMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(WorkMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<WorkItem>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', WorkStatus::Published);
    }

    public function detailUrl(): string
    {
        $slug = Str::slug($this->title);

        return route('work.show', $slug !== '' ? ['workItem' => $this, 'slug' => $slug] : $this);
    }

    /**
     * @return list<string>
     */
    public function technologies(): array
    {
        if (! is_string($this->caption) || $this->caption === '') {
            return [];
        }

        if (! preg_match('/(?:Teknologi yang digunakan:|Dibangun menggunakan)\s*(.+?)(?:\.(?:\s|$)|$)/iu', $this->caption, $matches)) {
            return [];
        }

        $items = preg_split('/\s*,\s*(?:dan\s+)?|\s+dan\s+/iu', trim($matches[1])) ?: [];

        return array_values(array_filter(array_map('trim', $items), fn (string $item): bool => $item !== ''));
    }
}
