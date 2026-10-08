<?php

namespace App\Models;

use App\MediaKind;
use Database\Factories\WorkMediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['work_item_id', 'kind', 'path', 'url', 'label', 'caption', 'sort_order'])]
class WorkMedia extends Model
{
    /** @use HasFactory<WorkMediaFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (WorkMedia $media): void {
            if ($media->path !== null) {
                Storage::disk('public')->delete($media->path);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => MediaKind::class,
        ];
    }

    /**
     * @return BelongsTo<WorkItem, $this>
     */
    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class);
    }

    public function fileUrl(): ?string
    {
        if ($this->path === null) {
            return null;
        }

        return url('/storage/'.ltrim($this->path, '/'));
    }
}
