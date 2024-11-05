<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()
        ->state([
            'email' => 'user@example.com',
            'password' => '123',
        ])
        ->create();
});

it('issues token correctly', function () {
    // When
    // Then
    $response = $this->postJson('/api/token', [
        'data' => [
            'type' => 'tokens',
            'attributes' => [
                'device_name' => 'test machine',
                'email' => 'user@example.com',
                'password' => '123',
            ],
        ],
    ]);
    // Assert
    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'type',
            'attributes' => [
                'sanctum',
            ],
        ],
    ]);
});

it('validates password correctly', function () {
    // When
    // Then
    $response = $this->postJson('/api/token', [
        'data' => [
            'type' => 'tokens',
            'attributes' => [
                'device_name' => 'test machine',
                'email' => 'user@example.com',
                'password' => '1234',
            ],
        ],
    ]);
    // Assert
    $response->assertUnprocessable();
    $response->assertJsonPath('errors.email', ['The provided credentials are incorrect.']);
});

it('validates email correctly', function () {
    // When
    // Then
    $response = $this->postJson('/api/token', [
        'data' => [
            'type' => 'tokens',
            'attributes' => [
                'device_name' => 'test machine',
                'email' => 'user1@example.com',
                'password' => '123',
            ],
        ],
    ]);
    // Assert
    $response->assertUnprocessable();
    $response->assertJsonPath('errors.email', ['The provided credentials are incorrect.']);
});
