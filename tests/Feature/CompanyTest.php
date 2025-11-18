<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_list_companies(): void
    {
        $user = User::factory()->create([
            'account_type' => 'pro',
        ]);

        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/companies', [
            'name' => 'Nova Labs',
            'cfe_number' => 'CFE99887',
            'address' => '1 rue des Entrepreneurs',
        ]);

        $createResponse->assertCreated();

        $listResponse = $this->getJson('/api/companies');
        $listResponse->assertOk()
            ->assertJsonFragment(['name' => 'Nova Labs']);
    }
}
