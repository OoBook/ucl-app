<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition()
    {
        return [
            'week_number' => $this->faker->numberBetween(1, 38),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'home_team_score' => $this->faker->optional(0.7)->numberBetween(0, 5),
            'away_team_score' => $this->faker->optional(0.7)->numberBetween(0, 5),
            'played' => $this->faker->boolean(70),
        ];
    }

    public function played()
    {
        return $this->state(function (array $attributes) {
            return [
                'played' => true,
                'home_team_score' => $this->faker->numberBetween(0, 5),
                'away_team_score' => $this->faker->numberBetween(0, 5),
            ];
        });
    }

    public function upcoming()
    {
        return $this->state(function (array $attributes) {
            return [
                'played' => false,
                'home_team_score' => null,
                'away_team_score' => null,
            ];
        });
    }
}