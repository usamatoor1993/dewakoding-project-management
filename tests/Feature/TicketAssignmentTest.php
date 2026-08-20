<?php

namespace Tests\Feature;

use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->super = User::where('email', 'superadmin@admin.com')->first();
        $this->member = User::factory()->create(['name' => 'Usama']);
        $this->member->assignRole('member');

        $this->project = Project::create([
            'name' => 'Doyoum',
            'ticket_prefix' => 'doyoum-accounts',
        ]);
        $this->project->members()->attach([$this->super->id, $this->member->id]);

        $status = TicketStatus::create([
            'project_id' => $this->project->id,
            'name' => 'To Do',
            'sort_order' => 1,
        ]);

        $this->ticket = Ticket::create([
            'project_id' => $this->project->id,
            'ticket_status_id' => $status->id,
            'name' => 'Chart of accounts',
            'created_by' => $this->super->id,
        ]);
    }

    public function test_assignees_dropdown_excludes_super_admin_and_includes_project_members(): void
    {
        $options = [];

        Livewire::actingAs($this->super)
            ->test(EditTicket::class, ['record' => $this->ticket->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('assignees', function ($field) use (&$options) {
                $options = $field->getOptions();

                return true;
            });

        $this->assertArrayHasKey($this->member->id, $options);
        $this->assertArrayNotHasKey($this->super->id, $options);
    }

    public function test_non_project_members_are_not_assignable(): void
    {
        $outsider = User::factory()->create(['name' => 'Outsider']);
        $outsider->assignRole('member');

        $options = [];

        Livewire::actingAs($this->super)
            ->test(EditTicket::class, ['record' => $this->ticket->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('assignees', function ($field) use (&$options) {
                $options = $field->getOptions();

                return true;
            });

        $this->assertArrayNotHasKey($outsider->id, $options);
    }
}