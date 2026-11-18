<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\WorkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BlogPost> */
class BlogPostFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'category' => 'Networking',
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
            'status' => WorkStatus::Draft,
            'cover_path' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => ['status' => WorkStatus::Published]);
    }
}
