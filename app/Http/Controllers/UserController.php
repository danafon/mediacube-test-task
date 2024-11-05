<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): UserCollection
    {
        Gate::authorize('viewAny', User::class);

        /** @var int $limit */
        $limit = $request->get('limit', 10);
        $roles = User::with(['roles'])->paginate($limit);

        return new UserCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $validated = $request->validated();
        /** @var array<string,mixed> $attributes */
        $attributes = Arr::get($validated, 'data.attributes');
        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
        ]);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        Gate::authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        Gate::authorize('update', $user);
        $validated = $request->validated();
        /** @var array<string,mixed> $attributes */
        $attributes = Arr::get($validated, 'data.attributes');
        $user->update($attributes);

        /** @var array<string,mixed> $relationships */
        $relationships = Arr::get($validated, 'data.relationships');
        if ($relationships !== null) {
            foreach ($relationships as $relationship => $data) {
                /** @var array<string,array<string,mixed>> $data */
                if ($relationship === 'roles') {
                    /** @var User */
                    $authorizedUser = Auth::user();
                    if (! $authorizedUser->isAdministrator()) {
                        throw ValidationException::withMessages([
                            'roles' => ['Only admin can update roles.'],
                        ]);
                    }
                    $user->roles()->sync(Arr::pluck($data['data'], 'id'));

                    Cache::forget('users.'.$user->id.'.is_admin');
                }
            }
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): HttpResponse
    {
        Gate::authorize('delete', $user);
        $user->roles()->detach();
        $user->delete();

        return Response::noContent();
    }
}
