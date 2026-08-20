<?php

namespace Tests\Feature;

use App\Filament\Pages\EpicsOverview;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\RelationManagers\EpicsRelationManager;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EpicCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->project = Project::create([
            'name' => 'Doyoum',
            'ticket_prefix' => 'doyoum-accounts',
        ]);
    }

    public function test_epic_can_be_created_from_relation_manager(): void
    {
        $super = User::where('email', 'superadmin@admin.com')->first();

        Livewire::actingAs($super)
            ->test(EpicsRelationManager::class, [
                'ownerRecord' => $this->project,
                'pageClass' => ViewProject::class,
            ])
            ->callTableAction(CreateAction::class, data: [
                'name' => 'User Accounts',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('epics', [
            'name' => 'User Accounts',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_epic_can_be_created_from_epics_overview_page(): void
    {
        $super = User::where('email', 'superadmin@admin.com')->first();

        Livewire::actingAs($super)
            ->test(EpicsOverview::class, ['project_id' => $this->project->id])
            ->assertOk()
            ->callAction('create_epic', data: [
                'name' => 'Billing Module',
                'sort_order' => 1,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('epics', [
            'name' => 'Billing Module',
            'project_id' => $this->project->id,
            'sort_order' => 1,
        ]);
    }
}