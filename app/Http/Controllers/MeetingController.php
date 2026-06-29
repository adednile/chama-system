<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\User;
use App\Services\CreditScoringEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings for Treasurer.
     */
    public function index()
    {
        $chama = Auth::user()->chama;
        $meetings = $chama->meetings()->latest()->paginate(10);

        // Calculate average attendance % across meetings that have attendance records
        $totalMembers = $chama->users()->where('role', 'member')->count();
        $totalMeetingsWithAttendance = 0;
        $sumAttendancePercentages = 0;

        foreach ($chama->meetings as $meeting) {
            $totalAttendanceRecords = $meeting->attendances()->count();
            if ($totalAttendanceRecords > 0 && $totalMembers > 0) {
                $presentCount = $meeting->attendances()->where('present', true)->count();
                $sumAttendancePercentages += ($presentCount / $totalMembers) * 100;
                $totalMeetingsWithAttendance++;
            }
        }

        $averageAttendance = $totalMeetingsWithAttendance > 0 
            ? round($sumAttendancePercentages / $totalMeetingsWithAttendance, 1) 
            : 0;

        // Get the next scheduled meeting in the future
        $nextMeeting = $chama->meetings()
            ->where('meeting_date', '>=', now()->toDateString())
            ->orderBy('meeting_date', 'asc')
            ->first();

        return view('Treasurer.meetings.index', compact('meetings', 'averageAttendance', 'nextMeeting'));
    }

    /**
     * Store a newly created meeting.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string|in:regular,agm,special',
            'notes' => 'nullable|string|max:1000',
        ]);

        $chama = Auth::user()->chama;
        $meeting = $chama->meetings()->create([
            'meeting_date' => $data['meeting_date'],
            'meeting_type' => $data['meeting_type'],
            'notes' => $data['notes'] ?? '',
        ]);

        // Pre-populate attendance for all members as present = false
        $members = $chama->users()->where('role', 'member')->get();
        foreach ($members as $member) {
            Attendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $member->id,
                'present' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Meeting created successfully.');
    }

    /**
     * Show the checklist for marking attendance.
     */
    public function attendance(Meeting $meeting)
    {
        $chama = Auth::user()->chama;

        if ($meeting->chama_id !== $chama->id) {
            abort(403, 'Unauthorized.');
        }

        // Dynamically ensure all current members have an attendance record for this meeting
        $members = $chama->users()->where('role', 'member')->get();
        foreach ($members as $member) {
            Attendance::firstOrCreate([
                'meeting_id' => $meeting->id,
                'user_id' => $member->id,
            ], [
                'present' => false,
            ]);
        }

        $attendances = $meeting->attendances()->with('user')->get();

        return view('Treasurer.meetings.attendance', compact('meeting', 'attendances'));
    }

    /**
     * Save the attendance checklist.
     */
    public function saveAttendance(Meeting $meeting, Request $request)
    {
        $chama = Auth::user()->chama;

        if ($meeting->chama_id !== $chama->id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'present' => 'nullable|array',
            'present.*' => 'exists:users,id',
        ]);

        $presentUserIds = $request->input('present', []);

        $attendances = $meeting->attendances;
        foreach ($attendances as $attendance) {
            $isPresent = in_array($attendance->user_id, $presentUserIds);
            if ($attendance->present !== $isPresent) {
                $attendance->update(['present' => $isPresent]);
            }
        }

        return redirect()->route('treasurer.meetings')->with('success', 'Attendance checklist updated successfully.');
    }

    /**
     * Display the member's personal attendance statement.
     */
    public function memberAttendance()
    {
        $user = Auth::user();
        $chama = $user->chama;

        $meetingsCount = Meeting::where('chama_id', $chama->id)->count();
        $attendances = $user->attendances()->with('meeting')->get();
        $attendedCount = $attendances->where('present', true)->count();

        // Calculate attendance reliability %
        $reliability = $meetingsCount > 0 
            ? round(($attendedCount / $meetingsCount) * 100, 1) 
            : 100;

        // Calculate credit score using engine
        $scoringEngine = new CreditScoringEngine();
        $creditScore = $scoringEngine->calculateScore($user);

        // Fetch configured weights
        $weights = [
            'savings' => $chama->savings_weight ?? 0.4,
            'repayment' => $chama->repayment_weight ?? 0.3,
            'attendance' => $chama->attendance_weight ?? 0.2,
        ];

        // Determine component contributions
        $rawAttendanceScore = $meetingsCount > 0 ? round(($attendedCount / $meetingsCount) * 10, 1) : 10;
        $attendanceContribution = round($rawAttendanceScore * $weights['attendance'], 1);
        $maxAttendanceContribution = round(10 * $weights['attendance'], 1);

        // Sort attendances by meeting date descending for list view & streak calculation
        $sortedAttendances = $attendances->sortByDesc(function ($att) {
            return $att->meeting->meeting_date;
        });

        // Calculate present streak
        $streak = 0;
        foreach ($sortedAttendances as $att) {
            if ($att->present) {
                $streak++;
            } else {
                break;
            }
        }

        // Calculate rank in Chama based on attendance rate
        $allMembers = $chama->users()->where('role', 'member')->get();
        $rankedMembers = $allMembers->map(function ($member) use ($meetingsCount) {
            $attended = $member->attendances()->where('present', true)->count();
            $rate = $meetingsCount > 0 ? ($attended / $meetingsCount) * 100 : 100;
            return [
                'user_id' => $member->id,
                'rate' => $rate,
            ];
        })->sortByDesc('rate')->values();

        $rank = 1;
        foreach ($rankedMembers as $index => $ranked) {
            if ($ranked['user_id'] === $user->id) {
                $rank = $index + 1;
                break;
            }
        }

        return view('Member.attendance.index', compact(
            'sortedAttendances',
            'meetingsCount',
            'attendedCount',
            'reliability',
            'creditScore',
            'attendanceContribution',
            'maxAttendanceContribution',
            'streak',
            'rank',
            'weights'
        ));
    }

    /**
     * Update the specified meeting (postpone/change details).
     */
    public function update(Request $request, Meeting $meeting)
    {
        $chama = Auth::user()->chama;

        if ($meeting->chama_id !== $chama->id) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string|in:regular,agm,special',
            'notes' => 'nullable|string|max:1000',
        ]);

        $meeting->update($data);

        return redirect()->back()->with('success', 'Meeting updated successfully.');
    }

    /**
     * Cancel/delete the specified meeting.
     */
    public function destroy(Meeting $meeting)
    {
        $chama = Auth::user()->chama;

        if ($meeting->chama_id !== $chama->id) {
            abort(403, 'Unauthorized.');
        }

        $meeting->delete();

        return redirect()->back()->with('success', 'Meeting cancelled successfully.');
    }
}
