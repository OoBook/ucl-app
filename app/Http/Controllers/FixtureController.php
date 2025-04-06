<?php

namespace App\Http\Controllers;

use App\Facades\Simulation;
use App\Models\Fixture;
use App\Models\Team;
use App\Http\Resources\TeamResource;
use App\Models\ChampionshipPrediction;
use App\Models\LeagueTable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FixtureController extends Controller
{
    public function index(Request $request)
    {
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->orderBy('week_number')
            ->get()
            ->groupBy('week_number');

        return Inertia::render('Fixtures/Index', [
            'fixtures' => $fixtures,
            'teams' => TeamResource::collection(Team::all())->toArray($request),
        ]);
    }

    public function generate(Request $request)
    {
        // Get all teams
        $teams = Team::take(4)->get();

        // Make sure we have at least 4 teams
        if ($teams->count() < 4) {
            return back()
                ->with('message', 'At least 4 teams are required to generate fixtures.')
                ->with('variant', 'error');
        }

        // Clear existing fixtures
        Fixture::truncate();

        $teamIds = $teams->pluck('id')->toArray();

        $fixtures = Simulation::generateFixtures($teamIds);

        // Insert fixtures into database
        foreach ($fixtures as $fixture) {
            Fixture::create($fixture);
        }

        return redirect()->route('fixtures.index');
    }


    public function clearFixtures()
    {
        Fixture::truncate();
        LeagueTable::resetStats();
        ChampionshipPrediction::resetPredictions();

        return redirect()->route('fixtures.index');
    }
}
