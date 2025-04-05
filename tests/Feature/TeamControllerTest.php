<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_display_a_list_of_teams()
    {
        // Create some teams
        Team::factory()->count(3)->create();

        $response = $this->get(route('teams.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Teams/Index')
            ->has('resource.data', 3)
        );
    }

    public function test_can_display_the_create_team_form()
    {
        $response = $this->get(route('teams.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Teams/Create')
            ->has('schema')
            ->where('routePrefix', 'teams')
        );
    }

    public function test_can_create_a_new_team()
    {
        $teamData = [
            'name' => 'New Test Team',
            'strength' => 80,
            'home_advantage' => 15,
            'away_disadvantage' => 10,
            'goalkeeper_index' => 75,
            'striker_index' => 85,
            'supporter_strength' => 45
        ];

        $response = $this->post(route('teams.store'), $teamData);

        $response->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', ['name' => 'New Test Team']);
    }

    public function test_can_display_a_specific_team()
    {
        $team = Team::factory()->create();

        $response = $this->get(route('teams.show', $team));

        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Teams/Show')
            // ->has('resource.data', fn ($assert) => dd($assert->has('strength', $team->strength), $team->strength))
            ->has('resource.data')
            ->has('resource.data', fn ($assert) => $assert
                ->has('strength')
                ->has('home_advantage')
                ->has('away_disadvantage')
                ->has('goalkeeper_index')
                ->has('striker_index')
                ->has('supporter_strength')
                ->has('name')
                ->has('id')
                ->where('strength', $team->strength)
            )
        );
    }

    public function test_can_display_the_edit_team_form()
    {
        $team = Team::factory()->create();

        $response = $this->get(route('teams.edit', $team));

        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Teams/Edit')
            ->has('resource.data', fn ($assert) => $assert
                ->where('id', $team->id)
                ->where('name', $team->name)
                ->where('goalkeeper_index', $team->goalkeeper_index)
                ->where('striker_index', $team->striker_index)
                ->where('supporter_strength', $team->supporter_strength)
                ->where('strength', $team->strength)
                ->where('home_advantage', $team->home_advantage)
                ->where('away_disadvantage', $team->away_disadvantage)
            )
        );
    }

    public function test_can_update_a_team()
    {
        $team = Team::factory()->create();

        $updatedData = [
            'name' => 'Updated Team Name',
            'strength' => 90,
            'home_advantage' => 15,
            'away_disadvantage' => 10,
            'goalkeeper_index' => 75,
            'striker_index' => 85,
            'supporter_strength' => 70
        ];

        $response = $this->put(route('teams.update', $team), $updatedData);

        $response->assertRedirect(route('teams.index'));
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Updated Team Name',
            'strength' => 90
        ]);
    }

    public function test_can_delete_a_team()
    {
        $team = Team::factory()->create();

        $response = $this->delete(route('teams.destroy', $team));

        $response->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}