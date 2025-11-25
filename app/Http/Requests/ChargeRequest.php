<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChargeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // This can be adjusted based on any authorization logic
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255', // Book name is required and should be a string with max length of 255
        ];

        return $rules;
    }
}
