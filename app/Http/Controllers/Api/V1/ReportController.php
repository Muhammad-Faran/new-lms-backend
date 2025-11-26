<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TransactionInstallment;
use App\Filters\V1\OverdueLoanFilter;
use App\Models\Transaction;
use App\Http\Resources\V1\OverdueLoanCollection;
use Illuminate\Http\Request;
use App\Services\ExportService;
use Carbon\Carbon;


class ReportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Get Overdue Loans Report.
     */
   public function getOverdueLoans(Request $request)
{
    // Fetch transactions where any of its installments are overdue and unpaid
    $query = Transaction::with(['applicant', 'installments'])
        ->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now()) // Overdue
                  ->where('status', 'unpaid'); // Only unpaid installments
        });

    // Apply filters
    $filter = new OverdueLoanFilter();
    $overdueLoans = $filter->filter($query, $request);

    return new OverdueLoanCollection($overdueLoans);
}

/**
 * Export Overdue Loans Report.
 */
public function exportOverdueLoans(Request $request)
{
    $filter = new OverdueLoanFilter();

    // Fetch transactions where any of its installments are overdue and unpaid
    $query = Transaction::with(['applicant', 'installments'])
        ->whereHas('installments', function ($query) {
            $query->where('due_date', '<', now())
                  ->where('status', 'unpaid');
        });

    // Apply filters
    $overdueLoans = $filter->filter($query, $request);

    if ($overdueLoans->isEmpty()) {
        return response()->json(['message' => 'No overdue loans found.'], 404);
    }

    // Prepare data for export
    $data = $overdueLoans->map(function ($transaction) {
        // Find the earliest overdue installment for due date reference
        $overdueInstallment = $transaction->installments
            ->where('due_date', '<', now())
            ->where('status', 'unpaid')
            ->sortBy('due_date')
            ->first();

            $dueDate = optional($overdueInstallment)->due_date;

        return [
            'Applicant Name' => optional($transaction->applicant)->first_name . ' ' . optional($transaction->applicant)->last_name,
            'Transaction ID' => $transaction->id,
            'Amount' => $transaction->loan_amount,
            'Date of Disbursement' => optional($transaction->created_at)->format('Y-m-d'),
            'Due Date' => optional($overdueInstallment)->due_date,
            'Days Overdue' => abs($dueDate
                    ? now()->setTimezone('Asia/Karachi')->startOfDay()->diffInDays(
                        Carbon::parse($dueDate)->setTimezone('Asia/Karachi')->startOfDay()
                    )
                    : null),
        ];
    })->toArray();

    $headers = [
        'Applicant Name', 'Transaction ID', 'Amount', 'Date of Disbursement', 'Due Date', 'Days Overdue'
    ];

    // Use ExportService to create an Excel file
    return $this->exportService->exportToExcel('overdue_transactions.xlsx', $data, $headers);
}

}
