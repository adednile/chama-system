<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Repayment;
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

        // Calculate rolling 12-month timeline and cumulative savings values
        $months = [];
        $savingsData = [];
        $monthKeys = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKeys[$date->format('Y-m')] = [
                'label' => $date->format('M Y'),
                'contrib' => 0,
                'repay' => 0,
                'fine' => 0,
            ];
        }
        foreach ($contributions as $contrib) {
            $key = substr($contrib->contribution_date, 0, 7);
            if (isset($monthKeys[$key])) {
                $monthKeys[$key]['contrib'] += $contrib->amount;
            }
        }
        $allRepayments = Repayment::whereHas('loan', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('repayment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->get();
        foreach ($allRepayments as $repay) {
            $key = substr($repay->repayment_date, 0, 7);
            if (isset($monthKeys[$key])) {
                $monthKeys[$key]['repay'] += $repay->repayment_amount;
            }
        }
        foreach ($fines as $fine) {
            $key = substr($fine->due_date, 0, 7);
            if (isset($monthKeys[$key])) {
                $monthKeys[$key]['fine'] += $fine->amount;
            }
        }
        
        $cumulative = 0;
        foreach ($monthKeys as $key => $values) {
            $months[] = $values['label'];
            $net = $values['contrib'] - $values['repay'] - $values['fine'];
            $cumulative += $net;
            $savingsData[] = $cumulative;
        }

        // Configure QuickChart URL for Cumulative Savings Line Chart
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $months,
                'datasets' => [[
                    'label' => 'Cumulative Savings (Ksh)',
                    'data' => $savingsData,
                    'borderColor' => '#0052cc',
                    'backgroundColor' => 'rgba(0, 102, 255, 0.06)',
                    'fill' => true,
                    'borderWidth' => 2.5,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#0052cc'
                ]]
            ],
            'options' => [
                'title' => [
                    'display' => true,
                    'text' => 'Historical Savings Growth & Velocity'
                ],
                'scales' => [
                    'yAxes' => [[
                        'gridLines' => [
                            'color' => '#E0E0E0'
                        ]
                    ]]
                ]
            ]
        ];
        $savingsChartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));

        $data = compact('user', 'contributions', 'loans', 'fines', 'transactions', 'savingsChartUrl');

        // If 'download' parameter is present, generate PDF
        if (request()->has('download')) {
            $pdf = Pdf::loadView('pdf.statement', $data)->setOption('isRemoteEnabled', true);
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

        // Calculate rolling 12-month MoM chama stats
        $chamaMonthKeys = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chamaMonthKeys[$date->format('Y-m')] = [
                'label' => $date->format('M Y'),
                'contrib' => 0,
                'fines' => 0,
            ];
        }
        foreach ($contributions as $contrib) {
            $key = substr($contrib->contribution_date, 0, 7);
            if (isset($chamaMonthKeys[$key])) {
                $chamaMonthKeys[$key]['contrib'] += $contrib->amount;
            }
        }
        foreach ($fines as $fine) {
            $key = substr($fine->due_date, 0, 7);
            if (isset($chamaMonthKeys[$key])) {
                $chamaMonthKeys[$key]['fines'] += $fine->amount;
            }
        }
        
        $chamaMonths = [];
        $chamaMonthlyContribs = [];
        $chamaCumulativeValue = [];
        $chamaCumulative = 0;
        foreach ($chamaMonthKeys as $key => $values) {
            $chamaMonths[] = $values['label'];
            $chamaMonthlyContribs[] = $values['contrib'];
            $chamaCumulative += ($values['contrib'] + $values['fines']);
            $chamaCumulativeValue[] = $chamaCumulative;
        }

        // Configure Dual-Axis QuickChart URL for Group Report
        $chamaChartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $chamaMonths,
                'datasets' => [
                    [
                        'type' => 'bar',
                        'label' => 'Monthly Volume (Left Axis)',
                        'data' => $chamaMonthlyContribs,
                        'backgroundColor' => 'rgba(86, 94, 116, 0.6)',
                        'yAxisID' => 'y-axis-1'
                    ],
                    [
                        'type' => 'line',
                        'label' => 'Cumulative Value (Right Axis)',
                        'data' => $chamaCumulativeValue,
                        'borderColor' => '#0052cc',
                        'borderWidth' => 2.5,
                        'fill' => false,
                        'pointRadius' => 4,
                        'pointBackgroundColor' => '#0052cc',
                        'yAxisID' => 'y-axis-2'
                    ]
                ]
            ],
            'options' => [
                'title' => [
                    'display' => true,
                    'text' => 'Chama MoM Growth & Contribution Trends'
                ],
                'legend' => [
                    'position' => 'bottom'
                ],
                'scales' => [
                    'yAxes' => [
                        [
                            'id' => 'y-axis-1',
                            'position' => 'left',
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Monthly Volume (Ksh)'
                            ],
                            'gridLines' => [
                                'color' => '#E0E0E0'
                            ]
                        ],
                        [
                            'id' => 'y-axis-2',
                            'position' => 'right',
                            'scaleLabel' => [
                                'display' => true,
                                'labelString' => 'Cumulative Value (Ksh)'
                            ],
                            'gridLines' => [
                                'display' => false
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $groupChartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chamaChartConfig));

        $data = compact('users', 'contributions', 'loans', 'fines', 'groupChartUrl');

        if ($request->has('download')) {
            $pdf = Pdf::loadView('pdf.group-report', $data)->setOption('isRemoteEnabled', true);
            return $pdf->download("group-report-" . now()->format('Y-m-d') . ".pdf");
        }

        return view('Treasurer.reports', $data);
    }
}