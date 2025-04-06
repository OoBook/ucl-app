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
    public function saveScore($homeGoals, $awayGoals)
    {
        $this->update([
            'home_team_score' => $homeGoals,
            'away_team_score' => $awayGoals,
            'played' => true,
        ]);
    }

    public static function resetFixtures()
    {
        static::query()->update([
            'played' => false,
            'home_team_score' => null,
            'away_team_score' => null,
        ]);

        LeagueTable::resetStats();

        ChampionshipPrediction::resetPredictions();
    }
}