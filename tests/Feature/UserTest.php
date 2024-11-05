<?php

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
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

it('lists users correctly', function (?User $actingUser, int $status, ?bool $expectSuccess) {
    // When
    if ($actingUser) {
        Sanctum::actingAs($actingUser);
    }
    // Then
    $response = $this->getJson('/api/users');
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'relationships' => [
                        'roles' => [
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

it('shows user correctly', function (?User $actingUser, int $id, int $status, ?bool $expectSuccess) {
    // When
    if ($actingUser) {
        Sanctum::actingAs($actingUser);
    }
    // Then
    $response = $this->getJson("/api/users/{$id}");
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $user = User::find($id);
        $response->assertJson([
            'data' => [
                'id' => $id,
                'type' => 'users',
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ],
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => $user->roles()->first()->id,
                                'type' => 'roles',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
})->with([
    'admin views self' => [fn () => $this->adminUser, fn () => $this->adminUser->id, 200, true],
    'admin views other' => [fn () => $this->adminUser, fn () => $this->customerUser->id, 200, true],
    'customer views self' => [fn () => $this->customerUser, fn () => $this->customerUser->id, 200, true],
    'customer views other' => [fn () => $this->customerUser, fn () => $this->adminUser->id, 403, null],
    'guest' => [null, fn () => $this->adminUser->id, 401, null],
]);

it('creates user correctly', function () {
    // When
    $start = Carbon::now()->millisecond(0);
    // Then
    $response = $this->postJson('/api/users', [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'Aaron A. Aaronson',
                'email' => 'name@exmaple.com',
                'password' => '123',
                'repeated_password' => '123',
            ],
        ],
    ]);
    // Assert
    $response->assertCreated();
    $now = Carbon::now();
    $response->assertJsonPath('data.type', 'users');
    $response->assertJsonPath('data.attributes.name', 'Aaron A. Aaronson');
    $response->assertJsonPath('data.attributes.email', 'name@exmaple.com');
    $this->assertDatabaseCount('users', 3);
    $this->assertDatabaseHas('users', [
        'email' => 'name@exmaple.com',
    ]);
    $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.created_at'))->between($start, $now));
    $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.updated_at'))->between($start, $now));
    $user = User::findOrFail(Arr::get($response, 'data.id'));
    $this->assertTrue(Hash::check('123', $user->password));
});

it('validates user creation correctly', function (array $body) {
    // When
    // Then
    $response = $this->postJson('/api/users', [
        'data' => [
            'type' => 'users',
            'attributes' => $body,
        ],
    ]);
    // Assert
    $response->assertUnprocessable();
})->with([
    'duplicated email' => fn () => [
        'name' => 'Aaron A. Aaronson',
        'email' => $this->adminUser->email,
        'password' => '123',
        'repeated_password' => '123',
    ],
    'incorrect repeated password' => fn () => [
        'name' => 'Aaron A. Aaronson',
        'email' => 'name@exmaple.com',
        'password' => '123',
        'repeated_password' => 'abc',
    ],
]);

it('updates user attributes correctly', function (?User $actingUser, int $id, int $status, bool $expectSuccess) {
    // When
    if ($actingUser) {
        Sanctum::actingAs($actingUser);
    }
    $start = Carbon::now();
    // Then
    $response = $this->patchJson("/api/users/{$id}", [
        'data' => [
            'type' => 'users',
            'attributes' => [
                'name' => 'Aaron A. Aaronson',
                'password' => 'password',
                'repeated_password' => 'password',
            ],
        ],
    ]);
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $now = Carbon::now()->millisecond(0);
        $response->assertJsonPath('data.type', 'users');
        $response->assertJsonPath('data.attributes.name', 'Aaron A. Aaronson');
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.created_at'))->between($start, $now));
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.updated_at'))->lessThanOrEqualTo($start));

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'name' => 'Aaron A. Aaronson',
        ]);
        $user = User::findOrFail($id);
        $this->assertTrue(Hash::check('password', $user->password));
    }
})->with([
    'admin updates self' => [fn () => $this->adminUser, fn () => $this->adminUser->id, 200, true],
    'admin updates other' => [fn () => $this->adminUser, fn () => $this->customerUser->id, 200, true],
    'customer updates self' => [fn () => $this->customerUser, fn () => $this->customerUser->id, 200, true],
    'customer updates other' => [fn () => $this->customerUser, fn () => $this->adminUser->id, 403, false],
    'guest' => [null, fn () => $this->adminUser->id, 401, false],
]);

it('updates user roles correctly', function (?User $actingUser, int $id, int $status, bool $expectSuccess) {
    // When
    if ($actingUser) {
        Sanctum::actingAs($actingUser);
    }
    $user = User::findOrFail($id);
    $this->assertEquals(1, $user->roles()->count());
    $start = Carbon::now();
    // Then
    $response = $this->patchJson("/api/users/{$id}", [
        'data' => [
            'type' => 'users',
            'attributes' => [],
            'relationships' => [
                'roles' => [
                    'data' => [
                        [
                            'id' => $this->adminRole->id,
                            'type' => 'roles',
                        ],
                        [
                            'id' => $this->customerRole->id,
                            'type' => 'roles',
                        ],
                    ],
                ],
            ],
        ],
    ]);
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $now = Carbon::now()->millisecond(0);
        $response->assertJsonPath('data.type', 'users');
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.created_at'))->between($start, $now));
        $this->assertTrue(Carbon::parse(Arr::get($response, 'data.attributes.updated_at'))->lessThanOrEqualTo($start));

        $user->refresh();
        $this->assertEquals(2, $user->roles()->count());
    }
})->with([
    'admin updates self' => [fn () => $this->adminUser, fn () => $this->adminUser->id, 200, true],
    'admin updates other' => [fn () => $this->adminUser, fn () => $this->customerUser->id, 200, true],
    'customer updates self' => [fn () => $this->customerUser, fn () => $this->customerUser->id, 422, false],
    'customer updates other' => [fn () => $this->customerUser, fn () => $this->adminUser->id, 403, false],
    'guest' => [null, fn () => $this->adminUser->id, 401, false],
]);

it('deletes user correctly', function (?User $actingUser, int $status, bool $expectSuccess) {
    // When
    if ($actingUser) {
        Sanctum::actingAs($actingUser);
    }
    $id = $this->customerUser->id;
    // Then
    $response = $this->deleteJson("/api/users/{$id}");
    // Assert
    $response->assertStatus($status);
    if ($expectSuccess) {
        $this->assertDatabaseMissing('users', [
            'id' => $id,
        ]);
    }
})->with([
    'admin' => [fn () => $this->adminUser, 204, true],
    'customer' => [fn () => $this->customerUser, 204, true],
    'guest' => [null, 401, false],
]);
