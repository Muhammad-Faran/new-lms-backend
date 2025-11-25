<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class RepaymentFilter extends ApiFilter
{

    protected $relation_table = 'borrowers';

    protected $current_table = 'repayments';

    protected $current_table_foreign_key = 'borrower_id';

     protected $dateFilterColumn = 'repayments.created_at';


    protected $relationSearchFields = [
        'borrower.first_name',
        'borrower.last_name',
    ];

    protected $relationSearchTableFields = [
        'transaction.order_number',
    ];

    protected $searchFields = [
        'repayments.id',
    ];

    protected $searchTableFields = [
        'repayments.id',
    ];

    protected $sortFields = [
        'repayments.id',        // Add table prefix
        'repayments.created_at' // Add table prefix
    ];

    protected $relationSearchDropdownFields = [
        'transaction.product_id', 
    ];

    protected $columnHeaders = [
        ['name' => 'ID', 'selector' => 'repayments.id'],
        ['name' => 'Transaction ID', 'selector' => 'repayments.transaction_id'],
        ['name' => 'Installment ID', 'selector' => 'repayments.installment_id'],
        ['name' => 'Borrower ID', 'selector' => 'repayments.borrower_id'],
        ['name' => 'Amount', 'selector' => 'repayments.amount'],
        ['name' => 'Status', 'selector' => 'repayments.status'],
        ['name' => 'Paid At', 'selector' => 'repayments.paid_at'],
        ['name' => 'Created At', 'selector' => 'repayments.created_at'],
        ['name' => 'Updated At', 'selector' => 'repayments.updated_at'],
    ];
}
