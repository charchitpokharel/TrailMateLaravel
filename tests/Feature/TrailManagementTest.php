<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailManagementTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'name' => 'Test Mountain Trail',
            'location' => 'Lake District',
            'difficulty' => 'Easy',
            'distance_km' => 5.5,
            'description' => 'A scenic mountain walking trail.',
        ];
    }

    private function trail(User $user): Trail
    {
        return Trail::create(
            $this->payload() + ['user_id' => $user->id]
        );
    }

    public function test_trails_page_loads(): void
    {
        $this->get('/trails')->assertOk();
    }

    public function test_user_can_create_trail(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/trails', $this->payload())
            ->assertCreated();

        $this->assertDatabaseHas('trails', [
            'name' => 'Test Mountain Trail',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_trail(): void
    {
        $this->postJson('/trails', $this->payload())
            ->assertUnauthorized();
    }

    public function test_trail_can_be_viewed(): void
    {
        $trail = $this->trail(User::factory()->create());

        $this->get('/trails/'.$trail->id)
            ->assertOk()
            ->assertSee($trail->name);
    }

    public function test_owner_can_update_trail(): void
    {
        $user = User::factory()->create();
        $trail = $this->trail($user);
        $data = $this->payload();
        $data['name'] = 'Updated Mountain Trail';

        $this->actingAs($user)
            ->putJson('/trails/'.$trail->id, $data)
            ->assertOk();

        $this->assertDatabaseHas('trails', [
            'id' => $trail->id,
            'name' => 'Updated Mountain Trail',
        ]);
    }

    public function test_owner_can_delete_trail(): void
    {
        $user = User::factory()->create();
        $trail = $this->trail($user);

        $this->actingAs($user)
            ->deleteJson('/trails/'.$trail->id)
            ->assertOk();

        $this->assertDatabaseMissing('trails', [
            'id' => $trail->id,
        ]);
    }

    public function test_other_user_cannot_delete_trail(): void
    {
        $trail = $this->trail(User::factory()->create());

        $this->actingAs(User::factory()->create())
            ->deleteJson('/trails/'.$trail->id)
            ->assertForbidden();

        $this->assertDatabaseHas('trails', [
            'id' => $trail->id,
        ]);
    }

    public function test_invalid_trail_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/trails', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name', 'location', 'difficulty',
                'distance_km', 'description',
            ]);
    }
}
