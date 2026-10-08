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

#[Fillable(['title', 'category', 'summary', 'role', 'period', 'caption', 'status', 'sort_order'])]
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
}
