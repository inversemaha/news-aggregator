<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_user_preferences()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/preferences', [
                'categories' => ['Technology', 'Sports'],
                'sources' => ['BBC', 'CNN'],
                'authors' => ['Author1', 'Author2'],
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'User preference has been updated successfully.']);
    }

    /** @test */
    public function it_fetches_personalized_feed_based_on_preferences()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/personalized-feed')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function it_returns_unauthorized_when_not_logged_in()
    {
        $response = $this->postJson('/api/v1/preferences', [
            'categories' => ['Technology'],
        ]);

        $response->assertStatus(401);
    }
}
