<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Support\Facades\Auth;

class PenaltyController extends Controller
{
    public function index()
    {
        $fines = Fine::query()
            ->where('chama_id', Auth::user()->chama_id)
            ->latest()
            ->get();

        return view('Treasurer.penalties', compact('fines'));
    }

    public function markPaid(Fine $fine, \App\Services\LedgerService $ledgerService)
    {
        $fine->update([
            'status' => 'paid',
            'paid_at' => now()->toDateString(),
        ]);

        // Record in ledger
        $ledgerService->record(
            'fine_paid',
            $fine->user_id,
            $fine->chama_id,
            $fine->amount,
            'Fine payment: ' . $fine->description,
            $fine->id
        );

        // Check if user has other unpaid fines
        $user = $fine->user;
        if ($user) {
            $unpaidFinesCount = $user->fines()->where('status', 'unpaid')->count();
            if ($unpaidFinesCount === 0) {
                $user->update(['account_status' => 'active']);
            }
        }

        return redirect()->back()->with('success', 'Fine marked as paid.');
    }
}
