<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var User $this
         */
        return [
            'id' => $this->id,
            'type' => 'users',
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            'relationships' => [
                'roles' => new RoleIdentifierCollection($this->roles),
            ],
        ];
    }
}
