<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\CreditLimit;
use Illuminate\Http\Request;

class CreditLimitController extends Controller
{
    public function show(Request $request)
{
    $request->validate([
        'wallet_id' => 'required|string', // Wallet ID is required
    ]);

    // Find applicant by wallet_id
    $applicant = Applicant::where('wallet_id', $request->wallet_id)->first();

    if (!$applicant) {
        return response()->json([
            'message' => 'applicant not found.',
        ], 404);
    }

    // Check if the applicant is active
    if (!$applicant->is_active) {
        return response()->json([
            'message' => 'applicant is disabled.',
        ], 403); // Return a 403 Forbidden status for disabled applicants
    }

    // Fetch the applicant's credit limit
    $creditLimit = CreditLimit::where('applicant_id', $applicant->id)->first();

    if (!$creditLimit) {
        return response()->json([
            'message' => 'Credit limit not found for the applicant.',
        ], 404);
    }

    // Fetch the applicant's credit score data
    $creditScore = $applicant->creditEngineShipperCreditScore()->first();
    $creditScoreData = $creditScore && is_string($creditScore->data) 
        ? json_decode($creditScore->data, true) 
        : ($creditScore->data ?? null); // Handle case where data is already an array

    return response()->json([
        'credit_limit' => $creditLimit->credit_limit,
        'available_limit' => $creditLimit->available_limit,
        'status' => $creditLimit->status,
        'date_assigned' => $creditLimit->date_assigned,
        'credit_score' => $creditScoreData ? [
            'credit_score' => isset($creditScoreData['credit_score']) 
            ? round($creditScoreData['credit_score'], 2) 
            : null,
            'category' => $creditScoreData['category'] ?? null,
            'description' => $creditScoreData['description'] ?? null,
            'improvement_tips' => $creditScoreData['improvement_tips'] ?? [],
        ] : null,
    ], 200);
}

}
