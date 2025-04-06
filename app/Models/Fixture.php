<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fixture extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_number',
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'played'
    ];

    protected $casts = [
        'played' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saved(function ($fixture) {

            if ($fixture->isDirty('played') && $fixture->played ) {
                // if played is changed to true, update the league table
                $homeTeam = $fixture->homeTeam;
                $awayTeam = $fixture->awayTeam;

                $homeTeamTable = $homeTeam->leagueTable;
                $awayTeamTable = $awayTeam->leagueTable;

                $homeGoals = $fixture->home_team_score;
                $awayGoals = $fixture->away_team_score;

                $homeTeamTable->played += 1;
                $homeTeamTable->goals_for += $homeGoals;
                $homeTeamTable->goals_against += $awayGoals;

                $awayTeamTable->played += 1;
                $awayTeamTable->goals_for += $awayGoals;
                $awayTeamTable->goals_against += $homeGoals;

                // Determine match result
                if ($homeGoals > $awayGoals) {
                    // Home team wins
                    $homeTeamTable->won += 1;
                    $homeTeamTable->points += 3;

                    $awayTeamTable->lost += 1;
                } elseif ($homeGoals < $awayGoals) {
                    // Away team wins
                    $awayTeamTable->won += 1;
                    $awayTeamTable->points += 3;

                    $homeTeamTable->lost += 1;
                } else {
                    // Draw
                    $homeTeamTable->drawn += 1;
                    $homeTeamTable->points += 1;

                    $awayTeamTable->drawn += 1;
                    $awayTeamTable->points += 1;
                }

                $homeTeamTable->goal_difference = $homeTeamTable->goals_for - $homeTeamTable->goals_against;
                $awayTeamTable->goal_difference = $awayTeamTable->goals_for - $awayTeamTable->goals_against;

                $homeTeamTable->save();
                $awayTeamTable->save();
            }
        });
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * Play the fixture and update the scores
     */
    public function play($homeGoals, $awayGoals)
    {
        $this->update([
            'home_team_score' => $homeGoals,
            'away_team_score' => $awayGoals,
            'played' => true,
        ]);
    }

    /**
     * Simulate the fixture and update the scores
     */
    public function simulate()
    {
        $homeTeam = $this->homeTeam;
        $awayTeam = $this->awayTeam;

        // Calculate effective strengths with home/away adjustments
        $homeEffectiveStrength = $homeTeam->strength + $homeTeam->home_advantage + $homeTeam->supporter_strength / 10;
        $awayEffectiveStrength = $awayTeam->strength - $awayTeam->away_disadvantage;

        // Calculate goal probabilities based on team strengths and striker/goalkeeper indexes
        $homeGoalProbability = ($homeEffectiveStrength + $homeTeam->striker_index - $awayTeam->goalkeeper_index / 2) / 100;
        $awayGoalProbability = ($awayEffectiveStrength + $awayTeam->striker_index - $homeTeam->goalkeeper_index / 2) / 100;

        // Simulate goals (typically 0-5 goals per team)
        $homeGoals = $this->generateGoals($homeGoalProbability);
        $awayGoals = $this->generateGoals($awayGoalProbability);

        $this->play($homeGoals, $awayGoals);
    }

    /**
     * Generate goals based on probability
     */
    private function generateGoals($probability)
    {
        // Base number of goals
        $baseGoals = rand(0, 3);

        // Adjust based on probability (higher probability = more likely to score additional goals)
        $additionalGoals = 0;
        if (rand(0, 100) / 100 < $probability) {
            $additionalGoals += rand(0, 2);
        }

        return $baseGoals + $additionalGoals;
    }

    public static function resetFixtures()
    {
        static::query()->update([
            'played' => false,
            'home_team_score' => null,
            'away_team_score' => null,
        ]);

        LeagueTable::resetStats();
    }
}