<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Fixture;
use App\Models\LeagueTable;
use App\Models\ChampionshipPrediction;

class SimulationService
{
    /**
     * Generate fixtures for the given teams using a round-robin algorithm
     */
    public function generateFixtures(array $teamIds): array
    {
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

        return $fixtures;
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

    /**
     * Simulate a match between two teams
     */
    public function simulateMatch(Fixture $fixture): void
    {
        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        // Calculate effective strengths with home/away adjustments
        $homeEffectiveStrength = $homeTeam->strength + $homeTeam->home_advantage + $homeTeam->supporter_strength / 10;
        $awayEffectiveStrength = $awayTeam->strength - $awayTeam->away_disadvantage;

        // Calculate goal probabilities based on team strengths and striker/goalkeeper indexes
        $homeGoalProbability = ($homeEffectiveStrength + $homeTeam->striker_index - $awayTeam->goalkeeper_index / 2) / 100;
        $awayGoalProbability = ($awayEffectiveStrength + $awayTeam->striker_index - $homeTeam->goalkeeper_index / 2) / 100;

        // Simulate goals (typically 0-5 goals per team)
        $homeGoals = $this->generateGoals($homeGoalProbability);
        $awayGoals = $this->generateGoals($awayGoalProbability);

        $fixture->saveScore($homeGoals, $awayGoals);
    }

    /**
     * Generate number of goals based on probability
     */
    private function generateGoals($probability): int
    {
        // Base number of goals
        $baseGoals = rand(0, 3);

        // Adjust based on probability (higher probability = more likely to score additional goals)
        $additionalGoals = 0;
        if (rand(0, 100) / 100 < $probability) {
            $additionalGoals += rand(0, 2);
        }

        return $baseGoals + $additionalGoals;
    }

    /**
     * Calculate championship probabilities for each team
     */
    public function calculateChampionshipProbabilities(int $currentWeek): array
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
        $probabilities = [];


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
        $remainingWeeks = $totalWeeks - $currentWeek + 1;

        foreach ($standings as $standing) {
            // First, check mathematical possibility based on remaining matches
            $maxPossiblePointsForTeam = $standing->points + ($teamRemainingMatches * 3);
            $leaderMaxPoints = $leadingTeam->points;

            // If it's mathematically impossible to catch the leader
            if ($maxPossiblePointsForTeam < $leaderMaxPoints) {
                $probability = 0;
            }
            // If team has already clinched (no other team can catch them)
            elseif ($standing->team_id === $leadingTeam->team_id &&
                    $standing->points > $maxPossiblePoints[array_keys($maxPossiblePoints)[1] ?? 0]) {
                $probability = 100;
            }
            // For all other cases, calculate probability
            else {
                // Get total weeks and current week for progress calculation
                $totalWeeks = Fixture::max('week_number');
                $currentWeek = $totalWeeks - $remainingWeeks + 1;

                // Progressive points influence factor that increases each week
                // Week 1: points have minimal impact
                // Final week: points have maximum impact
                $pointsInfluenceFactor = min(1.0, $currentWeek / ($totalWeeks * 0.75));

                // Team strength component
                $team = $standing->team;
                $teamStrengthFactor = (
                    ($team->strength * 0.5) +
                    ($team->striker_index * 0.25) +
                    ($team->goalkeeper_index * 0.15) +
                    ($team->supporter_strength * 0.1)
                ) / 100;

                // Get average team attributes for normalization
                $totalTeams = Team::count();
                $allTeamStrengths = Team::sum('strength') / $totalTeams;
                $allStrikerIndex = Team::sum('striker_index') / $totalTeams;
                $allGoalkeeperIndex = Team::sum('goalkeeper_index') / $totalTeams;
                $allSupporterStrength = Team::sum('supporter_strength') / $totalTeams;

                $avgTeamFactor = (
                    ($allTeamStrengths * 0.5) +
                    ($allStrikerIndex * 0.25) +
                    ($allGoalkeeperIndex * 0.15) +
                    ($allSupporterStrength * 0.1)
                ) / 100;

                // Normalize team strength (1.0 = average team)
                $normalizedStrength = $teamStrengthFactor / ($avgTeamFactor ?: 1);

                // Results component - weight increases each week
                $pointsComponent = ($standing->points / ($totalPoints ?: 1)) * 100;

                // Form factor (goal difference) - also increases in importance each week
                $formWeight = min(1.0, $currentWeek / ($totalWeeks * 0.5));
                $formFactor = ($standing->goal_difference > 0) ?
                    1 + (($standing->goal_difference / 10) * $formWeight) :
                    max(0.5, 1 - ((abs($standing->goal_difference) / 20) * $formWeight));

                // Dynamically calculate weights based on current week
                $strengthWeight = 1 - $pointsInfluenceFactor;
                $resultsWeight = $pointsInfluenceFactor;

                // Early season minimum chance based on team quality
                $earlySeasonFactor = max(0, 1 - ($currentWeek / ($totalWeeks * 0.3)));
                $minimumChance = max(0, (($normalizedStrength - 0.5) * 2) * $earlySeasonFactor * 15);

                // Calculate probability
                $probability = ($normalizedStrength * 100 * $strengthWeight) +
                              ($pointsComponent * $formFactor * $resultsWeight);

                // Position penalty decreases in early weeks, increases in later weeks
                $positionWeight = min(1.0, $currentWeek / ($totalWeeks * 0.6));
                $position = $standings->search(function ($team) use ($standing) {
                    return $team->team_id === $standing->team_id;
                }) + 1;
                $positionPenalty = 1 + (($position - 1) * 0.2 * $positionWeight);
                $probability = $probability / $positionPenalty;

                // Apply minimum chance in early season
                $probability = max($probability, $minimumChance);

                // Apply minor home advantage bonus
                if ($team->home_advantage > 10) {
                    $homeAdvBonus = ($team->home_advantage - 10) / 100;
                    $probability *= 1 + ($homeAdvBonus * (1 - $pointsInfluenceFactor));
                }

                // Apply minor away disadvantage penalty
                if ($team->away_disadvantage > 10) {
                    $awayDisBonus = ($team->away_disadvantage - 10) / 100;
                    $probability *= 1 - ($awayDisBonus * (1 - $pointsInfluenceFactor));
                }

                // Cap between 0 and 100
                $probability = min(100, max(0, $probability));
            }

            // Round to nearest integer
            $probabilities[$standing->team_id] = round($probability);
        }

        return $this->normalizeProbabilities($probabilities);
    }

    /**
     * Normalize probabilities to sum to 100%
     */
    public function normalizeProbabilities(array $probabilities): array
    {
        $total = array_sum($probabilities);

        if ($total <= 0) {
            return $probabilities;
        }

        $normalized = [];
        foreach ($probabilities as $teamId => $probability) {
            $normalized[$teamId] = round(($probability / $total) * 100);
        }

        return $normalized;
    }
}