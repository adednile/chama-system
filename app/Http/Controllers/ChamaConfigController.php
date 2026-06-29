<?php

namespace App\Http\Controllers;

use App\Models\Chama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChamaConfigController extends Controller
{
    public function edit()
    {
        $chama = Auth::user()->chama;
        return view('Treasurer.group-config', compact('chama'));
    }

    public function update(Request $request)
    {
        $chama = Auth::user()->chama;

        $data = $request->validate([
            'contribution_target' => ['required', 'numeric', 'min:0'],
            'collection_cutoff' => ['required', 'date'],
            'late_penalty_flat' => ['required', 'numeric', 'min:0'],
            'interest_rate_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_credit_score' => ['required', 'numeric', 'min:1', 'max:10'],
            'savings_weight' => ['required', 'numeric', 'min:0', 'max:1'],
            'attendance_weight' => ['required', 'numeric', 'min:0', 'max:1'],
            'repayment_weight' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        // Normalize weights to sum to 1.0
        $weightSum = $data['savings_weight'] + $data['attendance_weight'] + $data['repayment_weight'];
        if (abs($weightSum - 1.0) > 0.01) {
            return redirect()->back()->withErrors(['weights' => 'The sum of weights (Savings, Attendance, Repayment) must equal 1.0. (Currently ' . $weightSum . ')'])->withInput();
        }

        $chama->update($data);

        return redirect()->back()->with('success', 'Group configuration updated successfully.');
    }
}
