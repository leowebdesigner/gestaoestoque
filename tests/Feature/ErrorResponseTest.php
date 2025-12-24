<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found_returns_standard_error_response(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sales/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }
}
