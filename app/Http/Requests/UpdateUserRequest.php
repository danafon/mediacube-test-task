<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required',
            'data.type' => 'required|in:users',
            'data.attributes' => 'sometimes',
            'data.attributes.name' => 'sometimes|string',
            'data.attributes.email' => 'sometimes|email',
            'data.attributes.password' => 'sometimes|string',
            'data.attributes.repeated_password' => 'required_with:data.attributes.password|same:data.attributes.password',
            'data.relationships.roles.data.*.type' => 'sometimes|string|in:roles',
            'data.relationships.roles.data.*.id' => 'sometimes|int|exists:roles,id',
        ];
    }
}
