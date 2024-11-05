<?php

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adminRole = Role::factory()
        ->state(['title' => 'admin'])
        ->create();
    $this->adminUser = User::factory()
        ->create();
    $this->adminRole->users()->attach($this->adminUser);

    $this->customerRole = Role::factory()
        ->state(['title' => 'customer'])
        ->create();
    $this->customerUser = User::factory()
        ->create();
    $this->customerRole->users()->attach($this->customerUser);
});

it('lists roles correctly', function (?User $user, int $status, ?bool $checkJson) {
    // When
    if ($user) {
        Sanctum::actingAs($user);
    }
    // Then
    $response = $this->getJson('/api/roles');
    // Assert
    $response->assertStatus($status);
    if ($checkJson) {
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                        'title',
                        'created_at',
                        'updated_at',
                    ],
                    'relationships' => [
                        'users' => [
                            'data' => [
                                '*' => [
                                    'id',
                                    'type',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'links',
            'meta',
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 200, true],
    'customer' => [fn () => $this->customerUser, 403, null],
    'guest' => [null, 401, null],
]);

it('shows role correctly', function (?User $user, int $status, ?bool $checkJson) {
    // When
    if ($user) {
        Sanctum::actingAs($user);
    }
    // Then
    $response = $this->getJson("/api/roles/{$this->adminRole->id}");
    // Assert
    $response->assertStatus($status);
    if ($checkJson) {
        $response->assertJson([
            'data' => [
                'id' => $this->adminRole->id,
                'type' => 'roles',
                'attributes' => [
                    'title' => 'admin',
                    'created_at' => $this->adminRole->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $this->adminRole->updated_at->format('Y-m-d H:i:s'),
                ],
                'relationships' => [
                    'users' => [
                        'data' => [
                            [
                                'id' => $this->adminUser->id,
                                'type' => 'users',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 200, true],
    'customer' => [fn () => $this->customerUser, 403, null],
    'guest' => [null, 401, null],
]);

it('creates role correctly', function (?User $user, int $status, bool $expectSuccess) {
    // When
    if ($user) {
        Sanctum::actingAs($user);
    }
    $start = Carbon::now()->millisecond(0);
    // Then
    $response = $this->postJson('/api/roles', [
        'data' => [
            'type' => 'roles',
            'attributes' => [
                'title' => 'courier',
            ],
        ],
    ]);
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $now = Carbon::now();
        $response->assertJsonPath('data.type', 'roles');
        $response->assertJsonPath('data.attributes.title', 'courier');
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.created_at'))->between($start, $now));
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.updated_at'))->between($start, $now));

        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseHas('roles', [
            'title' => 'courier',
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 201, true],
    'customer' => [fn () => $this->customerUser, 403, false],
    'guest' => [null, 401, false],
]);

it('updates role correctly', function (?User $user, int $status, bool $expectSuccess) {
    // When
    if ($user) {
        Sanctum::actingAs($user);
    }
    $start = Carbon::now();
    // Then
    $response = $this->patchJson("/api/roles/{$this->customerRole->id}", [
        'data' => [
            'type' => 'roles',
            'attributes' => [
                'title' => 'courier',
            ],
        ],
    ]);
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $now = Carbon::now()->millisecond(0);
        $response->assertJsonPath('data.type', 'roles');
        $response->assertJsonPath('data.attributes.title', 'courier');
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.created_at'))->between($start, $now));
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.updated_at'))->lessThanOrEqualTo($start));

        $this->assertDatabaseCount('roles', 2);
        $this->assertDatabaseHas('roles', [
            'title' => 'courier',
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 200, true],
    'customer' => [fn () => $this->customerUser, 403, false],
    'guest' => [null, 401, false],
]);

it('deletes role correctly', function (?User $user, int $status, bool $expectSuccess) {
    // When
    if ($user) {
        Sanctum::actingAs($user);
    }
    $this->customerRole->users()->detach();
    // Then
    $response = $this->deleteJson("/api/roles/{$this->customerRole->id}");
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $this->assertDatabaseMissing('roles', [
            'title' => 'customer',
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 204, true],
    'customer' => [fn () => $this->customerUser, 403, false],
    'guest' => [null, 401, false],
]);

it('does not delete a role with existing users', function () {
    // When
    Sanctum::actingAs($this->adminUser);
    // Then
    $response = $this->deleteJson("/api/roles/{$this->customerRole->id}");
    // Assert
    $response->assertStatus(409);
});
