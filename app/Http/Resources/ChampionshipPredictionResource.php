<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChampionshipPredictionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team' => (new TeamResource($this->team))->toArray($request),
            'prediction_week' => $this->prediction_week,
            'win_probability' => $this->win_probability,
        ];
    }
}