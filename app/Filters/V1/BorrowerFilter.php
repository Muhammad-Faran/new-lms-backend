<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class BorrowerFilter extends ApiFilter
{
    protected $searchFields = [
        'first_name',
        'last_name',
        'shipper_name',
        'wallet_id',
        'cnic',
        'mobile_no',
        'email'
    ];

    protected $searchTableFields = [
        'first_name',
        'last_name',
        'wallet_id',
        'cnic',
        'mobile_no',
        'email'
    ];

     protected $sortFields = [
        'borrowers.id',        // Add table prefix
        'borrowers.created_at' // Add table prefix
    ];

    protected $columnHeaders = [
        ['name' => 'ID', 'selector' => 'borrowers.id'],
        ['name' => 'First Name', 'selector' => 'borrowers.first_name'],
        ['name' => 'Last Name', 'selector' => 'borrowers.last_name'],
        ['name' => 'Wallet ID', 'selector' => 'borrowers.wallet_id'],
        ['name' => 'Shipper ID', 'selector' => 'borrowers.shipper_id'],
        ['name' => 'Shipper Name', 'selector' => 'borrowers.shipper_name'],
        ['name' => 'CNIC', 'selector' => 'borrowers.cnic'],
        ['name' => 'Mobile No', 'selector' => 'borrowers.mobile_no'],
        ['name' => 'Email', 'selector' => 'borrowers.email'],
        ['name' => 'Father Name', 'selector' => 'borrowers.father_name'],
        ['name' => 'Mother Name', 'selector' => 'borrowers.mother_name'],
        ['name' => 'Address', 'selector' => 'borrowers.address'],
        ['name' => 'City', 'selector' => 'borrowers.city'],
        ['name' => 'Date of Birth', 'selector' => 'borrowers.dob'],
        ['name' => 'Status', 'selector' => 'borrowers.status'],
        ['name' => 'CNIC Issuance Date', 'selector' => 'borrowers.cnic_issuance_date'],
        ['name' => 'CNIC Front Image', 'selector' => 'borrowers.cnic_front_image'],
        ['name' => 'CNIC Back Image', 'selector' => 'borrowers.cnic_back_image'],
        ['name' => 'Created At', 'selector' => 'borrowers.created_at'],
        ['name' => 'Updated At', 'selector' => 'borrowers.updated_at'],
    ];
}
