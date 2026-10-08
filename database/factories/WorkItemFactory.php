<?php

namespace Database\Factories;

use App\Models\WorkItem;
use App\WorkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkItem>
 */
class WorkItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Tracing jalur ODP',
            'category' => 'Fiber optic',
            'summary' => 'Validasi koneksi pelanggan melalui ODP saat PKL di Telkom.',
            'role' => 'Membantu tracing dan penataan jalur.',
            'period' => 'Agustus 2022',
            'caption' => null,
            'status' => WorkStatus::Draft,
            'sort_order' => 1,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => WorkStatus::Published,
        ]);
    }
}
