<?php

namespace Tests\Feature;

use App\Filament\Pages\ProjectTimeline;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->super = User::where('email', 'superadmin@admin.com')->first();
        $this->admin = User::factory()->create(['name' => 'Admin']);
        $this->admin->assignRole('admin');
        $this->member = User::factory()->create(['name' => 'Member']);
        $this->member->assignRole('member');

        $this->projectA = Project::create([
            'name' => 'Alpha',
            'ticket_prefix' => 'alpha',
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-31',
        ]);
        $this->projectB = Project::create([
            'name' => 'Bravo',
            'ticket_prefix' => 'bravo',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-20',
        ]);
        $this->projectC = Project::create([
            'name' => 'Charlie',
            'ticket_prefix' => 'charlie',
            'start_date' => '2026-10-01',
            'end_date' => '2027-06-30',
        ]);

        // Member belongs only to Bravo (nearest deadline) and Alpha (far).
        $this->member->projects()->attach([$this->projectB->id, $this->projectA->id]);
    }

    public function test_super_admin_and_admin_see_all_projects(): void
    {
        foreach ([$this->super, $this->admin] as $user) {
            $component = Livewire::actingAs($user)
                ->test(ProjectTimeline::class)
                ->assertOk();

            $names = collect($component->instance()->ganttData['data'])->pluck('text')->toArray();
            $this->assertEqualsCanonicalizing(['Alpha', 'Bravo', 'Charlie'], $names);
        }
    }

    public function test_member_only_sees_assigned_projects(): void
    {
        $component = Livewire::actingAs($this->member)
            ->test(ProjectTimeline::class)
            ->assertOk();

        $names = collect($component->instance()->ganttData['data'])->pluck('text')->toArray();
        $this->assertEqualsCanonicalizing(['Alpha', 'Bravo'], $names);
        $this->assertNotContains('Charlie', $names);
    }

    public function test_projects_ordered_by_nearest_deadline_first(): void
    {
        $component = Livewire::actingAs($this->super)
            ->test(ProjectTimeline::class)
            ->assertOk();

        $data = collect($component->instance()->ganttData['data']);
        $names = $data->pluck('text')->toArray();

        $this->assertSame(['Bravo', 'Alpha', 'Charlie'], $names);

        $priorities = $data->pluck('priority')->toArray();
        $this->assertSame([1, 2, 3], $priorities);
    }
}