<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class BookFilter extends ApiFilter
{
    protected $sortFields = ['id', 'name', 'status'];
    
    protected $searchFields = ['id', 'name', 'status'];

    protected $searchTableFields = ['id', 'name', 'status'];

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
            'name' => 'Max Allowed Amount',
            'selector' => 'max_allowed_amount',
        ],
        [
            'name' => 'Max Allowed Amount',
            'selector' => 'min_allowed_amount',
        ],
        [
            'name' => 'Status',
            'selector' => 'status',
        ],
    ];
}
