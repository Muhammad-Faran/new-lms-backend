<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class UsersFilter extends ApiFilter
{
    protected $sortFields = ['id', 'first_name', 'last_name', 'email', 'phone'];
    
    protected $searchFields = ['id', 'first_name', 'last_name', 'email', 'phone'];

    protected $searchTableFields = ['id', 'first_name', 'last_name', 'email', 'phone'];

     protected $searchDropdownFields = ['first_name', 'last_name', 'email'];

    protected $columnHeaders = [
        [
            'name' => 'ID',
            'selector' => 'id',
        ],
        [
            'name' => 'First Name',
            'selector' => 'first_name',
        ],
        [
            'name' => 'Last Name',
            'selector' => 'last_name',
        ],
        [
            'name' => 'Email',
            'selector' => 'email',
        ],
        [
            'name' => 'Phone',
            'selector' => 'phone',
        ],
    ];
}
