<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var NewAccessToken $this
         */
        return [
            'type' => 'tokens',
            'attributes' => [
                'sanctum' => $this->plainTextToken,
            ],
        ];
    }
}
