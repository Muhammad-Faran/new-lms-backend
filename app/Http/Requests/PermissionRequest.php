<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You can adjust this if needed based on authorization logic
    }

    public function rules()
    {
        $rules = [
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug'), // Ensure slug is unique in the permissions table
            ],
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $permission = $this->route()->parameter('permission');

            // Modify slug validation to ignore the current permission's slug during update
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug')->ignore($permission->id),
            ];
        }

        return $rules;
    }
}
