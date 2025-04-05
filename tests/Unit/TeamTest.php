<?php

namespace Tests\Unit;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_the_correct_fillable_attributes()
    {
        $team = new Team();

        $this->assertEquals([
            'name',
            'strength',
            'home_advantage',
            'away_disadvantage',
            'goalkeeper_index',
            'striker_index',
            'supporter_strength'
        ], $team->getFillable());
    }

    public function test_can_be_created_with_required_attributes()
    {
        $team = Team::factory()->create([
            'name' => 'Test Team',
            'strength' => 75,
            'home_advantage' => 10,
            'away_disadvantage' => 5,
            'goalkeeper_index' => 80,
            'striker_index' => 70,
            'supporter_strength' => 25
        ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test Team',
            'strength' => 75,
            'home_advantage' => 10,
            'away_disadvantage' => 5,
            'goalkeeper_index' => 80,
            'striker_index' => 70,
            'supporter_strength' => 25
        ]);
    }
}