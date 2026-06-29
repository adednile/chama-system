<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MpesaParserController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChamaConfigController;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Member routes (secured with role:member middleware)
    Route::middleware('role:member')->group(function () {
        Route::get('/member/contributions', [ContributionController::class, 'index'])->name('member.contributions');
        Route::post('/member/contributions/parse-sms', [ContributionController::class, 'parseSms'])->name('member.contributions.parseSms');

        Route::get('/member/loans', [LoanController::class, 'index'])->name('member.loans');
        Route::post('/member/loans', [LoanController::class, 'store'])->name('member.loans.store');

        Route::get('/member/attendance', [MeetingController::class, 'memberAttendance'])->name('member.attendance');
    });

    // Treasurer routes (secured with role:treasurer middleware)
    Route::middleware('role:treasurer')->group(function () {
        Route::get('/treasurer/loans/pending', [LoanController::class, 'pending'])->name('treasurer.loans.pending');
        Route::post('/treasurer/loans/{loan}/approve', [LoanController::class, 'approve'])->name('treasurer.loans.approve');
        Route::post('/treasurer/loans/{loan}/reject', [LoanController::class, 'reject'])->name('treasurer.loans.reject');

        Route::get('/treasurer/penalties', [PenaltyController::class, 'index'])->name('treasurer.penalties');
        Route::post('/treasurer/penalties/{fine}/mark-paid', [PenaltyController::class, 'markPaid'])->name('treasurer.penalties.markPaid');

        Route::get('/treasurer/sms-parser', [MpesaParserController::class, 'index'])->name('treasurer.sms-parser');
        Route::post('/treasurer/sms-parser', [MpesaParserController::class, 'store'])->name('treasurer.sms-parser.store');
        Route::post('/treasurer/sms-parser/{tx}/match', [MpesaParserController::class, 'match'])->name('treasurer.sms-parser.match');
        Route::post('/treasurer/sms-parser/{tx}/reject', [MpesaParserController::class, 'reject'])->name('treasurer.sms-parser.reject');

        Route::get('/reports/treasurer', [ReportController::class, 'treasurerReports'])->name('reports.treasurer');

        Route::get('/treasurer/chama/config', [ChamaConfigController::class, 'edit'])->name('treasurer.chama.config');
        Route::post('/treasurer/chama/config', [ChamaConfigController::class, 'update'])->name('treasurer.chama.config.update');

        Route::get('/treasurer/meetings', [MeetingController::class, 'index'])->name('treasurer.meetings');
        Route::post('/treasurer/meetings', [MeetingController::class, 'store'])->name('treasurer.meetings.store');
        Route::patch('/treasurer/meetings/{meeting}', [MeetingController::class, 'update'])->name('treasurer.meetings.update');
        Route::delete('/treasurer/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('treasurer.meetings.destroy');
        Route::get('/treasurer/meetings/{meeting}/attendance', [MeetingController::class, 'attendance'])->name('treasurer.meetings.attendance');
        Route::post('/treasurer/meetings/{meeting}/attendance', [MeetingController::class, 'saveAttendance'])->name('treasurer.meetings.saveAttendance');
    });

    // Report routes for members (and authorized treasurers)
    Route::get('/reports/member/{user}', [ReportController::class, 'memberStatement'])->name('reports.member');
});

require __DIR__.'/auth.php';