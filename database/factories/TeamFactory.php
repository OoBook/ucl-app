<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'strength' => $this->faker->numberBetween(40, 90),
            'home_advantage' => $this->faker->numberBetween(5, 15),
            'away_disadvantage' => $this->faker->numberBetween(3, 10),
            'goalkeeper_index' => $this->faker->numberBetween(40, 90),
            'striker_index' => $this->faker->numberBetween(40, 90),
            'supporter_strength' => $this->faker->numberBetween(20, 50),
        ];
    }
}