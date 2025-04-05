<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'strength' => $this->strength,
            'home_advantage' => $this->home_advantage,
            'away_disadvantage' => $this->away_disadvantage,
            'goalkeeper_index' => $this->goalkeeper_index,
            'striker_index' => $this->striker_index,
            'supporter_strength' => $this->supporter_strength,
        ];
    }
}