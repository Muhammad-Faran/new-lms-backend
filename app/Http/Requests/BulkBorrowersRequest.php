<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkBorrowersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'borrowers' => 'required|array',
            'borrowers.*.first_name' => 'required|string|max:100',
            'borrowers.*.last_name' => 'required|string|max:100',
            'borrowers.*.cnic' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'cnic'),
            ],
            'borrowers.*.wallet_id' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'wallet_id'),
            ],
            'borrowers.*.shipper_id' => [
                'required',
                'string',
                'max:15',
            ],
            'borrowers.*.cnic_front_image' => 'nullable|string',
            'borrowers.*.cnic_back_image' => 'nullable|string',
            'borrowers.*.cnic_issuance_date' => 'nullable|date',
            'borrowers.*.mobile_no' => [
                'required',
                'string',
                'max:15',
                Rule::unique('borrowers', 'mobile_no'),
            ],
            'borrowers.*.email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('borrowers', 'email'),
            ],
            'borrowers.*.father_name' => 'nullable|string|max:100',
            'borrowers.*.mother_name' => 'nullable|string|max:100',
            'borrowers.*.address' => 'nullable|string',
            'borrowers.*.city' => 'nullable|string|max:50',
            'borrowers.*.dob' => 'nullable|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $borrowers = collect($this->borrowers);

            $fieldsToCheck = ['cnic', 'wallet_id', 'mobile_no', 'email'];
            $duplicateMessages = [];
            $fieldErrors = [];

            foreach ($fieldsToCheck as $field) {
                $duplicates = $borrowers->pluck($field)
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
                    $validator->errors()->add('borrowers', $error);
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        if ($this->has('borrowers')) {
            $borrowers = collect($this->borrowers)->map(function ($borrower) {
                return [
                    ...$borrower,
                    'cnic' => isset($borrower['cnic']) ? str_replace('-', '', $borrower['cnic']) : null,
                    'mobile_no' => isset($borrower['mobile_no']) ? preg_replace('/^0/', '92', $borrower['mobile_no']) : null,
                ];
            });

            $this->merge(['borrowers' => $borrowers->toArray()]);
        }
    }
}
