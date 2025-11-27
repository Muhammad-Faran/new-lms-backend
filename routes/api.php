<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ApplicantController;
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
        Route::post('/addApplicant', [ApplicantController::class, 'addApplicant'])->can('add-Applicant')->middleware('api.log');
        Route::post('/addApplicantsBulk', [ApplicantController::class, 'addApplicantsBulk'])->can('add-Applicant')->middleware('api.log');
        Route::get('/applicant', [ApplicantController::class, 'ApplicantByWalletId'])->can('view-applicants');
        Route::get('/applicants', [ApplicantController::class, 'index'])->can('view-applicants');
        Route::get('/applicants/{Applicant}', [ApplicantController::class, 'show'])->can('view-applicants');
        Route::put('/applicants/{Applicant}', [ApplicantController::class, 'update'])->can('update-applicants');
        Route::post('/applicants/{Applicant}/products/sync', [ApplicantController::class, 'syncApplicantProducts'])->can('sync-Applicant-products');
        Route::post('/applicants/{Applicant}/assignCreditLimit', [ApplicantController::class, 'assignCreditLimit'])->can('assign-credit-limit');
        Route::post('/applicants/{Applicant}/assignFinancingPolicy', [ApplicantController::class, 'assignFinancingPolicy'])->can('assign-financing-policy');
        Route::get('/applicant/shipper-names', [ApplicantController::class, 'getUniqueShipperNames']);
        Route::post('/refreshOfacNacta', [ApplicantController::class, 'refreshOfacNacta'])->can('refresh-ofac-nacta');
        Route::post('/refreshCreditEngineShipperCreditScore', [ApplicantController::class, 'refreshCreditEngineShipperCreditScore'])->can('refresh-credit-score');
        Route::post('/refreshCreditEngineShipperInfo', [ApplicantController::class, 'refreshCreditEngineShipperInfo'])->can('refresh-shipper-info');
        Route::post('/refreshCreditEngineShipperKyc', [ApplicantController::class, 'refreshCreditEngineShipperKyc'])->can('refresh-shipper-kyc');
        Route::post('/refreshCreditEngineShipperPricing', [ApplicantController::class, 'refreshCreditEngineShipperPricing'])->can('refresh-shipper-pricing');

        Route::patch('/applicants/updateStatus', [ApplicantController::class, 'updateStatus'])->can('update-applicant-status');
        Route::get('/credit-limit', [CreditLimitController::class, 'show'])->can('get-credit-limit');

        Route::get('/transactions', [TransactionController::class, 'index'])->can('view-transactions');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->can('view-transactions');
        Route::get('/transaction/export', [TransactionController::class, 'export'])->can('export-transactions');
        Route::post('/transactions/initiate', [TransactionController::class, 'initiateTransaction'])->can('initiate-transaction')->middleware('api.log');
        Route::post('/transactions/calculate', [TransactionController::class, 'calculateTransaction'])->can('initiate-transaction')->middleware('api.log');

        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->can('view-dashboard');



        Route::get('/loan-details', [ApplicantController::class, 'getLoanDetails'])->can('loan-details');
        Route::get('/loan-details-listing', [ApplicantController::class, 'getLoanDetailsListing'])->can('loan-details');
        Route::post('/repayment/pay-installment', [RepaymentController::class, 'payInstallment'])->can('pay-installment')->middleware('api.log');
        Route::get('/repayments', [RepaymentController::class, 'index'])->can('view-repayments');
        Route::get('/repayments/{repayment}', [RepaymentController::class, 'show'])->can('view-repayments');
        Route::get('/repayment/export', [RepaymentController::class, 'export'])->can('export-repayments');


        Route::get('/reports/overdue-loans', [ReportController::class, 'getOverdueLoans'])->can('view-reports');
        Route::get('/reports/export-overdue-loans', [ReportController::class, 'exportOverdueLoans'])->can('view-reports');


		Route::post('/syncUserPermissions', [UserController::class, 'syncUserPermissions'])->can('user-permissions');
		Route::post('/syncRolePermissions', [RoleController::class, 'syncRolePermissions'])->can('role-permissions'); 


	});