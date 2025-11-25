<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ProductsFilter extends ApiFilter
{
    protected $sortFields = ['id', 'name', 'code', 'status'];
    
    protected $searchFields = ['id', 'name', 'code', 'status'];

    protected $searchTableFields = ['id', 'name', 'code', 'status'];

     protected $searchDropdownFields = ['name', 'code', 'status'];

    protected $columnHeaders = [
        [
            'name' => 'ID',
            'selector' => 'id',
        ],
        [
            'name' => 'Name',
            'selector' => 'name',
        ],
        [
            'name' => 'Code',
            'selector' => 'code',
        ],
        [
            'name' => 'Description',
            'selector' => 'description',
        ],
        [
            'name' => 'Tnc Url',
            'selector' => 'tnc',
        ],
        [
            'name' => 'Default Status',
            'selector' => 'default_status',
        ],
        [
            'name' => 'Max Requested Amount',
            'selector' => 'max_requested_amount',
        ],
        [
            'name' => 'Min Requested Amount',
            'selector' => 'min_requested_amount',
        ],
        [
            'name' => 'Eligibility Criteria',
            'selector' => 'eligibility_criteria',
        ],
        [
            'name' => 'Disable Loan On Avail',
            'selector' => 'disable_loan_on_avail',
        ],
        [
            'name' => 'Status',
            'selector' => 'status',
        ]
    ];
}
