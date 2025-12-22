<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Application;
use App\Models\Product;
use App\Models\ApplicantProductRule;
use App\Models\ApplicationLog;
use App\Models\ProductPlan;
use App\Http\Resources\V1\ApplicationResource;
use App\Http\Resources\V1\ApplicationCollection;
use App\Filters\V1\ApplicationFilter;
use App\Models\Applicant;
use App\Services\ExportService;

class ApplicationController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

 public function index(Request $request)
    {
        $filter = new ApplicationFilter();

        $query = Application::with(['charges', 'installments', 'applicant', 'product']);

        $applications = $filter->filter($query, $request);

        return new ApplicationCollection($applications);
    }

    public function show(Application $application)
    {
        return new ApplicationResource($application);
    }

    public function export(Request $request)
{
    $filter = new ApplicationFilter();

    $query = Application::with(['charges', 'installments', 'applicant', 'product']);

    // Apply filters
    $applications = $filter->filter($query, $request);

    if ($applications->isEmpty()) {
        return response()->json(['message' => 'No applications found for the given filters.'], 404);
    }

    // Prepare data for export
    $data = $applications->map(function ($application) {
        return [
            'Id' => $application->id,
            'Applicant' => optional($application->applicant)->first_name . ' ' . optional($application->applicant)->last_name,
            'Product' => optional($application->product)->name,
            'Financing Amount' => $application->loan_amount,
            'Total Charges' => $application->total_charges,
            'Disbursed Amount' => $application->disbursed_amount,
            'Due Date' => $application->next_due_date,
            'Disbursement Date' => optional($application->created_at)->format('Y-m-d'),
            'Status' => $application->status,
        ];
    })->toArray();

    $headers = [
        'Id', 'Applicant', 'Product', 'Financing Amount', 'Total Charges', 'Disbursed Amount',
        'Due Date', 'Disbursement Date', 'Status',
    ];

    // Use ExportService to create the Excel file
    return $this->exportService->exportToExcel('applications.xlsx', $data, $headers);
}


public function initiateApplication(Request $request)
{
    $request->validate([
        'loan_amount' => 'required|numeric|min:1',
        'plan_id' => 'required|exists:product_plans,id',
        'product_id' => 'required|exists:products,id',
        'applicant_id' => 'required|exists:applicants,id',
    ]);

    DB::beginTransaction();

    try {
        $applicant = Applicant::with(['creditLimit', 'financingPolicy', 'products', 'applicantThreshold'])->findOrFail($request->applicant_id);

        if (!$applicant->is_active) {
            return response()->json(['message' => 'Application failed: Applicant is disabled.'], 403);
        }

        if (in_array($applicant->id, [114, 809, 821, 822, 823]) && $request->loan_amount > 3100) {
            return response()->json([
                'message' => 'Application failed: Order value threshold breached for this Applicant.'
            ], 422);
        }


        if (!$applicant->products->contains('id', $request->product_id)) {
            return response()->json(['message' => 'Application failed: Applicant cannot avail this product at this time.'], 403);
        }

        $financingPercentage = $applicant->financingPolicy->financing_percentage ?? 100;
        $adjustedLoanAmount = ceil($request->loan_amount * ($financingPercentage / 100));
        $availableLimit = $applicant->creditLimit->available_limit ?? 0;

        if ($adjustedLoanAmount > $availableLimit) {
            return response()->json(['message' => 'Application failed: Adjusted loan amount exceeds available credit limit.'], 422);
        }

        $product = Product::with('productPlans')->findOrFail($request->product_id);
        if (!$product->is_active) {
            return response()->json(['message' => 'Application failed: Product is disabled.'], 403);
        }


        if (!$product->productPlans->contains('id', $request->plan_id)) {
            return response()->json(['message' => 'Application failed: The specified plan does not belong to the selected product.'], 422);
        }

         // Check adjusted loan amount against product's min and max requested amounts
        $minRequestedAmount = $product->min_requested_amount ?? 0;
        $maxRequestedAmount = $product->max_requested_amount ?? 1000000;
        if ($adjustedLoanAmount < $minRequestedAmount || $adjustedLoanAmount > $maxRequestedAmount) {
            return response()->json([
                'message' => 'Application failed: Requested Financing amount is out of the allowed range.',
                'adjusted_financing_amount' => $adjustedLoanAmount,
                'min_requested_amount' => $minRequestedAmount,
                'max_requested_amount' => $maxRequestedAmount,
            ], 422);
        }


        $plan = ProductPlan::with('productTier.productTierCharges.productCharge.charge')->findOrFail($request->plan_id);
        $tier = $plan->productTier;

        $totalCharges = 0;
        $totalFedCharges = 0;
        $chargesLogData = [];
        $chargesData = [];
        $fedLogData = [];

if (!empty($applicant->applicantThreshold) &&
    !empty($applicant->applicantThreshold->order_threshold) &&
    !empty($applicant->applicantThreshold->fixed_threshold_charges)) {

    // Applicant threshold is set, apply its charge if applicable
    if ($request->loan_amount <= $applicant->applicantThreshold->order_threshold) {
        $chargeAmount = $applicant->applicantThreshold->fixed_threshold_charges;

        $chargesData[] = [
            'product_tier_id' => $tier->id, // Not related to a specific tier
            'product_charge_id' => null, // No specific charge since it's Applicant-level
            'charge_amount' => $chargeAmount,
            'apply_fed' => false,
            'fed_amount' => 0,
            'charge_condition' => 'Applicant Threshold Charge',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $chargesLogData[] = [
            'application_id' => null,
            'application_installment_id' => null,
            'amount' => $chargeAmount,
            'type' => 'charges',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $totalCharges += $chargeAmount;

    } 
    // Loan amount exceeds Applicant threshold → SKIP tier threshold, go to percentage calculation
} elseif (!empty($tier->order_threshold) && $request->loan_amount <= $tier->order_threshold) {
    // Applicant threshold is not set, check tier threshold
    $chargeAmount = $tier->fixed_threshold_charges;

    $chargesData[] = [
        'product_tier_id' => $tier->id,
        'product_charge_id' => null,
        'charge_amount' => $chargeAmount,
        'apply_fed' => false,
        'fed_amount' => 0,
        'charge_condition' => 'Tier Threshold Charge',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    $chargesLogData[] = [
        'application_id' => null,
        'application_installment_id' => null,
        'amount' => $chargeAmount,
        'type' => 'charges',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    $totalCharges += $chargeAmount;
}

// If neither Applicant nor tier threshold applies, OR if Applicant threshold exists but is exceeded → use percentage-based charges
 if ((empty($applicant->applicantThreshold) && (!empty($tier->order_threshold) && $request->loan_amount > $tier->order_threshold)) || (!empty($applicant->applicantThreshold) && $request->loan_amount > $applicant->applicantThreshold->order_threshold)) {
    $applicantRule = ApplicantProductRule::where('applicant_id', $applicant->id)
        ->where('product_id', $request->product_id)
        ->first();

    foreach ($tier->productTierCharges as $tierCharge) {
        // Determine the base amount for charges
        $baseAmount = $tierCharge->productCharge->charge_condition === 'Order Amount'
            ? $request->loan_amount 
            : $adjustedLoanAmount;  

        $chargeUnit = $applicantRule ? $applicantRule->charge_unit : $tierCharge->charges_unit;
        $chargeValue = $applicantRule ? $applicantRule->charge_value : $tierCharge->charges_value;

        $chargeAmount = $chargeUnit === 'percentage'
            ? $baseAmount * ($chargeValue / 100)
            : $chargeValue;

        $fedAmount = 0;
        if ($tierCharge->productCharge->apply_fed) {
            $fedValue = $tierCharge->productCharge->fed_charges_value ?? 0;

            if ($tierCharge->is_fed_inclusive) {
                $baseChargeAmount = $chargeAmount / (1 + ($fedValue / 100));
                $fedAmount = $chargeAmount - $baseChargeAmount;
                $chargeAmount = $baseChargeAmount;
            } else {
                $fedAmount = $chargeAmount * ($fedValue / 100);
            }

            $totalFedCharges += $fedAmount;

            $fedLogData[] = [
                'application_id' => null,
                'application_installment_id' => null,
                'amount' => $fedAmount,
                'type' => 'fed',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $chargesData[] = [
            'product_tier_id' => $tier->id,
            'product_charge_id' => $tierCharge->product_charge_id,
            'charge_amount' => $chargeAmount,
            'apply_fed' => $tierCharge->productCharge->apply_fed,
            'fed_amount' => $fedAmount,
            'charge_condition' => $tierCharge->productCharge->charge_condition ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $chargesLogData[] = [
            'application_id' => null,
            'application_installment_id' => null,
            'amount' => $chargeAmount,
            'type' => 'charges',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $totalCharges += $chargeAmount;
    }
}


        $totalCharges += $totalFedCharges;
        $disbursedAmount = $adjustedLoanAmount - $totalCharges;
        $outstandingAmount = $adjustedLoanAmount;

        $application = Application::create([
            'applicant_id' => $applicant->id,
            'product_id' => $product->id,
            'plan_id' => $plan->id,
            'status' => "disbursed",
            'loan_amount' => $adjustedLoanAmount,
            'disbursed_amount' => $disbursedAmount,
            'total_charges' => $totalCharges,
            'outstanding_amount' => $outstandingAmount,
        ]);

        foreach ($chargesLogData as &$log) {
            $log['application_id'] = $application->id;
        }
        foreach ($fedLogData as &$fedLog) {
            $fedLog['application_id'] = $application->id;
        }
        ApplicationLog::insert(array_merge($chargesLogData, $fedLogData));

        foreach ($chargesData as &$charge) {
            $charge['application_id'] = $application->id;
        }
        DB::table('application_charges')->insert($chargesData);

        ApplicationLog::create([
            'application_id' => $application->id,
            'application_installment_id' => null,
            'amount' => $disbursedAmount,
            'type' => 'disbursement',
        ]);

        if ($plan->duration_unit === 'months') {
            $baseInstallmentAmount = floor($outstandingAmount / $plan->duration_value);
            $remainder = $outstandingAmount - ($baseInstallmentAmount * $plan->duration_value);

            for ($i = 1; $i <= $plan->duration_value; $i++) {
                $finalAmount = ($i === $plan->duration_value) ? $baseInstallmentAmount + $remainder : $baseInstallmentAmount;

                DB::table('application_installments')->insert([
                    'application_id' => $application->id,
                    'amount' => $finalAmount,
                    'outstanding' => $finalAmount,
                    'due_date' => now()->addMonths($i),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($plan->duration_unit === 'days') {
            DB::table('application_installments')->insert([
                'application_id' => $application->id,
                'amount' => $outstandingAmount,
                'outstanding' => $outstandingAmount,
                'due_date' => now()->addDays($plan->duration_value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $applicant->creditLimit->update([
            'available_limit' => $availableLimit - $adjustedLoanAmount,
        ]);

        DB::commit();

       return response()->json([
            'message' => 'Application initiated successfully.',
            'application_id' => $application->id,
            'application_details' => new ApplicationResource($application)
                    ], 201)->header('Content-Type', 'application/json');

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['message' => 'Failed to initiate application: ' . $e->getMessage()], 500);
    }
}

public function calculateApplication(Request $request)
{
    $request->validate([
        'loan_amount' => 'required|numeric|min:1',
        'plan_id' => 'required|exists:product_plans,id',
        'product_id' => 'required|exists:products,id',
        'applicant_id' => 'required|exists:applicants,id',
    ]);

    try {
        $applicant = Applicant::with(['creditLimit', 'financingPolicy', 'products', 'applicantThreshold'])->findOrFail($request->applicant_id);

        if (!$applicant->is_active) {
            return response()->json(['message' => 'Application failed: Applicant is disabled.'], 403);
        }

        if (!$applicant->products->contains('id', $request->product_id)) {
            return response()->json(['message' => 'Application failed: Applicant cannot avail this product at this time.'], 403);
        }

        $financingPercentage = $applicant->financingPolicy->financing_percentage ?? 100;
        $adjustedLoanAmount = $request->loan_amount * ($financingPercentage / 100);
        if ($adjustedLoanAmount > ($applicant->creditLimit->available_limit ?? 0)) {
            return response()->json(['message' => 'Application failed: Adjusted loan amount exceeds available credit limit.'], 422);
        }

        $product = Product::with('productPlans')->findOrFail($request->product_id);
        if (!$product->is_active) {
            return response()->json(['message' => 'Application failed: Product is disabled.'], 403);
        }

        if (!$product->productPlans->contains('id', $request->plan_id)) {
            return response()->json(['message' => 'Application failed: The specified plan does not belong to the selected product.'], 422);
        }

        $minRequestedAmount = $product->min_requested_amount ?? 0;
        $maxRequestedAmount = $product->max_requested_amount ?? 1000000;
        if ($adjustedLoanAmount < $minRequestedAmount || $adjustedLoanAmount > $maxRequestedAmount) {
            return response()->json([
                'message' => 'Application failed: Requested Financing amount is out of the allowed range.',
                'adjusted_financing_amount' => $adjustedLoanAmount,
                'min_requested_amount' => $minRequestedAmount,
                'max_requested_amount' => $maxRequestedAmount,
            ], 422);
        }


        $plan = ProductPlan::with('productTier.productTierCharges.productCharge.charge')->findOrFail($request->plan_id);
        $tier = $plan->productTier;

        $totalCharges = 0;
        $chargesData = [];

        if (!empty($applicant->applicantThreshold) && 
            !empty($applicant->applicantThreshold->order_threshold) &&
            $request->loan_amount <= $applicant->applicantThreshold->order_threshold) {

            // Apply Applicant threshold charge
            $chargeAmount = $applicant->applicantThreshold->fixed_threshold_charges;

            $chargesData[] = [
                'charge_amount' => $chargeAmount,
                'charge_condition' => 'Applicant Threshold Charge',
            ];

            $totalCharges += $chargeAmount;

        } 
        // If Applicant threshold exists but loan amount exceeds it → SKIP tier threshold, go to percentage-based charges
        elseif (empty($applicant->applicantThreshold) && 
                !empty($tier->order_threshold) && 
                $request->loan_amount <= $tier->order_threshold) {

            // Apply tier threshold charge if Applicant threshold is not set
            $chargeAmount = $tier->fixed_threshold_charges;

            $chargesData[] = [
                'charge_amount' => $chargeAmount,
                'charge_condition' => 'Tier Threshold Charge',
            ];

            $totalCharges += $chargeAmount;
        }

        // If neither Applicant nor tier threshold applies, OR Applicant threshold exists but is exceeded → Use percentage-based calculation
        else
        {

            $applicantRule = ApplicantProductRule::where('applicant_id', $applicant->id)
                ->where('product_id', $request->product_id)
                ->first();

            foreach ($tier->productTierCharges as $tierCharge) {
                // Determine base amount for charge calculation
                $baseAmount = $tierCharge->productCharge->charge_condition === 'Order Amount'
                    ? $request->loan_amount
                    : $adjustedLoanAmount;

                // Use Applicant-specific charge unit and value if a Applicant rule exists, otherwise use default tier values
                $chargeUnit = $applicantRule ? $applicantRule->charge_unit : $tierCharge->charges_unit;
                $chargeValue = $applicantRule ? $applicantRule->charge_value : $tierCharge->charges_value;

                $chargeAmount = $chargeUnit === 'percentage'
                    ? $baseAmount * ($chargeValue / 100)
                    : $chargeValue;

                // Apply FED charges if applicable
                if ($tierCharge->productCharge->apply_fed && !$tierCharge->is_fed_inclusive) {
                    $chargeAmount += $chargeAmount * ($tierCharge->productCharge->fed_charges_value / 100);
                }

                $chargesData[] = [
                    'charge_amount' => $chargeAmount,
                    'charge_condition' => $tierCharge->productCharge->charge_condition ?? null,
                ];

                $totalCharges += $chargeAmount;
            }
        }

        $disbursedAmount = $adjustedLoanAmount - $totalCharges;

        // Installment calculation based on duration unit
        $installments = [];
        if ($plan->duration_unit === 'months') {
            $baseInstallmentAmount = floor($adjustedLoanAmount / $plan->duration_value);
            $remainder = $adjustedLoanAmount - ($baseInstallmentAmount * $plan->duration_value);

            for ($i = 1; $i <= $plan->duration_value; $i++) {
                $installments[] = [
                    'amount' => $i === $plan->duration_value ? $baseInstallmentAmount + $remainder : $baseInstallmentAmount,
                    'due_date' => now()->addMonths($i)->toDateString(),
                ];
            }
        } elseif ($plan->duration_unit === 'days') {
            $installments[] = [
                'amount' => $adjustedLoanAmount,
                'due_date' => now()->addDays($plan->duration_value)->toDateString(),
            ];
        } else {
            return response()->json(['message' => 'Application failed: Invalid duration unit.'], 422);
        }

        return response()->json([
            'message' => 'Application calculated successfully.',
            'loan_amount' => $request->loan_amount,
            'approved_amount' => $adjustedLoanAmount,
            'disbursed_amount' => $disbursedAmount,
            'charges' => $chargesData,
            'total_charges' => $totalCharges,
            'installments' => $installments,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to calculate application: ' . $e->getMessage()], 500);
    }
}




}
