<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'strength',
        'home_advantage',
        'away_disadvantage',
        'goalkeeper_index',
        'striker_index',
        'supporter_strength'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($team) {
            $team->leagueTable()->create();
            $team->championshipPrediction()->create();
        });
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }

    public function leagueTable(): HasOne
    {
        return $this->hasOne(LeagueTable::class);
    }

    public function championshipPrediction(): HasOne
    {
        return $this->hasOne(ChampionshipPrediction::class);
    }
}