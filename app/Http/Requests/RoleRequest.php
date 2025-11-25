<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this based on your authorization logic
    }

    public function rules()
    {
        $rules = [
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'slug'), // Ensure the slug is unique
            ],
            'permissions' => 'nullable|array', // Validate permissions as an array (nullable)
            'permissions.*' => 'exists:permissions,id', // Ensure each permission ID exists in the permissions table
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $role = $this->route()->parameter('role');

            // Modify slug validation to ignore the current role's slug during update
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ];
        }

        return $rules;
    }
}
