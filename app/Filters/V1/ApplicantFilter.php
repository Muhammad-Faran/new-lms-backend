<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ApplicantFilter extends ApiFilter
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
        'applicants.id',        // Add table prefix
        'applicants.created_at' // Add table prefix
    ];

    protected $columnHeaders = [
        ['name' => 'ID', 'selector' => 'applicants.id'],
        ['name' => 'First Name', 'selector' => 'applicants.first_name'],
        ['name' => 'Last Name', 'selector' => 'applicants.last_name'],
        ['name' => 'Wallet ID', 'selector' => 'applicants.wallet_id'],
        ['name' => 'Shipper ID', 'selector' => 'applicants.shipper_id'],
        ['name' => 'Shipper Name', 'selector' => 'applicants.shipper_name'],
        ['name' => 'CNIC', 'selector' => 'applicants.cnic'],
        ['name' => 'Mobile No', 'selector' => 'applicants.mobile_no'],
        ['name' => 'Email', 'selector' => 'applicants.email'],
        ['name' => 'Father Name', 'selector' => 'applicants.father_name'],
        ['name' => 'Mother Name', 'selector' => 'applicants.mother_name'],
        ['name' => 'Address', 'selector' => 'applicants.address'],
        ['name' => 'City', 'selector' => 'applicants.city'],
        ['name' => 'Date of Birth', 'selector' => 'applicants.dob'],
        ['name' => 'Status', 'selector' => 'applicants.status'],
        ['name' => 'CNIC Issuance Date', 'selector' => 'applicants.cnic_issuance_date'],
        ['name' => 'CNIC Front Image', 'selector' => 'applicants.cnic_front_image'],
        ['name' => 'CNIC Back Image', 'selector' => 'applicants.cnic_back_image'],
        ['name' => 'Created At', 'selector' => 'applicants.created_at'],
        ['name' => 'Updated At', 'selector' => 'applicants.updated_at'],
    ];
}
