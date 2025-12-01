<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Repayment;
use App\Models\Application;
use App\Models\ApplicationInstallment;
use App\Models\Applicant;
use App\Models\ApplicationLog;
use App\Filters\V1\RepaymentFilter;
use App\Http\Resources\V1\RepaymentCollection;
use App\Http\Resources\V1\RepaymentResource;
use Illuminate\Http\Request;
use App\Services\ExportService;


class RepaymentController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

        public function index(Request $request)
    {
        $filter = new RepaymentFilter();

        $query = Repayment::with(['application', 'applicant','installment']);

        $repayments = $filter->filter($query, $request);

        return new RepaymentCollection($repayments);
    }

    /**
     * Get a single repayment.
     */
    public function show(Repayment $repayment)
    {
        return new RepaymentResource($repayment);
    }
    
    /**
     * API to pay an installment.
     */
   public function payInstallment(Request $request)
{
    $request->validate([
        'installment_id' => 'required|exists:application_installments,id',
        'application_id' => 'required|exists:applications,id',
        'wallet_id' => 'required|exists:applicants,wallet_id',
        'amount' => 'required|numeric|min:1',
    ]);

    $applicant = Applicant::where('wallet_id', $request->wallet_id)->firstOrFail();
    $application = Application::findOrFail($request->application_id);

    if ($application->applicant_id !== $applicant->id) {
        return response()->json([
            'message' => 'Unauthorized: The provided wallet ID does not own this application.',
        ], 403);
    }

    $installment = $application->installments()->where('id', $request->installment_id)->first();

    if (!$installment) {
        return response()->json([
            'message' => 'The specified installment does not belong to the provided application.',
        ], 422);
    }

    if ($installment->status === 'paid') {
        return response()->json([
            'message' => 'Installment has already been paid.',
        ], 422);
    }

    if ($request->amount != $installment->outstanding) {
        return response()->json([
            'message' => 'The amount provided does not match the outstanding amount for this installment.',
            'outstanding' => $installment->outstanding,
        ], 422);
    }

    $repayment = Repayment::create([
        'application_id' => $application->id,
        'installment_id' => $installment->id,
        'applicant_id' => $applicant->id,
        'amount' => $request->amount,
        'paid_at' => now(),
        'status' => 'paid',
    ]);

    $outstandingAmount = $installment->outstanding;

    $installment->update([
        'status' => 'paid',
        'outstanding' => 0,
    ]);

    $creditLimit = $applicant->creditLimit;
    if ($creditLimit) {
        $creditLimit->update([
            'available_limit' => $creditLimit->available_limit + $outstandingAmount,
        ]);
    }

    $application->update([
        'outstanding_amount' => $application->outstanding_amount - $outstandingAmount,
    ]);

    if ($application->outstanding_amount <= 0) {
        $application->update(['status' => 'completed']);
    }

    ApplicationLog::create([
        'application_id' => $application->id,
        'application_installment_id' => $installment->id,
        'amount' => $request->amount,
        'type' => 'collection',
    ]);

    return response()->json([
        'message' => 'Installment paid successfully.',
        'repayment' => $repayment,
    ]);
}

 public function export(Request $request)
{
    $filter = new RepaymentFilter();

    $query = Repayment::with(['application', 'applicant','installment']);

    // Apply filters
    $repayments = $filter->filter($query, $request);

    if ($repayments->isEmpty()) {
        return response()->json(['message' => 'No repayments found for the given filters.'], 404);
    }

    // Prepare data for export
    $data = $repayments->map(function ($repayment) {
        return [
            'Id' => $repayment->id,
            'Amount Paid' => $repayment->amount,
            'Application Id' => optional($repayment->application)->id,
            'applicant' => optional($repayment->applicant)->first_name . ' ' . optional($repayment->applicant)->last_name,
            'Shipper' => optional($repayment->applicant)->shipper_name,
            'Product' => optional($repayment->application->product)->name,
            'Order Number' => $repayment->application->order_number,
            'Order Amount' => $repayment->application->order_amount,
            'Financing Amount' => $repayment->application->loan_amount,
            'Total Charges' => $repayment->application->total_charges,
            'Disbursed Amount' => $repayment->application->disbursed_amount,
            'Disbursement Date' => optional($repayment->created_at)->format('Y-m-d'),
            'Paid Date' => $repayment->paid_at,
            'Status' => $repayment->status,
        ];
    })->toArray();

    $headers = [
        'Id','Amount Paid', 'Application Id', 'applicant', 'Shipper', 'Product', 'Order Number',
        'Order Amount', 'Financing Amount', 'Total Charges', 'Disbursed Amount',
        'Paid Date', 'Disbursement Date', 'Status',
    ];

    // Use ExportService to create the Excel file
    return $this->exportService->exportToExcel('repayments.xlsx', $data, $headers);
}



}
