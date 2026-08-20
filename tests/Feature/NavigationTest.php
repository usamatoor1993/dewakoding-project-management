<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_analytics_visible_to_super_admin(): void
    {
        $super = User::where('email', 'superadmin@admin.com')->first();

        $this->actingAs($super)->get('/admin')
            ->assertOk()
            ->assertSee('User Contributions')
            ->assertSee('Leaderboard');
        $this->actingAs($super)->get('/admin/user-contributions')->assertOk();
        $this->actingAs($super)->get('/admin/leaderboard')->assertOk();
    }

    public function test_analytics_visible_to_admin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@bytes.com'],
            ['name' => 'Admin User', 'password' => 'password', 'email_verified_at' => now()],
        );
        $admin->syncRoles('admin');

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('User Contributions')
            ->assertSee('Leaderboard');
        $this->actingAs($admin)->get('/admin/user-contributions')->assertOk();
    }

    public function test_analytics_hidden_from_member(): void
    {
        $member = User::where('email', 'test@example.com')->first();
        $member->syncRoles('member');

        $this->actingAs($member)->get('/admin')
            ->assertOk()
            ->assertDontSee('User Contributions')
            ->assertDontSee('Leaderboard');
        $this->actingAs($member)->get('/admin/user-contributions')->assertForbidden();
        $this->actingAs($member)->get('/admin/leaderboard')->assertForbidden();
    }

    public function test_settings_group_is_last_and_all_groups_collapsed_by_default(): void
    {
        $super = User::where('email', 'superadmin@admin.com')->first();
        $html = $this->actingAs($super)->get('/admin')->getContent();

        $positions = [];
        foreach (['Project Management', 'Analytics', 'Settings'] as $label) {
            $pos = strpos($html, 'data-group-label="' . $label . '"');
            $this->assertNotFalse($pos, "Group [{$label}] should be present in the sidebar");
            $positions[$label] = $pos;
        }

        $this->assertLessThan($positions['Analytics'], $positions['Project Management']);
        $this->assertLessThan($positions['Settings'], $positions['Analytics']);

        $this->assertStringContainsString(
            '\u0022Project Management\u0022,\u0022Analytics\u0022,\u0022Settings\u0022',
            $html,
            'All navigation groups should be collapsed by default',
        );
    }
}