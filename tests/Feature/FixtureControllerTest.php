<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FixtureControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_fixtures_page()
    {
        // Create teams and fixtures
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        Fixture::factory()->create([
            'week_number' => 1,
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);

        // Visit fixtures page
        $response = $this->get(route('fixtures.index'));

        // Assert response is successful and contains expected data
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Fixtures/Index')
            ->has('fixtures')
            ->has('teams')
        );
    }

    public function test_generate_fixtures_creates_matches_for_teams()
    {
        // Create 4 teams
        Team::factory()->count(4)->create();

        // Initially no fixtures
        $this->assertEquals(0, Fixture::count());

        // Generate fixtures
        $response = $this->post(route('fixtures.generate'));

        // Assert redirect to fixtures index
        $response->assertRedirect(route('fixtures.index'));

        // Assert fixtures were created
        $this->assertGreaterThan(0, Fixture::count());

        // For 4 teams, we should have 12 fixtures (each team plays against every other team home and away)
        // 4 teams * 3 opponents per team * 2 (home and away) / 2 (to avoid counting twice) = 12 fixtures
        $this->assertEquals(12, Fixture::count());

        // Verify each team has both home and away fixtures
        $teams = Team::all();
        foreach ($teams as $team) {
            $this->assertGreaterThan(0, $team->homeFixtures()->count());
            $this->assertGreaterThan(0, $team->awayFixtures()->count());
        }
    }

    public function test_generate_fixtures_returns_error_with_less_than_four_teams()
    {
        // Create only 3 teams
        Team::factory()->count(3)->create();

        // Try to generate fixtures
        $response = $this->from(route('fixtures.index'))->post(route('fixtures.generate'));

        // Assert redirect back with error message
        $response->assertRedirect(route('fixtures.index'));
        $response->assertSessionHas('message', 'At least 4 teams are required to generate fixtures.');
        $response->assertSessionHas('variant', 'error');

        // Assert no fixtures were created
        $this->assertEquals(0, Fixture::count());
    }

    public function test_clear_fixtures_removes_all_fixtures()
    {
        // Create teams and fixtures
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        Fixture::factory()->count(5)->create([
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);

        // Assert fixtures exist
        $this->assertEquals(5, Fixture::count());

        // Clear fixtures
        $response = $this->post(route('fixtures.clear'));

        // Assert redirect to fixtures index
        $response->assertRedirect(route('fixtures.index'));

        // Assert fixtures were removed
        $this->assertEquals(0, Fixture::count());
    }

    public function test_generated_fixtures_have_correct_structure()
    {
        // Create 4 teams
        $teams = Team::factory()->count(4)->create();

        // Generate fixtures
        $this->post(route('fixtures.generate'));

        // Check first round fixtures
        $weekOneFixtures = Fixture::where('week_number', 1)->get();

        // Should have 2 matches in week 1 (4 teams / 2)
        $this->assertEquals(2, $weekOneFixtures->count());

        // Each fixture should have different home and away teams
        foreach ($weekOneFixtures as $fixture) {
            $this->assertNotEquals($fixture->home_team_id, $fixture->away_team_id);
            $this->assertFalse($fixture->played);
            $this->assertNull($fixture->home_team_score);
            $this->assertNull($fixture->away_team_score);
        }

        // Check that each team plays against each other team twice (home and away)
        foreach ($teams as $team1) {
            foreach ($teams as $team2) {
                if ($team1->id !== $team2->id) {
                    // Team 1 hosts Team 2
                    $homeFixture = Fixture::where('home_team_id', $team1->id)
                        ->where('away_team_id', $team2->id)
                        ->count();

                    // Team 1 visits Team 2
                    $awayFixture = Fixture::where('home_team_id', $team2->id)
                        ->where('away_team_id', $team1->id)
                        ->count();

                    // Each team should play once at home and once away against each other team
                    $this->assertEquals(1, $homeFixture);
                    $this->assertEquals(1, $awayFixture);
                }
            }
        }
    }
}