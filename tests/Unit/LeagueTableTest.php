<?php

namespace Tests\Unit;

use App\Models\LeagueTable;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_expected_fillable_attributes()
    {
        $leagueTable = new LeagueTable();

        $this->assertEquals([
            'team_id',
            'played',
            'won',
            'drawn',
            'lost',
            'goals_for',
            'goals_against',
            'goal_difference',
            'points'
        ], $leagueTable->getFillable());
    }

    public function test_belongs_to_team()
    {
        $team = Team::factory()->create();
        $leagueTable = $team->leagueTable;

        $this->assertInstanceOf(Team::class, $leagueTable->team);
        $this->assertEquals($team->id, $leagueTable->team->id);
    }

    public function test_league_table_is_automatically_created_with_team()
    {
        $team = Team::factory()->create();

        $this->assertNotNull($team->leagueTable);
        $this->assertInstanceOf(LeagueTable::class, $team->leagueTable);
        $this->assertEquals(0, $team->leagueTable->played);
        $this->assertEquals(0, $team->leagueTable->points);
    }

    public function test_can_create_league_table_with_custom_stats()
    {
        $team = Team::factory()->create();

        $leagueTable = LeagueTable::create([
            'team_id' => $team->id,
            'played' => 10,
            'won' => 5,
            'drawn' => 3,
            'lost' => 2,
            'goals_for' => 15,
            'goals_against' => 8,
            'goal_difference' => 7,
            'points' => 18
        ]);

        $this->assertEquals(10, $leagueTable->played);
        $this->assertEquals(5, $leagueTable->won);
        $this->assertEquals(3, $leagueTable->drawn);
        $this->assertEquals(2, $leagueTable->lost);
        $this->assertEquals(15, $leagueTable->goals_for);
        $this->assertEquals(8, $leagueTable->goals_against);
        $this->assertEquals(7, $leagueTable->goal_difference);
        $this->assertEquals(18, $leagueTable->points);
    }

    public function test_reset_stats_method_resets_all_stats_to_zero()
    {
        // Create multiple teams with non-zero stats
        $teams = Team::factory()->count(3)->create();

        foreach ($teams as $team) {
            $team->leagueTable->update([
                'played' => 5,
                'won' => 2,
                'drawn' => 1,
                'lost' => 2,
                'goals_for' => 7,
                'goals_against' => 6,
                'goal_difference' => 1,
                'points' => 7
            ]);
        }

        // Reset all stats
        LeagueTable::resetStats();

        // Check that all league tables have zeroed stats
        foreach ($teams as $team) {
            $leagueTable = $team->fresh('leagueTable')->leagueTable;

            $this->assertEquals(0, $leagueTable->played);
            $this->assertEquals(0, $leagueTable->won);
            $this->assertEquals(0, $leagueTable->drawn);
            $this->assertEquals(0, $leagueTable->lost);
            $this->assertEquals(0, $leagueTable->goals_for);
            $this->assertEquals(0, $leagueTable->goals_against);
            $this->assertEquals(0, $leagueTable->goal_difference);
            $this->assertEquals(0, $leagueTable->points);
        }
    }

    public function test_default_values_are_zero()
    {
        $team = Team::factory()->create();
        $leagueTable = $team->leagueTable;

        $this->assertEquals(0, $leagueTable->played);
        $this->assertEquals(0, $leagueTable->won);
        $this->assertEquals(0, $leagueTable->drawn);
        $this->assertEquals(0, $leagueTable->lost);
        $this->assertEquals(0, $leagueTable->goals_for);
        $this->assertEquals(0, $leagueTable->goals_against);
        $this->assertEquals(0, $leagueTable->goal_difference);
        $this->assertEquals(0, $leagueTable->points);
    }

    public function test_can_update_league_table_stats()
    {
        $team = Team::factory()->create();
        $leagueTable = $team->leagueTable;

        $leagueTable->played = 1;
        $leagueTable->won = 1;
        $leagueTable->goals_for = 3;
        $leagueTable->goals_against = 1;
        $leagueTable->goal_difference = 2;
        $leagueTable->points = 3;
        $leagueTable->save();

        $freshLeagueTable = $leagueTable->fresh();

        $this->assertEquals(1, $freshLeagueTable->played);
        $this->assertEquals(1, $freshLeagueTable->won);
        $this->assertEquals(0, $freshLeagueTable->drawn);
        $this->assertEquals(0, $freshLeagueTable->lost);
        $this->assertEquals(3, $freshLeagueTable->goals_for);
        $this->assertEquals(1, $freshLeagueTable->goals_against);
        $this->assertEquals(2, $freshLeagueTable->goal_difference);
        $this->assertEquals(3, $freshLeagueTable->points);
    }
}