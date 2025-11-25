<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\BorrowerController;
use App\Http\Controllers\Api\V1\CreditLimitController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\RepaymentController;
use App\Http\Controllers\Api\V1\DashboardController;



Route::options('{any}', function (Request $request) {
    return response()->json(['status' => 'ok'], 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS, PUT, DELETE',
        'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Credentials' => 'true',
    ]);
})->where('any', '.*');



// Define login routes
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('force_json');
Route::post('/login-request', [AuthController::class, 'loginRequest']); // Possibly for sending magic tokens
Route::post('/login/{token}', [AuthController::class, 'loginByToken']); // Token login


// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => ['auth:sanctum', 'force_json']], function () {
        Route::post('/password/change', [AuthController::class, 'changePassword']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('books', BookController::class);
        Route::apiResource('charges', ChargeController::class);
		Route::post('/addProduct', [ProductController::class, 'addProduct'])->can('add-product');
		Route::get('/products', [ProductController::class, 'index'])->can('view-products');
        Route::patch('/products/{id}/updateStatus', [ProductController::class, 'updateStatus'])->can('update-product-status');
        Route::post('/addBorrower', [BorrowerController::class, 'addBorrower'])->can('add-borrower')->middleware('api.log');
        Route::post('/addBorrowersBulk', [BorrowerController::class, 'addBorrowersBulk'])->can('add-borrower')->middleware('api.log');
        Route::get('/borrower', [BorrowerController::class, 'borrowerByWalletId'])->can('view-borrowers');
        Route::get('/borrowers', [BorrowerController::class, 'index'])->can('view-borrowers');
        Route::get('/borrowers/{borrower}', [BorrowerController::class, 'show'])->can('view-borrowers');
        Route::put('/borrowers/{borrower}', [BorrowerController::class, 'update'])->can('update-borrowers');
        Route::post('/borrowers/{borrower}/products/sync', [BorrowerController::class, 'syncBorrowerProducts'])->can('sync-borrower-products');
        Route::post('/borrowers/{borrower}/assignCreditLimit', [BorrowerController::class, 'assignCreditLimit'])->can('assign-credit-limit');
        Route::post('/borrowers/{borrower}/assignFinancingPolicy', [BorrowerController::class, 'assignFinancingPolicy'])->can('assign-financing-policy');
        Route::get('/borrower/shipper-names', [BorrowerController::class, 'getUniqueShipperNames']);
        Route::post('/refreshOfacNacta', [BorrowerController::class, 'refreshOfacNacta'])->can('refresh-ofac-nacta');
        Route::post('/refreshCreditEngineShipperCreditScore', [BorrowerController::class, 'refreshCreditEngineShipperCreditScore'])->can('refresh-credit-score');
        Route::post('/refreshCreditEngineShipperInfo', [BorrowerController::class, 'refreshCreditEngineShipperInfo'])->can('refresh-shipper-info');
        Route::post('/refreshCreditEngineShipperKyc', [BorrowerController::class, 'refreshCreditEngineShipperKyc'])->can('refresh-shipper-kyc');
        Route::post('/refreshCreditEngineShipperPricing', [BorrowerController::class, 'refreshCreditEngineShipperPricing'])->can('refresh-shipper-pricing');

        Route::patch('/borrowers/updateStatus', [BorrowerController::class, 'updateStatus'])->can('update-borrower-status');
        Route::get('/credit-limit', [CreditLimitController::class, 'show'])->can('get-credit-limit');

        Route::get('/transactions', [TransactionController::class, 'index'])->can('view-transactions');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->can('view-transactions');
        Route::get('/transaction/export', [TransactionController::class, 'export'])->can('export-transactions');
        Route::post('/transactions/initiate', [TransactionController::class, 'initiateTransaction'])->can('initiate-transaction')->middleware('api.log');
        Route::post('/transactions/calculate', [TransactionController::class, 'calculateTransaction'])->can('initiate-transaction')->middleware('api.log');

        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->can('view-dashboard');



        Route::get('/loan-details', [BorrowerController::class, 'getLoanDetails'])->can('loan-details');
        Route::get('/loan-details-listing', [BorrowerController::class, 'getLoanDetailsListing'])->can('loan-details');
        Route::post('/repayment/pay-installment', [RepaymentController::class, 'payInstallment'])->can('pay-installment')->middleware('api.log');
        Route::get('/repayments', [RepaymentController::class, 'index'])->can('view-repayments');
        Route::get('/repayments/{repayment}', [RepaymentController::class, 'show'])->can('view-repayments');
        Route::get('/repayment/export', [RepaymentController::class, 'export'])->can('export-repayments');


        Route::get('/reports/overdue-loans', [ReportController::class, 'getOverdueLoans'])->can('view-reports');
        Route::get('/reports/export-overdue-loans', [ReportController::class, 'exportOverdueLoans'])->can('view-reports');


		Route::post('/syncUserPermissions', [UserController::class, 'syncUserPermissions'])->can('user-permissions');
		Route::post('/syncRolePermissions', [RoleController::class, 'syncRolePermissions'])->can('role-permissions'); 


	});