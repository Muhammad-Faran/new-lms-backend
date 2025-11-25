<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize()
    {
        return true; // This can be adjusted based on any authorization logic
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255', // Book name is required and should be a string with max length of 255
            'status' => 'nullable|boolean', // Status can be either true or false
            'max_allowed_amount' => 'required|integer|min:1', // Maximum allowed amount must be a positive integer
            'min_allowed_amount' => 'required|integer|min:1|lte:max_allowed_amount', // Minimum allowed amount must be a positive integer and less than or equal to max_allowed_amount
        ];

        return $rules;
    }
}
