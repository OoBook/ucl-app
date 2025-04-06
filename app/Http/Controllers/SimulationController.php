<?php

namespace App\Http\Controllers;

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
        $currentWeek = Fixture::where('played', false)->min('week_number');
        if (is_null($currentWeek)) {
            $currentWeek = Fixture::max('week_number');
        }


        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('week_number', $currentWeek)
            ->get();

        // $predictions = $this->getPredictions($currentWeek);

        return Inertia::render('Simulation/Index', [
            'teams' => LeagueTableResource::collection($teams->map->leagueTable)->toArray($request),
            'currentWeek' => $currentWeek,
            'fixtures' => FixtureResource::collection($fixtures)->toArray($request),
            // 'predictions' => $predictions,
            'totalWeeks' => Fixture::max('week_number'),
        ]);
    }

    public function playNextWeek()
    {
        $currentWeek = Fixture::where('played', false)->min('week_number') ?? 1;

        // Get fixtures for this week
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('week_number', $currentWeek)
            ->get();

        foreach ($fixtures as $fixture) {
            $fixture->simulate();
        }

        // Update predictions if we're in week 4 or later
        // if ($currentWeek >= 4) {
        //     $this->updatePredictions($currentWeek);
        // }

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
                $fixture->simulate();
            }

            // Update predictions if we're in week 4 or later
            // if ($week >= 4) {
            //     $this->updatePredictions($week);
            // }
        }

        return redirect()->route('simulation.index');
    }

    public function resetData()
    {
        // Reset all fixtures and league tables
        Fixture::resetFixtures();

        // Clear predictions
        // ChampionshipPrediction::truncate();

        return redirect()->route('simulation.index');
    }

    private function updatePredictions($week)
    {
        // Get current league standings
        $standings = LeagueTable::with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();

        // Get remaining fixtures
        $remainingFixtures = Fixture::where('played', false)->get();
        $totalRemaining = $remainingFixtures->count();

        // Calculate championship probability for each team
        $totalPoints = $standings->sum('points');
        $leadingTeam = $standings->first();
        $maxPossiblePoints = [];

        // Calculate max possible points for each team
        foreach ($standings as $standing) {
            $maxPossible = $standing->points;

            // Add potential 3 points for each remaining match
            $teamRemainingMatches = $remainingFixtures->filter(function ($fixture) use ($standing) {
                return $fixture->home_team_id == $standing->team_id ||
                       $fixture->away_team_id == $standing->team_id;
            })->count();

            $maxPossible += $teamRemainingMatches * 3;
            $maxPossiblePoints[$standing->team_id] = $maxPossible;
        }

        // Calculate championship probabilities
        $totalWeeks = Fixture::max('week_number');
        $remainingWeeks = $totalWeeks - $week + 1;

        foreach ($standings as $standing) {
            // Basic probability calculations
            $pointGap = $leadingTeam->points - $standing->points;
            $maxPossible = $maxPossiblePoints[$standing->team_id];

            // If mathematically impossible, probability is 0
            if ($leadingTeam->team_id != $standing->team_id &&
                $leadingTeam->points > $maxPossible) {
                $probability = 0;
            }
            // If already clinched, probability is 100
            elseif ($standing->points > $maxPossiblePoints[$standings->where('team_id', '!=', $standing->team_id)->max('team_id')] ) {
                $probability = 100;
            }
            // Otherwise, calculate based on points, form, goal difference, etc.
            else {
                // Base probability on current points percentage
                $baseProbability = ($standing->points / ($totalPoints ?: 1)) * 100;

                // Adjust for remaining matches (fewer matches = closer to current standing)
                $weekFactor = ($totalWeeks - $remainingWeeks + 1) / $totalWeeks;

                // Adjust for form (goal difference)
                $formFactor = ($standing->goal_difference > 0) ?
                    1 + ($standing->goal_difference / 10) :
                    1 / (1 + abs($standing->goal_difference) / 10);

                $probability = $baseProbability * $weekFactor * $formFactor;

                // Normalize by position in table
                $position = $standings->search(function($item) use ($standing) {
                    return $item->team_id === $standing->team_id;
                }) + 1;

                $probability = $probability / ($position * 0.7);

                // Cap between 0 and 100
                $probability = min(100, max(0, $probability));
            }

            // Round to nearest integer
            $probability = round($probability);

            // Save prediction
            ChampionshipPrediction::updateOrCreate(
                ['team_id' => $standing->team_id, 'prediction_week' => $week],
                ['win_probability' => $probability]
            );
        }

        // Normalize probabilities to sum to 100
        $predictions = ChampionshipPrediction::where('prediction_week', $week)->get();
        $totalProbability = $predictions->sum('win_probability');

        if ($totalProbability > 0) {
            foreach ($predictions as $prediction) {
                $normalized = ($prediction->win_probability / $totalProbability) * 100;
                $prediction->update(['win_probability' => round($normalized)]);
            }
        }
    }

    private function getPredictions($week)
    {
        $predictions = ChampionshipPrediction::with('team')
            ->where('prediction_week', $week - 1)
            ->get();

        if ($predictions->isEmpty() && $week > 4) {
            $previousWeek = ChampionshipPrediction::max('prediction_week');
            if ($previousWeek) {
                $predictions = ChampionshipPrediction::with('team')
                    ->where('prediction_week', $previousWeek)
                    ->get();
            }
        }

        return ChampionshipPredictionResource::collection($predictions);
    }
}