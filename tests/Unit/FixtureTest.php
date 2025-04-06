<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_expected_fillable_attributes()
    {
        $fixture = new Fixture();

        $this->assertEquals([
            'week_number',
            'home_team_id',
            'away_team_id',
            'home_team_score',
            'away_team_score',
            'played'
        ], $fixture->getFillable());
    }

    public function test_casts_played_to_boolean()
    {
        $fixture = Fixture::factory()->create(['played' => 1]);

        $this->assertIsBool($fixture->played);
        $this->assertTrue($fixture->played);

        $fixture = Fixture::factory()->create(['played' => 0]);

        $this->assertIsBool($fixture->played);
        $this->assertFalse($fixture->played);
    }

    public function test_belongs_to_home_team()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->assertInstanceOf(Team::class, $fixture->homeTeam);
        $this->assertEquals($homeTeam->id, $fixture->homeTeam->id);
    }

    public function test_belongs_to_away_team()
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->assertInstanceOf(Team::class, $fixture->awayTeam);
        $this->assertEquals($awayTeam->id, $fixture->awayTeam->id);
    }

    public function test_can_create_fixture_with_scores()
    {
        $fixture = Fixture::factory()->create([
            'home_team_score' => 3,
            'away_team_score' => 1,
            'played' => true,
        ]);

        $this->assertEquals(3, $fixture->home_team_score);
        $this->assertEquals(1, $fixture->away_team_score);
        $this->assertTrue($fixture->played);
    }

    public function test_can_create_fixture_without_scores()
    {
        $fixture = Fixture::factory()->create([
            'home_team_score' => null,
            'away_team_score' => null,
            'played' => false,
        ]);

        $this->assertNull($fixture->home_team_score);
        $this->assertNull($fixture->away_team_score);
        $this->assertFalse($fixture->played);
    }

    public function test_a_team_can_have_multiple_home_fixtures()
    {
        $team = Team::factory()->create();
        Fixture::factory()->count(3)->create(['home_team_id' => $team->id]);

        $this->assertCount(3, $team->homeFixtures);
        $this->assertInstanceOf(Fixture::class, $team->homeFixtures->first());
    }

    public function test_a_team_can_have_multiple_away_fixtures()
    {
        $team = Team::factory()->create();
        Fixture::factory()->count(2)->create(['away_team_id' => $team->id]);

        $this->assertCount(2, $team->awayFixtures);
        $this->assertInstanceOf(Fixture::class, $team->awayFixtures->first());
    }
}