<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_paginated_articles()
    {
        Article::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/articles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    /** @test */
    public function it_fetches_a_single_article()
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/v1/articles/{$article->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $article->id]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_article()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/articles/999')
            ->assertStatus(404);
    }
}
