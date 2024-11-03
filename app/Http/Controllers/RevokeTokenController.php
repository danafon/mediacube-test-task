<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;

class RevokeTokenController extends Controller
{
    public function __invoke(Request $request): HttpResponse
    {
        /** @var ?User $user */
        $user = $request->user();
        /** @var PersonalAccessToken $token */
        $token = $user?->currentAccessToken();
        $token->delete();

        return Response::noContent();
    }
}
