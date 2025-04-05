<?php

namespace Database\Seeders;

use App\Models\LeagueTable;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Liverpool',
                'strength' => 85,
                'home_advantage' => 12,
                'away_disadvantage' => 5,
                'goalkeeper_index' => 82,
                'striker_index' => 88,
                'supporter_strength' => 45,
            ],
            [
                'name' => 'Manchester City',
                'strength' => 88,
                'home_advantage' => 10,
                'away_disadvantage' => 4,
                'goalkeeper_index' => 85,
                'striker_index' => 90,
                'supporter_strength' => 35,
            ],
            [
                'name' => 'Chelsea',
                'strength' => 83,
                'home_advantage' => 11,
                'away_disadvantage' => 6,
                'goalkeeper_index' => 84,
                'striker_index' => 82,
                'supporter_strength' => 45,
            ],
            [
                'name' => 'Arsenal',
                'strength' => 82,
                'home_advantage' => 12,
                'away_disadvantage' => 7,
                'goalkeeper_index' => 81,
                'striker_index' => 83,
                'supporter_strength' => 40,
            ],
        ];

        foreach ($teams as $teamData) {
            $team = Team::updateOrCreate(['name' => $teamData['name']], Arr::except($teamData, ['name']));
        }
    }
}