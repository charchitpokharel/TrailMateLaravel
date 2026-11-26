<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailAdvancedTest extends TestCase
{
    use RefreshDatabase;

    private function trail(array $attributes = []): Trail
    {
        return Trail::create(array_merge([
            'name' => 'Mountain Trail',
            'location' => 'Lake District',
            'difficulty' => 'Easy',
            'distance_km' => 5.5,
            'description' => 'A beautiful scenic walking trail.',
            'user_id' => User::factory()->create()->id,
        ], $attributes));
    }

    public function test_search_by_name(): void
    {
        $this->trail();
        $this->trail(['name' => 'Forest Walk']);

        $this->getJson('/api/trails/search?q=Mountain')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Mountain Trail');
    }

    public function test_search_by_location(): void
    {
        $this->trail();
        $this->trail(['location' => 'Scotland']);

        $this->getJson('/api/trails/search?q=Scotland')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.location', 'Scotland');
    }

    public function test_search_by_difficulty(): void
    {
        $this->trail();
        $this->trail(['difficulty' => 'Hard']);

        $this->getJson('/api/trails/search?q=Hard')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.difficulty', 'Hard');
    }

    public function test_difficulty_filter(): void
    {
        $this->trail();
        $this->trail(['difficulty' => 'Hard']);

        $this->getJson('/trails?difficulty=Easy')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.difficulty', 'Easy');
    }

    public function test_pagination(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->trail(['name' => 'Trail '.$i]);
        }

        $this->getJson('/trails?page=1')
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('total', 12)
            ->assertJsonPath('last_page', 2);

        $this->getJson('/trails?page=2')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_empty_search_results(): void
    {
        $this->trail();

        $this->getJson('/api/trails/search?q=Nonexistent')
            ->assertOk()
            ->assertExactJson([]);

        $this->getJson('/api/trails/search?q=a')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_other_user_cannot_update_trail(): void
    {
        $trail = $this->trail();

        $this->actingAs(User::factory()->create())
            ->putJson('/trails/'.$trail->id, [
                'name' => 'Unauthorised Change',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('trails', [
            'id' => $trail->id,
            'name' => 'Mountain Trail',
        ]);
    }

    public function test_admin_can_delete_any_trail(): void
    {
        $trail = $this->trail();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->deleteJson('/trails/'.$trail->id)
            ->assertOk();

        $this->assertDatabaseMissing('trails', [
            'id' => $trail->id,
        ]);
    }

    public function test_invalid_difficulty_and_distance(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/trails', [
                'name' => 'Invalid Trail',
                'location' => 'London',
                'difficulty' => 'Impossible',
                'distance_km' => -10,
                'description' => 'Testing invalid trail information.',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'difficulty', 'distance_km',
            ]);
    }

    public function test_missing_trail_returns_404(): void
    {
        $this->get('/trails/999999')
            ->assertNotFound();
    }
}
