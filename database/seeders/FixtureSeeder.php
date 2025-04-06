<?php

namespace Database\Seeders;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Seeder;

class FixtureSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::all()->pluck('id')->toArray();
        $weeks = 6; // For a 4-team league, each team plays against the others twice (home and away)

        $fixtures = [
            // Week 1
            ['week' => 1, 'home' => 0, 'away' => 3], // Arsenal vs Liverpool
            ['week' => 1, 'home' => 1, 'away' => 2], // Manchester City vs Chelsea

            // Week 2
            ['week' => 2, 'home' => 1, 'away' => 0], // Manchester City vs Arsenal
            ['week' => 2, 'home' => 2, 'away' => 3], // Chelsea vs Liverpool

            // Week 3
            ['week' => 3, 'home' => 0, 'away' => 2], // Arsenal vs Chelsea
            ['week' => 3, 'home' => 3, 'away' => 1], // Liverpool vs Manchester City

            // Week 4
            ['week' => 4, 'home' => 3, 'away' => 0], // Liverpool vs Arsenal
            ['week' => 4, 'home' => 2, 'away' => 1], // Chelsea vs Manchester City

            // Week 5
            ['week' => 5, 'home' => 0, 'away' => 1], // Arsenal vs Manchester City
            ['week' => 5, 'home' => 3, 'away' => 2], // Liverpool vs Chelsea

            // Week 6
            ['week' => 6, 'home' => 2, 'away' => 0], // Chelsea vs Arsenal
            ['week' => 6, 'home' => 1, 'away' => 3], // Manchester City vs Liverpool
        ];

        Fixture::truncate();

        foreach ($fixtures as $fixture) {
            Fixture::updateOrCreate(
                [
                    'week_number' => $fixture['week'],
                    'home_team_id' => $teams[$fixture['home']],
                    'away_team_id' => $teams[$fixture['away']],
                ],
                [
                    // 'week_number' => $fixture['week'],
                    // 'home_team_id' => $teams[$fixture['home']],
                    // 'away_team_id' => $teams[$fixture['away']],
                    'played' => false,
                ]
        );
        }
    }
}