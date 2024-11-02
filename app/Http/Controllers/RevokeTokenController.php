<?php

namespace App\Http\Controllers;

use App\Http\Resources\TokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class RevokeTokenController
{
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return Response::noContent();
    }
}
