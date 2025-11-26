<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class TransactionFilter extends ApiFilter
{
    protected $relation_table = 'applicants';
    protected $current_table = 'transactions';
    protected $current_table_foreign_key = 'applicant_id';
    protected $dateFilterColumn = 'transactions.created_at';

    protected $sortFields = [
        'transactions.id',
        'transactions.created_at'
    ];

    protected $relationSearchFields = [
        'applicant.first_name',
        'applicant.last_name',
        'applicant.mobile_no',
        'applicant.shipper_name',
        'applicant.cnic',
    ];

    protected $searchFields = [
        'transactions.id',
        'transactions.order_number',
    ];

    protected $searchTableFields = [
        'transactions.id',
        'transactions.order_number',
    ];

    protected $searchDropdownFields = [
        'transactions.status',  // ✅ Status will be a dropdown
        'transactions.product_id',  // ✅ Product filter based on product_id
    ];

    protected $relationDateFilters = [
    'due_date' => 'installments.due_date' // ✅ Define due_date for special date-based filtering
];


    protected $columnHeaders = [
        ['name' => 'ID', 'selector' => 'transactions.id'],
        ['name' => 'Applicant ID', 'selector' => 'transactions.applicant_id'],
        ['name' => 'Product ID', 'selector' => 'transactions.product_id'],
        ['name' => 'Plan ID', 'selector' => 'transactions.plan_id'],
        ['name' => 'Loan Amount', 'selector' => 'transactions.loan_amount'],
        ['name' => 'Total Charges', 'selector' => 'transactions.total_charges'],
        ['name' => 'Order Amount', 'selector' => 'transactions.order_amount'],
        ['name' => 'Order Number', 'selector' => 'transactions.order_number'],
        ['name' => 'Disbursed Amount', 'selector' => 'transactions.disbursed_amount'],
        ['name' => 'Outstanding Amount', 'selector' => 'transactions.outstanding_amount'],
        ['name' => 'Status', 'selector' => 'transactions.status'],
        ['name' => 'Created At', 'selector' => 'transactions.created_at'],
        ['name' => 'Updated At', 'selector' => 'transactions.updated_at'],
    ];
}
