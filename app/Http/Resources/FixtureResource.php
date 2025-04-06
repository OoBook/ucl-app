<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'week_number' => $this->week_number,
            'home_team' => (new TeamResource($this->homeTeam))->toArray($request),
            'away_team' => (new TeamResource($this->awayTeam))->toArray($request),
            'home_team_score' => $this->home_team_score,
            'away_team_score' => $this->away_team_score,
            'played' => $this->played,
        ];
    }
}