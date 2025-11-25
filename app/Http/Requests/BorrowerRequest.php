<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BorrowerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cnic' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'cnic'),
            ],
            'wallet_id' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'wallet_id'),
            ],
            'shipper_id' => [
                'required',
                'string',
                'max:15',
            ],
            'shipper_name' => [
                'required',
                'string',
            ],
            'cnic_front_image' => 'nullable|string',
            'cnic_back_image' => 'nullable|string',
            'cnic_issuance_date' => 'nullable|date',
            'mobile_no' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'mobile_no'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('borrowers', 'email'),
            ],
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $borrower = $this->route()->parameter('borrower'); // Get the borrower from the route
            // Update unique rules to ignore the current borrower
            $rules['cnic'] = [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'cnic')->ignore($borrower->id),
            ];

            $rules['wallet_id'] = [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'wallet_id')->ignore($borrower->id),
            ];

            $rules['mobile_no'] = [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'mobile_no')->ignore($borrower->id),
            ];

            $rules['email'] = [
                'nullable',
                'email',
                'max:100',
                Rule::unique('borrowers', 'email')->ignore($borrower->id),
            ];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // Remove dashes from CNIC
            'cnic' => $this->cnic ? str_replace('-', '', $this->cnic) : $this->cnic,
            // Replace leading '0' with '92' in mobile number
            'mobile_no' => $this->mobile_no ? preg_replace('/^0/', '92', $this->mobile_no) : $this->mobile_no,
        ]);
    }
}
