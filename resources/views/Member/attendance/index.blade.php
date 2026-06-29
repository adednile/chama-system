@extends('layouts.app')
@section('title', 'Member Attendance Statement')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-slate-800 font-medium">Attendance Statement</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Summary & Credit Widget -->
        <div class="lg:col-span-4 space-y-6">
            <!-- 1. Personal Attendance Tracker Card -->
            <div class="premium-card rounded-2xl p-6 flex flex-col items-center text-center">
                <h3 class="text-sm font-bold font-title text-slate-800 self-start mb-4">Personal Tracking</h3>
                
                <div class="relative w-40 h-40 mb-4">
                    <!-- Circular Progress Ring -->
                    <svg class="w-full h-full" viewBox="0 0 100 100">
                        <circle class="text-slate-100" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-width="8"></circle>
                        <circle class="text-gold-600" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * ($reliability / 100)) }}" stroke-linecap="round" stroke-width="8" transform="rotate(-90 50 50)"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-title font-black text-2xl text-slate-800">{{ $reliability }}%</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Reliability</span>
                    </div>
                </div>

                <div class="space-y-0.5">
                    <p class="text-sm font-bold text-slate-700">{{ $attendedCount }} / {{ $meetingsCount }} Meetings</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Attendance Ratio</p>
                </div>

                <div class="mt-6 w-full pt-4 border-t border-slate-100 grid grid-cols-2 gap-4">
                    <div class="text-left">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Present Streak</span>
                        <span class="text-sm font-extrabold text-emerald-600">{{ $streak }} {{ Str::plural('Meeting', $streak) }}</span>
                    </div>
                    <div class="text-left">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Group Rank</span>
                        <span class="text-sm font-extrabold text-gold-600">#{{ $rank }} Member</span>
                    </div>
                </div>
            </div>

            <!-- 2. Credit Score Impact Calculator Widget -->
            <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute -top-4 -right-4 p-4 opacity-5">
                    <span class="material-symbols-outlined text-[80px] font-bold">verified_user</span>
                </div>
                <h3 class="text-sm font-bold font-title text-slate-800 mb-4">Credit Score Impact</h3>
                
                <div class="mb-4">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-semibold text-slate-400">Current Credit Rating</span>
                        <span class="text-xl font-black text-gold-600 leading-tight">
                            {{ $creditScore }}<span class="text-xs text-slate-400 font-medium ml-0.5">/ 10.0</span>
                        </span>
                    </div>
                    <!-- Visual Breakdown Bar -->
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gold-600" style="width: {{ $creditScore * 10 }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold-600 text-base">calendar_today</span>
                            <span class="font-bold text-slate-600">Attendance Contribution</span>
                        </div>
                        <span class="font-bold text-gold-600">{{ $attendanceContribution }} / {{ $maxAttendanceContribution }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-semibold border-t border-slate-100 pt-2">
                        <span>Attendance Weightage</span>
                        <span class="text-slate-600 font-bold">{{ $weights['attendance'] * 100 }}% of Total Score</span>
                    </div>
                </div>

                <div class="mt-4 flex gap-2.5 p-3 rounded-xl bg-rose-50 border border-rose-200">
                    <span class="material-symbols-outlined text-rose-600 text-base font-bold">info</span>
                    <p class="text-[10px] text-slate-500 font-medium leading-relaxed">
                        Missing meetings directly reduces your raw attendance score, lowering your credit score and reducing your borrowing capacity.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Meeting History List -->
        <div class="lg:col-span-8">
            <div class="premium-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-bold font-title text-slate-800">Meeting History</h3>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Detailed record of the current financial year</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                                <th class="px-6 py-3">Meeting Date</th>
                                <th class="px-6 py-3">Meeting Purpose</th>
                                <th class="px-6 py-3">Meeting Type</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                            @forelse($sortedAttendances as $attendance)
                                <tr class="hover:bg-slate-50/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($attendance->meeting->meeting_date)->format('M d, Y') }}</span>
                                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($attendance->meeting->meeting_date)->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate" title="{{ $attendance->meeting->notes }}">
                                        {{ $attendance->meeting->notes ?: 'Regular Deliberations' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($attendance->meeting->meeting_type === 'regular')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100">Regular</span>
                                        @elseif($attendance->meeting->meeting_type === 'agm')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-100">AGM</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-100">Special</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($attendance->present)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Present
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                                Absent
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">event_available</span>
                                        No attendance logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-semibold">Showing {{ $sortedAttendances->count() }} of {{ $meetingsCount }} meetings</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
