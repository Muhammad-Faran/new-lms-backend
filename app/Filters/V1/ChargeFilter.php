<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ChargeFilter extends ApiFilter
{
    protected $sortFields = ['id', 'name'];
    
    protected $searchFields = ['id', 'name'];

    protected $searchTableFields = ['id', 'name'];

    protected $columnHeaders = [
        [
            'name' => 'ID',
            'selector' => 'id',
        ],
        [
            'name' => 'Name',
            'selector' => 'name',
        ]
    ];
}
