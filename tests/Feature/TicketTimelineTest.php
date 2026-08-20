<?php

namespace Tests\Feature;

use App\Filament\Pages\TicketTimeline;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->super = User::where('email', 'superadmin@admin.com')->first();
        $this->project = Project::create(['name' => 'Doyoum', 'ticket_prefix' => 'doyoum']);
        $status = TicketStatus::create(['project_id' => $this->project->id, 'name' => 'To Do', 'sort_order' => 1, 'color' => '#3B82F6']);
        $this->ticket = Ticket::create([
            'project_id' => $this->project->id,
            'ticket_status_id' => $status->id,
            'name' => 'Chart of accounts',
            'start_date' => '2026-08-20',
            'due_date' => '2026-08-21',
            'created_by' => $this->super->id,
        ]);
    }

    public function test_timeline_page_renders_gantt_data(): void
    {
        $component = Livewire::actingAs($this->super)
            ->test(TicketTimeline::class, ['project_id' => $this->project->id])
            ->assertOk();

        $ganttData = $component->instance()->ganttData;

        $this->assertCount(1, $ganttData['data']);
        $this->assertEquals('Chart of accounts', $ganttData['data'][0]['text']);
        $this->assertEquals('20-08-2026 00:00', $ganttData['data'][0]['start_date']);
        $this->assertEquals('21-08-2026 00:00', $ganttData['data'][0]['end_date']);
    }
}