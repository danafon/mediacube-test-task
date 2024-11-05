<?php

// use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;

// use function Pest\Stressless\stress;

// uses(RefreshDatabase::class);

// beforeEach(function () {
//     $this->user = User::factory()
//         ->state([
//             'email' => 'user@example.com',
//             'password' => '1234',
//         ])
//         ->create();
//     $this->token = $this->user->createToken('testing')->plainTextToken;
// });

// test('stress test for token issuing', function () {
//     stress('http://localhost/api/token')
//         ->post([
//             'data' => [
//                 'attributes' => [
//                     'device_name' => 'test machine',
//                     'email' => 'user@example.com',
//                     'password' => '1234',
//                 ],
//             ],
//         ])
//         ->dd();
// });

// test('stress test for user view', function () {
//     stress("http://localhost/api/users/{$this->user->id}", [
//         'Authorization' => 'Bearer ' . $this->token,
//     ])
//     ->get()
//     ->dd();
// });
