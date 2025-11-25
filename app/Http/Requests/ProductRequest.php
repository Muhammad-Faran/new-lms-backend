<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assuming no special authorization logic needed
    }

    public function rules()
    {
        return [
            'code' => 'required|string|unique:products,code|max:255',
            'tnc' => 'nullable|string',
            'max_requested_amount' => 'required|integer',
            'min_requested_amount' => 'required|integer',
            'description' => 'required|string',
            'disable_loan_on_avail' => 'nullable|string',
            'default_status' => 'required|in:review,approve,reject',
            'product_books' => 'required|array',
            'product_plans' => 'required|array',
            'product_tiers' => 'required|array',
            'selected_charges' => 'required|array',
            'name' => 'required|string|max:255',
            'limit' => 'required',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Product code is required.',
            'code.unique' => 'Product code must be unique.',
            // Add more custom messages as needed
        ];
    }
}
