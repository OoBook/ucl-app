<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Http\Resources\TeamResource;

class TeamController extends CoreController
{
    protected $titleField = 'name';

    protected $routePrefix = 'teams';

    protected $title = 'Teams';

    protected $pageTitle = 'Teams';

    protected $pagesPrefix = 'Teams';

    protected $model = Team::class;

    protected $transformerClass = TeamResource::class;

    protected $displayFields = [
        [
            'name' => 'strength',
            'label' => 'Strength'
        ],
        [
            'name' => 'home_advantage',
            'label' => 'Home Advantage'
        ],
        [
            'name' => 'away_disadvantage',
            'label' => 'Away Disadvantage'
        ],
        [
            'name' => 'goalkeeper_index',
            'label' => 'Goalkeeper Index'
        ],
        [
            'name' => 'striker_index',
            'label' => 'Striker Index'
        ],
        [
            'name' => 'supporter_strength',
            'label' => 'Supporter Strength'
        ],
    ];

    protected $schema = [
        [
            'name' => 'name',
            'label' => 'Name',
        ],
        [
            'name' => 'strength',
            'label' => 'Strength (1-100)',
            'type' => 'number',
            'attributes' => ['min' => 1, 'max' => 100],
        ],
        [
            'name' => 'home_advantage',
            'label' => 'Home Advantage (0-20)',
            'type' => 'number',
            'attributes' => ['min' => 0, 'max' => 20],
        ],
        [
            'name' => 'away_disadvantage',
            'label' => 'Away Disadvantage (0-20)',
            'type' => 'number',
            'attributes' => ['min' => 0, 'max' => 20],
        ],
        [
            'name' => 'goalkeeper_index',
            'label' => 'Goalkeeper Index (1-100)',
            'type' => 'number',
            'attributes' => ['min' => 1, 'max' => 100],
        ],
        [
            'name' => 'striker_index',
            'label' => 'Striker Index (1-100)',
            'type' => 'number',
            'attributes' => ['min' => 1, 'max' => 100],
        ],
        [
            'name' => 'supporter_strength',
            'label' => 'Supporter Strength (1-50)',
            'type' => 'number',
            'attributes' => ['min' => 1, 'max' => 50],
        ],
    ];

    protected $tableColumns = [
        [
            'title' => 'Team',
            'key' => 'name',
        ],
    ];

    protected $storeRules = [
        'name' => 'required|string|max:255',
        'strength' => 'required|integer|min:1|max:100',
        'home_advantage' => 'required|integer|min:0|max:20',
        'away_disadvantage' => 'required|integer|min:0|max:20',
        'goalkeeper_index' => 'required|integer|min:1|max:100',
        'striker_index' => 'required|integer|min:1|max:100',
        'supporter_strength' => 'required|integer|min:1|max:50',
    ];

    protected $updateRules = [
        'name' => 'required|string|max:255',
        'strength' => 'required|integer|min:1|max:100',
        'home_advantage' => 'required|integer|min:0|max:20',
        'away_disadvantage' => 'required|integer|min:0|max:20',
        'goalkeeper_index' => 'required|integer|min:1|max:100',
        'striker_index' => 'required|integer|min:1|max:100',
        'supporter_strength' => 'required|integer|min:1|max:50',
    ];
}