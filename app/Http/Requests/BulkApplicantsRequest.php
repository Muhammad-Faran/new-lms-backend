<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkApplicantsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'applicants' => 'required|array',
            'applicants.*.first_name' => 'required|string|max:100',
            'applicants.*.last_name' => 'required|string|max:100',
            'applicants.*.cnic' => [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'cnic'),
            ],
            'applicants.*.wallet_id' => [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'wallet_id'),
            ],
            'applicants.*.shipper_id' => [
                'required',
                'string',
                'max:15',
            ],
            'applicants.*.cnic_front_image' => 'nullable|string',
            'applicants.*.cnic_back_image' => 'nullable|string',
            'applicants.*.cnic_issuance_date' => 'nullable|date',
            'applicants.*.mobile_no' => [
                'required',
                'string',
                'max:15',
                Rule::unique('applicants', 'mobile_no'),
            ],
            'applicants.*.email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('applicants', 'email'),
            ],
            'applicants.*.father_name' => 'nullable|string|max:100',
            'applicants.*.mother_name' => 'nullable|string|max:100',
            'applicants.*.address' => 'nullable|string',
            'applicants.*.city' => 'nullable|string|max:50',
            'applicants.*.dob' => 'nullable|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $applicants = collect($this->applicants);

            $fieldsToCheck = ['cnic', 'wallet_id', 'mobile_no', 'email'];
            $duplicateMessages = [];
            $fieldErrors = [];

            foreach ($fieldsToCheck as $field) {
                $duplicates = $applicants->pluck($field)
                    ->filter()
                    ->duplicates();

                if ($duplicates->isNotEmpty()) {
                    $duplicateMessages[] = "Duplicate {$field} values found.";
                    $fieldErrors[] = "Duplicate {$field} values found in the request: " . $duplicates->join(', ');
                }
            }

            if (!empty($duplicateMessages)) {
                $validator->errors()->add('message', 'Duplicate values found.');
                foreach ($fieldErrors as $error) {
                    $validator->errors()->add('applicants', $error);
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        if ($this->has('applicants')) {
            $applicants = collect($this->applicants)->map(function ($applicant) {
                return [
                    ...$applicant,
                    'cnic' => isset($applicant['cnic']) ? str_replace('-', '', $applicant['cnic']) : null,
                    'mobile_no' => isset($applicant['mobile_no']) ? preg_replace('/^0/', '92', $applicant['mobile_no']) : null,
                ];
            });

            $this->merge(['applicants' => $applicants->toArray()]);
        }
    }
}
