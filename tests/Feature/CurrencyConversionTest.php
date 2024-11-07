<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CurrencyConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_conversion_endpoint()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => 100
        ];

        Http::fake([
            'data.fixer.io/api/latest' => Http::response([
                'rates' => [
                    'USD' => 1,
                    'EUR' => 0.85
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'source_currency',
                'target_currency',
                'amount',
                'converted_amount',
                'rate',
                'timestamp'
            ]);
    }

    public function test_invalid_source_currency()
    {
        $payload = [
            'source_currency' => 'INVALID',
            'target_currency' => 'EUR',
            'amount' => 100
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['source_currency']);
    }

    public function test_invalid_target_currency()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'INVALID',
            'amount' => 100
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['target_currency']);
    }


    public function test_missing_parameters()
    {
        $payload = [
            'source_currency' => 'USD',
            'amount' => 100,
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['target_currency']);
    }

    public function test_invalid_amount_zero()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => 0
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_invalid_amount_negative()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => -100
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_successful_conversion()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => 100
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'source_currency',
                'target_currency',
                'amount',
                'converted_amount',
                'rate',
                'timestamp'
            ]);

        $data = $response->json();
        $this->assertEquals($payload['source_currency'], $data['source_currency']);
        $this->assertEquals($payload['target_currency'], $data['target_currency']);
        $this->assertGreaterThan(0, $data['rate']);
        $this->assertGreaterThan(0, $data['converted_amount']);
    }

    public function test_large_amount_conversion()
    {
        $payload = [
            'source_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => 1000000
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'source_currency',
                'target_currency',
                'amount',
                'converted_amount',
                'rate',
                'timestamp'
            ]);
    }
}

