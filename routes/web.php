<?php

use Illuminate\Support\Facades\Route;
use App\Models\Permission;
use App\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('Mercado_financing_tncs', function () {
    return view('TCs.Mercado_financing_tncs');
});


Route::get('/sync', function () {
    foreach (config('permission.permission_slugs') as $slug) {
        if (!empty($slug)) {
            Permission::updateOrCreate(['slug' => $slug]);
        }
    }

    // foreach (config('permission.role_slugs') as $slug => $permissions) {
    //     $role = Role::updateOrCreate(['slug' => $slug]);
    //     $role->permissions()->detach();
    //     if ($permissions) {
    //         foreach ($permissions as $slug) {
    //             if (!empty($slug)) {
    //                 $permission = Permission::where('slug', $slug)->first();
    //                 if ($permission) {
    //                     $role->permissions()->attach($permission);
    //                 }
    //             }
    //         }
    //     }
    // }
});


