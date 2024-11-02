<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'data.attributes' => 'required',
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'required|email|unique:users,email',
            'data.attributes.password' => 'required|string',
            'data.attributes.repeated_password' => 'required|same:data.attributes.password',
        ];
    }
}
