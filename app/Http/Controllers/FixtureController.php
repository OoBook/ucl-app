<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Team;
use App\Http\Resources\TeamResource;
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

        // Randomize the team order while preserving the correct match structure
        shuffle($teamIds);

        $teamCount = count($teamIds);
        $rounds = ($teamCount - 1);
        $fixtures = [];

        // Generate first half of the season using round robin algorithm
        $this->generateRoundRobin($teamIds, 1, $fixtures);

        // Create return fixtures for second half of the season
        $firstHalfFixtureCount = count($fixtures);
        for ($i = 0; $i < $firstHalfFixtureCount; $i++) {
            $fixtures[] = [
                'week_number' => $fixtures[$i]['week_number'] + $rounds,
                'home_team_id' => $fixtures[$i]['away_team_id'],
                'away_team_id' => $fixtures[$i]['home_team_id'],
                'played' => false
            ];
        }

        // Further randomize the weeks (within each half of the season)
        $this->randomizeFixtureWeeks($fixtures, $teamCount);

        // Insert fixtures into database
        foreach ($fixtures as $fixture) {
            Fixture::create($fixture);
        }

        return redirect()->route('fixtures.index');
    }

    /**
     * Generate single round-robin schedule
     * Uses the circle method which guarantees each team plays every other team exactly once
     */
    private function generateRoundRobin(array $teamIds, int $startWeek, array &$fixtures)
    {
        $teamCount = count($teamIds);
        $rounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;

        // If teamCount is odd, add a dummy team for byes
        $hasDummy = false;
        if ($teamCount % 2 == 1) {
            $teamIds[] = null; // Dummy team
            $teamCount++;
            $rounds = $teamCount - 1;
            $matchesPerRound = $teamCount / 2;
            $hasDummy = true;
        }

        // Take the first team out and hold it fixed
        $fixedTeam = array_shift($teamIds);

        for ($round = 0; $round < $rounds; $round++) {
            $weekNumber = $startWeek + $round;
            $roundTeams = $teamIds;

            // Rotate the array by 1 position
            if ($round > 0) {
                $lastTeam = array_pop($teamIds);
                array_unshift($teamIds, $lastTeam);
                $roundTeams = $teamIds;
            }

            // Add the fixed team back for this round's matches
            array_unshift($roundTeams, $fixedTeam);

            // Create fixtures for this round
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $homeIndex = $match;
                $awayIndex = $teamCount - 1 - $match;

                $homeTeamId = $roundTeams[$homeIndex];
                $awayTeamId = $roundTeams[$awayIndex];

                // Skip if this involves the dummy team
                if ($hasDummy && ($homeTeamId === null || $awayTeamId === null)) {
                    continue;
                }

                // Randomly decide home/away to add more variety
                if (rand(0, 1) == 0) {
                    $fixtures[] = [
                        'week_number' => $weekNumber,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'played' => false
                    ];
                } else {
                    $fixtures[] = [
                        'week_number' => $weekNumber,
                        'home_team_id' => $awayTeamId,
                        'away_team_id' => $homeTeamId,
                        'played' => false
                    ];
                }
            }
        }
    }

    /**
     * Randomize fixture weeks while maintaining constraints
     */
    private function randomizeFixtureWeeks(array &$fixtures, int $teamCount)
    {
        $halfSeason = count($fixtures) / 2;

        // Group fixtures by week for first half of season
        $firstHalfWeeks = [];
        for ($i = 0; $i < $halfSeason; $i++) {
            $week = $fixtures[$i]['week_number'];
            if (!isset($firstHalfWeeks[$week])) {
                $firstHalfWeeks[$week] = [];
            }
            $firstHalfWeeks[$week][] = $i;
        }

        // Group fixtures by week for second half of season
        $secondHalfWeeks = [];
        for ($i = $halfSeason; $i < count($fixtures); $i++) {
            $week = $fixtures[$i]['week_number'];
            if (!isset($secondHalfWeeks[$week])) {
                $secondHalfWeeks[$week] = [];
            }
            $secondHalfWeeks[$week][] = $i;
        }

        // Shuffle week order within each half
        $firstHalfWeekNumbers = array_keys($firstHalfWeeks);
        shuffle($firstHalfWeekNumbers);

        $secondHalfWeekNumbers = array_keys($secondHalfWeeks);
        shuffle($secondHalfWeekNumbers);

        // Reassign weeks
        $weekIndex = 0;
        foreach ($firstHalfWeekNumbers as $originalWeek) {
            $newWeek = $weekIndex + 1;
            foreach ($firstHalfWeeks[$originalWeek] as $fixtureIndex) {
                $fixtures[$fixtureIndex]['week_number'] = $newWeek;
            }
            $weekIndex++;
        }

        $weekIndex = 0;
        foreach ($secondHalfWeekNumbers as $originalWeek) {
            $newWeek = $weekIndex + 1 + ($teamCount - 1);
            foreach ($secondHalfWeeks[$originalWeek] as $fixtureIndex) {
                $fixtures[$fixtureIndex]['week_number'] = $newWeek;
            }
            $weekIndex++;
        }
    }

    public function clearFixtures()
    {
        Fixture::truncate();
        return redirect()->route('fixtures.index');
    }
}
