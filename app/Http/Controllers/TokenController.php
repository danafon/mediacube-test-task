<?php

namespace App\Http\Controllers;

use App\Http\Resources\TokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenController
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'data.attributes.email' => 'required|email',
            'data.attributes.password' => 'required',
            'data.attributes.device_name' => 'required',
        ]);
        $attributes = Arr::get($validated, 'data.attributes');

        $user = User::where('email', $attributes['email'])->first();

        if (! $user || ! Hash::check($attributes['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return new TokenResource($user->createToken($attributes['device_name']));
    }
}
