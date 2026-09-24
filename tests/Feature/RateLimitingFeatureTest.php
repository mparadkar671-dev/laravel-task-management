<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_is_throttled_after_exceeding_rate_limit(): void
    {
        $user = User::factory()->create([
            'email' => 'victim@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        // First 5 attempts with wrong password
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/login', [
                'email' => 'victim@example.com',
                'password' => 'wrong-password',
            ]);
            $response->assertStatus(422);
        }

        // 6th attempt should be blocked by rate limiter (HTTP 429)
        $blockedResponse = $this->postJson('/api/v1/login', [
            'email' => 'victim@example.com',
            'password' => 'wrong-password',
        ]);

        $blockedResponse->assertStatus(429);
    }
}
