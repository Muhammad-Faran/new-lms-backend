<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    \App\Models\User::create([
        'first_name' => 'admin',
        'last_name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('admin'),
        'is_admin' => true, // if you have this column
    ]);
    
    \App\Models\User::create([
        'first_name' => 'admin1',
        'last_name' => 'admin',
        'email' => 'admin1@admin.com',
        'password' => Hash::make('admin'),
        'is_admin' => true, // if you have this column
    ]);
}

}
