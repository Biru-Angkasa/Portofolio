<?php

namespace Tests\Feature;

use App\Models\WorkItem;
use App\WorkStatus;
use Database\Seeders\PofPortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PofPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_pof_project_is_published_with_documentation_and_a_live_link(): void
    {
        $existingWork = WorkItem::factory()->published()->create(['sort_order' => 5]);

        $this->seed(PofPortfolioSeeder::class);

        $work = WorkItem::query()->where('title', 'Kalkulator PoF TARMOC')->sole();
        $this->assertSame(WorkStatus::Published, $work->status);
        $this->assertSame(6, $work->sort_order);
        $this->assertModelExists($existingWork);
        $this->get(route('home'))
            ->assertSee('Kalkulator PoF TARMOC')
            ->assertSee('https://kalkulator-pof.opsys.web.id/', false)
            ->assertSee('/storage/work/pof/calculator.png', false);
        $this->get(route('work.show', $work))
            ->assertSee('Cari adaptor, Cari jarak, dan Cari daya')
            ->assertSee('Hasil proyek')
            ->assertSee('/storage/work/pof/adapter-chart.png', false)
            ->assertSee('Buka Kalkulator PoF TARMOC');
    }

    public function test_reseeding_does_not_duplicate_projects_or_overwrite_editorial_changes(): void
    {
        $this->seed(PofPortfolioSeeder::class);
        $work = WorkItem::query()->sole();
        $work->update(['summary' => 'Deskripsi yang sudah disunting pemilik.', 'status' => WorkStatus::Draft]);
        $work->media()->where('kind', 'link')->update(['label' => 'Tautan yang disunting pemilik']);

        $this->seed(PofPortfolioSeeder::class);

        $this->assertDatabaseCount('work_items', 1);
        $this->assertDatabaseCount('work_media', 3);
        $this->assertSame('Deskripsi yang sudah disunting pemilik.', $work->fresh()->summary);
        $this->assertSame(WorkStatus::Draft, $work->fresh()->status);
        $this->assertDatabaseHas('work_media', ['work_item_id' => $work->id, 'label' => 'Tautan yang disunting pemilik']);
    }
}
