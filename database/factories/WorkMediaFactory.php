<?php

namespace Database\Factories;

use App\MediaKind;
use App\Models\WorkItem;
use App\Models\WorkMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkMedia>
 */
class WorkMediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_item_id' => WorkItem::factory(),
            'kind' => MediaKind::Link,
            'path' => null,
            'url' => 'https://example.com/dokumentasi',
            'label' => 'Catatan lapangan',
            'caption' => null,
            'sort_order' => 1,
        ];
    }
}
