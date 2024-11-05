<?php

namespace App\Http\Controllers;

use App\Exceptions\DBConflictException;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): RoleCollection
    {
        Gate::authorize('viewAny', Role::class);

        /** @var int $limit */
        $limit = $request->get('limit', 10);
        $roles = Role::with('users')->paginate($limit);

        return new RoleCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RoleResource
    {
        Gate::authorize('create', Role::class);
        $validated = $request->validated();
        /** @var array<string,mixed> $attributes */
        $attributes = Arr::get($validated, 'data.attributes');
        $role = Role::create($attributes);

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): RoleResource
    {
        Gate::authorize('view', $role);

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        Gate::authorize('update', $role);
        $validated = $request->validated();

        /** @var array<string,mixed> $attributes */
        $attributes = Arr::get($validated, 'data.attributes');
        $role->update($attributes);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): HttpResponse
    {
        Gate::authorize('delete', $role);
        if ($role->users()->exists()) {
            throw new DBConflictException('user', 'role');
        }
        $role->delete();

        return Response::noContent();
    }
}
