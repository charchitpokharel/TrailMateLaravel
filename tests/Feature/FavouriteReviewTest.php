<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavouriteReviewTest extends TestCase
{
    use RefreshDatabase;

    private function trail(): Trail
    {
        return Trail::create([
            'name' => 'Mountain Walking Trail',
            'location' => 'Lake District',
            'difficulty' => 'Easy',
            'distance_km' => 5.5,
            'description' => 'A beautiful scenic walking trail.',
            'user_id' => User::factory()->create()->id,
        ]);
    }

    private function review(): array
    {
        return [
            'reviewer_name' => 'Test Reviewer',
            'rating' => 5,
            'comment' => 'Excellent walking trail.',
        ];
    }

    public function test_empty_favourites_page(): void
    {
        $this->get('/favourites')
            ->assertOk()
            ->assertViewHas('trails', fn ($trails) => $trails->isEmpty());
    }

    public function test_add_and_remove_favourite(): void
    {
        $trail = $this->trail();
        $url = '/api/favourites/'.$trail->id;

        $this->postJson($url)
            ->assertOk()
            ->assertJson(['saved' => true, 'count' => 1])
            ->assertSessionHas('favourites', [$trail->id]);

        $this->postJson($url)
            ->assertOk()
            ->assertJson(['saved' => false, 'count' => 0])
            ->assertSessionHas('favourites', []);
    }

    public function test_saved_favourite_is_displayed(): void
    {
        $trail = $this->trail();

        $this->postJson('/api/favourites/'.$trail->id)
            ->assertOk();

        $this->get('/favourites')
            ->assertOk()
            ->assertSee($trail->name);
    }

    public function test_deleted_favourite_is_not_displayed(): void
    {
        $trail = $this->trail();
        $id = $trail->id;
        $trail->delete();

        $this->withSession(['favourites' => [$id]])
            ->get('/favourites')
            ->assertOk()
            ->assertViewHas('trails', fn ($trails) => $trails->isEmpty());
    }

    public function test_missing_favourite_returns_404(): void
    {
        $this->postJson('/api/favourites/999999')
            ->assertNotFound();
    }

    public function test_review_page_loads(): void
    {
        $trail = $this->trail();

        $this->get('/reviews/'.$trail->id)
            ->assertOk();
    }

    public function test_review_can_be_submitted(): void
    {
        $trail = $this->trail();

        $this->postJson('/api/reviews/'.$trail->id, $this->review())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('review.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'trail_id' => $trail->id,
            'reviewer_name' => 'Test Reviewer',
            'rating' => 5,
        ]);
    }

    public function test_reviews_belong_to_correct_trail(): void
    {
        $trail = $this->trail();
        $otherTrail = $this->trail();

        $trail->reviews()->create($this->review());
        $otherTrail->reviews()->create($this->review());

        $this->getJson('/reviews/'.$trail->id)
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.trail_id', $trail->id);
    }

    public function test_empty_review_is_rejected(): void
    {
        $trail = $this->trail();

        $this->postJson('/api/reviews/'.$trail->id, [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'reviewer_name', 'rating', 'comment',
            ]);
    }

    public function test_invalid_ratings_are_rejected(): void
    {
        $trail = $this->trail();

        foreach ([0, 6, -1, 2.5, 'invalid'] as $rating) {
            $data = $this->review();
            $data['rating'] = $rating;

            $this->postJson('/api/reviews/'.$trail->id, $data)
                ->assertUnprocessable()
                ->assertJsonValidationErrors('rating');
        }

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_for_missing_trail_is_rejected(): void
    {
        $this->postJson('/api/reviews/999999', $this->review())
            ->assertNotFound();
    }

    public function test_deleting_trail_removes_reviews(): void
    {
        $trail = $this->trail();
        $review = $trail->reviews()->create($this->review());

        $trail->delete();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
