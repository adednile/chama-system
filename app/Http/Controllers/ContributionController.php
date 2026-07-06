<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\MappedMpesaTransaction;
use App\Services\MpesaSMSParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    public function index()
    {
        $contributions = \App\Models\Contribution::query()
            ->where('user_id', Auth::id())
            ->where('chama_id', Auth::user()->chama_id)
            ->latest()
            ->get();

        return view('Member.contributions', compact('contributions'));
    }

    public function parseSms(Request $request, MpesaSMSParser $parser)
    {
        $data = $request->validate([
            'message'      => ['required', 'string'],
            'payment_type' => ['sometimes', 'in:contribution,loan_repayment,fine_payment'],
            'fine_id'      => ['sometimes', 'nullable', 'exists:fines,id'],
        ]);

        $parsed = $parser->parse($data['message']);

        if (empty($parsed['transaction_code']) || floatval($parsed['amount']) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse a valid M-Pesa transaction code or amount. Please check the SMS text.',
            ], 422);
        }

        if ($parsed['transaction_code']) {
            $exists = MappedMpesaTransaction::where('transaction_code', $parsed['transaction_code'])->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This M-Pesa transaction code has already been submitted.',
                ], 422);
            }
        }

        $paymentType = $data['payment_type'] ?? 'contribution';
        $loanId      = null;
        $fineId      = null;

        if ($paymentType === 'loan_repayment') {
            $activeLoan = Loan::where('user_id', Auth::id())
                ->whereIn('status', ['active', 'overdue'])
                ->first();

            if (!$activeLoan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active loan found on your account. Please submit this as a Savings Contribution.',
                ], 422);
            }
            $loanId = $activeLoan->id;
        }

        if ($paymentType === 'fine_payment') {
            $fine = null;
            if ($request->input('fine_id')) {
                $fine = \App\Models\Fine::where('id', $request->input('fine_id'))
                    ->where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->first();
            } else {
                $fine = \App\Models\Fine::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->first();
            }

            if (!$fine) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending fine found on your account. Please submit this as a Savings Contribution.',
                ], 422);
            }

            $parsedAmount = round((float) $parsed['amount'], 2);
            $fineAmount = round((float) $fine->amount, 2);
            if ($parsedAmount !== $fineAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "The payment amount (KES " . number_format($parsedAmount, 2) . ") does not match the exact fine amount (KES " . number_format($fineAmount, 2) . "). Partial payments are not allowed.",
                ], 422);
            }

            $fineId = $fine->id;
        }

        MappedMpesaTransaction::create([
            'user_id'          => Auth::id(),
            'amount'           => $parsed['amount'],
            'sender'           => $parsed['sender'],
            'transaction_code' => $parsed['transaction_code'],
            'message'          => $parsed['message'],
            'status'           => 'unmapped',
            'payment_type'     => $paymentType,
            'loan_id'          => $loanId,
            'fine_id'          => $fineId,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'amount'           => number_format($parsed['amount'], 2),
                'sender'           => $parsed['sender'],
                'transaction_code' => $parsed['transaction_code'],
                'date'             => $parsed['date'] ?? now()->toDateString(),
                'payment_type'     => $paymentType,
                'fine_id'          => $fineId,
            ],
        ]);
    }
}
