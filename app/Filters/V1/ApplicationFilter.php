<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ApplicationFilter extends ApiFilter
{
    protected $relation_table = 'applicants';
    protected $current_table = 'applications';
    protected $current_table_foreign_key = 'applicant_id';
    protected $dateFilterColumn = 'applications.created_at';

    protected $sortFields = [
        'applications.id',
        'applications.created_at'
    ];

    protected $relationSearchFields = [
        'applicant.first_name',
        'applicant.last_name',
        'applicant.mobile_no',
        'applicant.shipper_name',
        'applicant.cnic',
    ];

    protected $searchFields = [
        'applications.id',
    ];

    protected $searchTableFields = [
        'applications.id',
    ];

    protected $searchDropdownFields = [
        'applications.status',  // ✅ Status will be a dropdown
        'applications.product_id',  // ✅ Product filter based on product_id
    ];

    protected $relationDateFilters = [
    'due_date' => 'installments.due_date' // ✅ Define due_date for special date-based filtering
];


    protected $columnHeaders = [
        ['name' => 'ID', 'selector' => 'applications.id'],
        ['name' => 'Applicant ID', 'selector' => 'applications.applicant_id'],
        ['name' => 'Product ID', 'selector' => 'applications.product_id'],
        ['name' => 'Plan ID', 'selector' => 'applications.plan_id'],
        ['name' => 'Loan Amount', 'selector' => 'applications.loan_amount'],
        ['name' => 'Total Charges', 'selector' => 'applications.total_charges'],
        ['name' => 'Disbursed Amount', 'selector' => 'applications.disbursed_amount'],
        ['name' => 'Outstanding Amount', 'selector' => 'applications.outstanding_amount'],
        ['name' => 'Status', 'selector' => 'applications.status'],
        ['name' => 'Created At', 'selector' => 'applications.created_at'],
        ['name' => 'Updated At', 'selector' => 'applications.updated_at'],
    ];
}
