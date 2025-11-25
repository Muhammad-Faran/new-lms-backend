<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Borrower;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        // Exclude specific borrower IDs (safe for future updates)
        $excludedBorrowerIds = [1]; // Add more IDs as needed

        // Retrieve filters from the request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $shipperName = $request->input('shipper_name');
        $productId = $request->input('product_id');
        // Base Transaction Query
        $baseQuery = Transaction::query();

        // Ensure transactions belong to borrowers who are NOT in the exclusion list
        $baseQuery->whereHas('borrower', function ($query) use ($excludedBorrowerIds, $shipperName) {
            $query->whereNotIn('id', $excludedBorrowerIds);

            if ($shipperName) {
                $query->where('shipper_name', 'LIKE', "%$shipperName%");
            }
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
        $transactionQuery = clone $baseQuery;

        // Total disbursed amount
        $totalDisbursedAmount = $transactionQuery->sum('disbursed_amount');

        // Total outstanding amount (AUM) for disbursed transactions
        $totalAUM = (clone $transactionQuery)->where('status', 'disbursed')->sum('outstanding_amount');

        // Total number of transactions (order count)
        $orderCount = (clone $transactionQuery)->count();

        // Total revenue (sum of total_charges in transactions table)
        $totalRevenue = (clone $transactionQuery)->sum('total_charges');

        // Total number of shippers
        $totalShippers = Borrower::whereNotIn('id', $excludedBorrowerIds)
            ->when($shipperName, function ($query) use ($shipperName) {
                return $query->where('shipper_name', 'LIKE', "%$shipperName%");
            })
            ->count();

        // Total active shippers in the last 3 months
        $threeMonthsAgo = now()->subMonths(3);
        $totalActiveShippers = Borrower::whereNotIn('id', $excludedBorrowerIds)
            ->when($shipperName, function ($query) use ($shipperName) {
                return $query->where('shipper_name', 'LIKE', "%$shipperName%");
            })
            ->whereHas('transactions', function ($query) use ($threeMonthsAgo, $fromDate, $toDate, $productId) {
                $query->where('created_at', '>=', $threeMonthsAgo);

                if ($fromDate) {
                    $query->where('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->where('created_at', '<=', $toDate);
                }
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            })
            ->count();

        // Count of delinquent orders (transactions with overdue & unpaid installments)
        $delinquentOrdersCount = (clone $transactionQuery)->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now())->where('status', 'unpaid');
        })->count();

        // Volume of delinquent loans (sum of disbursed_amount for delinquent transactions)
        $volumeOfDelinquentLoans = (clone $transactionQuery)->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now())->where('status', 'unpaid');
        })->sum('disbursed_amount');

        return response()->json([
            'total_disbursed_amount' => $totalDisbursedAmount,
            'total_aum' => $totalAUM,
            'total_shippers' => $totalShippers,
            'total_active_shippers' => $totalActiveShippers,
            'order_count' => $orderCount,
            'total_revenue' => $totalRevenue,
            'delinquent_orders_count' => $delinquentOrdersCount,
            'volume_of_delinquent_loans' => $volumeOfDelinquentLoans,
        ], 200);
    }
}
