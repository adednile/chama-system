<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display or download a member's personal financial statement.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function memberStatement(User $user)
    {
        $authUser = Auth::user();

        // Authorization: only the member themselves or a treasurer in the same Chama
        if ($authUser->id !== $user->id && $authUser->role !== 'treasurer') {
            abort(403, 'Unauthorized.');
        }
        if ($authUser->role === 'treasurer' && $authUser->chama_id !== $user->chama_id) {
            abort(403, 'You are not in the same Chama.');
        }

        $contributions = Contribution::where('user_id', $user->id)->get();
        $loans = Loan::where('user_id', $user->id)->get();
        $fines = Fine::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)->latest()->get();

        $data = compact('user', 'contributions', 'loans', 'fines', 'transactions');

        // If 'download' parameter is present, generate PDF
        if (request()->has('download')) {
            $pdf = Pdf::loadView('pdf.statement', $data);
            return $pdf->download("statement-{$user->id}.pdf");
        }

        return view('Member.statement', $data);
    }

    /**
     * Display or download the treasurer's group financial report.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function treasurerReports(Request $request)
    {
        $chamaId = Auth::user()->chama_id;

        $users = User::where('chama_id', $chamaId)->where('role', 'member')->get();
        $contributions = Contribution::where('chama_id', $chamaId)->get();
        $loans = Loan::where('chama_id', $chamaId)->get();
        $fines = Fine::where('chama_id', $chamaId)->get();

        $data = compact('users', 'contributions', 'loans', 'fines');

        if ($request->has('download')) {
            $pdf = Pdf::loadView('pdf.group-report', $data);
            return $pdf->download("group-report-" . now()->format('Y-m-d') . ".pdf");
        }

        return view('Treasurer.reports', $data);
    }
}