<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_search_posts(): void
    {
        $user = User::factory()->create([
            'account_type' => 'pro',
        ]);

        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/posts', [
            'title' => 'Offre de service mobile',
            'content' => 'Nous proposons une application mobile clé en main.',
            'company_id' => $company->id,
        ])->assertCreated();

        Post::factory()->create([
            'title' => 'Autre publication',
            'content' => 'Texte',
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->getJson('/api/posts?search=mobile');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Offre de service mobile']);
    }
}
