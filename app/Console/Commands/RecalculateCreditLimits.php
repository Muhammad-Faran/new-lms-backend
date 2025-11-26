<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Applicant;
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
    protected $description = 'Recalculate and update available credit limits for all applicants based on disbursed transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting credit limit recalculation...');

        // Fetch all active applicants with enabled credit limits
        $applicants = Applicant::where('status', 1)
            ->whereHas('creditLimit', function ($query) {
                $query->where('status', 'active'); // Only applicants with active credit limits
            })
            ->get();

        $this->info('Processing ' . $applicants->count() . ' applicants...');

        DB::transaction(function () use ($applicants) {
            foreach ($applicants as $applicant) {
                $creditLimit = $applicant->creditLimit;

                if (!$creditLimit) {
                    continue; // Skip if no credit limit exists
                }

                // Sum of only disbursed transactions (excluding repaid/completed)
                $totalDisbursed = Transaction::where('applicant_id', $applicant->id)
                    ->where('status', 'disbursed') // Only disbursed transactions
                    ->sum('loan_amount');

                // Calculate new available limit
                $newAvailableLimit = $creditLimit->credit_limit - $totalDisbursed;

                // Update available credit limit
                $creditLimit->update(['available_limit' => $newAvailableLimit]);

                $this->info("Updated applicant ID: {$applicant->id}, New Available Limit: {$newAvailableLimit}");
            }
        });

        $this->info('Credit limit recalculation completed successfully.');
    }
}
