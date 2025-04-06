<?php

namespace App\Http\Controllers;

use App\Facades\Simulation;
use App\Models\Fixture;
use App\Models\LeagueTable;
use App\Models\Team;
use App\Models\ChampionshipPrediction;
use App\Http\Resources\FixtureResource;
use App\Http\Resources\LeagueTableResource;
use App\Http\Resources\ChampionshipPredictionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SimulationController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::with('leagueTable')->get();
        $currentWeek = Fixture::where('played', true)->max('week_number');
        $nextWeek = Fixture::where('played', false)->min('week_number');

        if (is_null($currentWeek)) {
            $currentWeek = $nextWeek;
        }

        $fixtures = Fixture::where('week_number', $currentWeek)
            ->get();

        $predictions = ChampionshipPrediction::all();

        return Inertia::render('Simulation/Index', [
            'teams' => LeagueTableResource::collection($teams->map->leagueTable)->toArray($request),
            'currentWeek' => $currentWeek,
            'nextWeek' => $nextWeek,
            'fixtures' => FixtureResource::collection($fixtures)->toArray($request),
            'predictions' => ChampionshipPredictionResource::collection($predictions)->toArray($request),
            'totalWeeks' => Fixture::max('week_number'),
        ]);
    }

    public function playNextWeek()
    {
        $possibleLastWeek = Fixture::max('week_number');
        $currentWeek = Fixture::where('played', false)->min('week_number') ?? 1;

        // Get fixtures for this week
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('week_number', $currentWeek)
            ->get();

        foreach ($fixtures as $fixture) {
            Simulation::simulateMatch($fixture);
        }

        $probabilities = Simulation::calculateChampionshipProbabilities($currentWeek);

        if ($currentWeek === $possibleLastWeek) {
            ChampionshipPrediction::resetPredictions();
        } else {
            foreach ($probabilities as $teamId => $probability) {
                $team = Team::find($teamId);
                $team->championshipPrediction()->update(['win_probability' => $probability, 'prediction_week' => $currentWeek]);
            }
        }

        return redirect()->route('simulation.index');
    }

    public function playAllWeeks()
    {
        $maxWeek = Fixture::max('week_number');
        $currentWeek = Fixture::where('played', false)->min('week_number') ?? 1;

        for ($week = $currentWeek; $week <= $maxWeek; $week++) {
            $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
                ->where('week_number', $week)
                ->get();

            foreach ($fixtures as $fixture) {
                Simulation::simulateMatch($fixture);
            }
        }

        // Reset predictions after all weeks have been played
        ChampionshipPrediction::resetPredictions();

        return redirect()->route('simulation.index');
    }

    public function resetData()
    {
        // Reset all fixtures and league tables
        Fixture::resetFixtures();

        return redirect()->route('simulation.index');
    }
}