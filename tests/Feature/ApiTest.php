<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Url;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_new_encode_can_be_created_and_decoded(): void
    {
        /**
         * 1) Test encoding a new link
         * 2) Trying to encode the new link again
         * 3) And then decoding the link
         */
        $response = $this->postJson('/api/encode', [
            'url' => 'https://www.google.com/',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->postJson('/api/encode', [
            'url' => 'https://www.google.com/',
        ]);

        $response_data = $response->json();

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->postJson('/api/decode', [
            'url' => $response_data['encodedUrl'],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'decodedUrl' => 'https://www.google.com/',
            ]);
    }

    public function test_that_missing_link_is_404(): void
    {
        $response = $this->postJson('/api/decode', [
            'url' => "abcdef",
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_it_allows_decode_requests_until_rate_limit_is_reached(): void
    {
        // 100 allowed per minute
        for ($i = 0; $i < 100; $i++) {
            $response = $this->postJson('/api/decode', [
                'url' => "abcdef",
            ]);
            $response->assertStatus(404);
        }

        // 101st request should be blocked due to rate limiting
        $response = $this->postJson('/api/decode', [
            'url' => "abcdef",
        ]);
        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Too many requests, please wait before trying again',
        ]);
    }

    public function test_it_allows_encode_requests_until_rate_limit_is_reached(): void
    {
        // 10 allowed per minute
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/encode', [
                'url' => "https://www.google.com/",
            ]);
            $this->assertTrue(in_array($response->status(), [200, 201]), $response->status());
        }

        // 11th request should be blocked due to rate limiting
        $response = $this->postJson('/api/encode', [
            'url' => "https://www.google.com/",
        ]);
        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Too many requests, please wait before trying again',
        ]);
    }

    public function test_old_urls_are_pruned(): void
    {
        Url::create([
            'encoded' => '1234567890',
            'unencoded' => 'example_one',
            'expires_at' => now()->subDays(1),
        ]);

        Url::create([
            'encoded' => '1234567891',
            'unencoded' => 'example_two',
            'expires_at' => now()->addDays(1),
        ]);

        $this->assertEquals(2, Url::count());

        // URLs older than 10 days are pruned
        $this->artisan('model:prune');

        $this->assertEquals(1, Url::count());
    }
}