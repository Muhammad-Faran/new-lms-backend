<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Applicant;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        // Exclude specific applicant IDs (safe for future updates)
        $excludedApplicantIds = [1]; // Add more IDs as needed

        // Retrieve filters from the request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $productId = $request->input('product_id');
        // Base Application Query
        $baseQuery = Application::query();

        // Ensure applications belong to applicants who are NOT in the exclusion list
        $baseQuery->whereHas('applicant', function ($query) use ($excludedApplicantIds) {
            $query->whereNotIn('id', $excludedApplicantIds);

        });

        // Apply filters only when they exist
        if ($fromDate) {
            $baseQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $baseQuery->whereDate('created_at', '<=', $toDate);
        }
        if ($productId) {
            $baseQuery->where('product_id', $productId);
        }
        // Clone query before applying different aggregates
        $applicationQuery = clone $baseQuery;

        // Total disbursed amount
        $totalDisbursedAmount = $applicationQuery->sum('disbursed_amount');

        // Total outstanding amount (AUM) for disbursed applications
        $totalAUM = (clone $applicationQuery)->where('status', 'disbursed')->sum('outstanding_amount');

        // Total number of applications (order count)
        $orderCount = (clone $applicationQuery)->count();

        // Total revenue (sum of total_charges in applications table)
        $totalRevenue = (clone $applicationQuery)->sum('total_charges');

        // Count of delinquent orders (applications with overdue & unpaid installments)
        $delinquentOrdersCount = (clone $applicationQuery)->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now())->where('status', 'unpaid');
        })->count();

        // Volume of delinquent loans (sum of disbursed_amount for delinquent applications)
        $volumeOfDelinquentLoans = (clone $applicationQuery)->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now())->where('status', 'unpaid');
        })->sum('disbursed_amount');

        return response()->json([
            'total_disbursed_amount' => $totalDisbursedAmount,
            'total_aum' => $totalAUM,
            'order_count' => $orderCount,
            'total_revenue' => $totalRevenue,
            'delinquent_orders_count' => $delinquentOrdersCount,
            'volume_of_delinquent_loans' => $volumeOfDelinquentLoans,
        ], 200);
    }
}
