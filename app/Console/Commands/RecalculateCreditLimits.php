<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrower;
use App\Models\Transaction;
use App\Models\CreditLimit;
use DB;

class RecalculateCreditLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credit-limits:recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and update available credit limits for all borrowers based on disbursed transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting credit limit recalculation...');

        // Fetch all active borrowers with enabled credit limits
        $borrowers = Borrower::where('status', 1)
            ->whereHas('creditLimit', function ($query) {
                $query->where('status', 'active'); // Only borrowers with active credit limits
            })
            ->get();

        $this->info('Processing ' . $borrowers->count() . ' borrowers...');

        DB::transaction(function () use ($borrowers) {
            foreach ($borrowers as $borrower) {
                $creditLimit = $borrower->creditLimit;

                if (!$creditLimit) {
                    continue; // Skip if no credit limit exists
                }

                // Sum of only disbursed transactions (excluding repaid/completed)
                $totalDisbursed = Transaction::where('borrower_id', $borrower->id)
                    ->where('status', 'disbursed') // Only disbursed transactions
                    ->sum('loan_amount');

                // Calculate new available limit
                $newAvailableLimit = $creditLimit->credit_limit - $totalDisbursed;

                // Update available credit limit
                $creditLimit->update(['available_limit' => $newAvailableLimit]);

                $this->info("Updated Borrower ID: {$borrower->id}, New Available Limit: {$newAvailableLimit}");
            }
        });

        $this->info('Credit limit recalculation completed successfully.');
    }
}
