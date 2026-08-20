<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\MonthlyTicketTrendChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAndProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_dashboard_renders_widgets(): void
    {
        $user = User::where('email', 'superadmin@admin.com')->first();

        $this->actingAs($user)->get('/admin')->assertOk();

        Livewire::test(StatsOverview::class)
            ->assertSee('Total Projects')
            ->assertSee('My Assigned Tickets')
            ->assertSee('Team Members');
    }

    public function test_profile_page_renders_and_updates(): void
    {
        $user = User::where('email', 'superadmin@admin.com')->first();

        $this->actingAs($user)->get('/admin/profile')->assertOk();

        Livewire::actingAs($user)
            ->test(EditProfile::class)
            ->set('data.name', 'Usama Updated')
            ->set('data.email', 'superadmin@admin.com')
            ->set('data.job_title', 'Lead Developer')
            ->set('data.phone', '+628123456789')
            ->set('data.bio', 'Building great software.')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals('Usama Updated', $user->name);
        $this->assertEquals('Lead Developer', $user->job_title);
        $this->assertEquals('+628123456789', $user->phone);
        $this->assertEquals('Building great software.', $user->bio);
    }

    public function test_monthly_ticket_trend_chart_renders_on_sqlite(): void
    {
        $user = User::where('email', 'superadmin@admin.com')->first();

        Livewire::actingAs($user)
            ->test(MonthlyTicketTrendChart::class)
            ->assertOk()
            ->assertHasNoErrors();
    }
}