<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicantRequest extends FormRequest
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
                Rule::unique('applicants', 'cnic'),
            ],
            'cnic_front_image' => 'nullable|string',
            'cnic_back_image' => 'nullable|string',
            'cnic_issuance_date' => 'nullable|date',
            'mobile_no' => [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'mobile_no'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('applicants', 'email'),
            ],
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $applicant = $this->route()->parameter('applicant'); // Get the applicant from the route
            // Update unique rules to ignore the current applicant
            $rules['cnic'] = [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'cnic')->ignore($applicant->id),
            ];

            $rules['mobile_no'] = [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'mobile_no')->ignore($applicant->id),
            ];

            $rules['email'] = [
                'nullable',
                'email',
                'max:100',
                Rule::unique('applicants', 'email')->ignore($applicant->id),
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
