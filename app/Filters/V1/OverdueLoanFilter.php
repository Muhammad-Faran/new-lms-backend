<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class OverdueLoanFilter extends ApiFilter
{
    protected $relation_table = 'borrowers';
    protected $current_table = 'transactions';
    protected $current_table_foreign_key = 'borrower_id';
    protected $dateFilterColumn = 'transactions.created_at';

    protected $sortFields = [
        'transactions.id',
        'transactions.created_at'
    ];

    protected $relationSearchFields = [
        'borrower.first_name',
        'borrower.last_name',
        'borrower.mobile_no',
        'borrower.shipper_name',
        'borrower.cnic',
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
        ['name' => 'Borrower ID', 'selector' => 'transactions.borrower_id'],
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
