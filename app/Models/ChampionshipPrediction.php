<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChampionshipPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'prediction_week',
        'win_probability'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function resetPredictions()
    {
        static::query()->update([
            'prediction_week' => null,
            'win_probability' => null,
        ]);
    }
}