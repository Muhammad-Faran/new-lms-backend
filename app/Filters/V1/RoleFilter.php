<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class RoleFilter extends ApiFilter
{
    protected $sortFields = ['id', 'slug'];
    
    protected $searchFields = ['id', 'slug'];

    protected $searchTableFields = ['id', 'slug'];

    protected $columnHeaders = [
        [
            'name' => 'ID',
            'selector' => 'id',
        ],
        [
            'name' => 'Name',
            'selector' => 'slug',
        ]
    ];
}
