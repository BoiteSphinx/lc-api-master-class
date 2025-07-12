<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ReplaceUserRequest extends BaseUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.attributes.name' => ['required', 'string'],
            'data.attributes.email' => ['required', 'email'],
            'data.attributes.isManager' => ['required', 'boolean'],
            'data.attributes.password' => ['required', 'string'],
        ];
    }
}
