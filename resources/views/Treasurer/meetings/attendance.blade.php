@extends('layouts.app')
@section('title', 'Mark Meeting Attendance')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <a href="{{ route('treasurer.meetings') }}" class="hover:text-primary transition-colors">Meetings</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-slate-800 font-medium">Mark Attendance</span>
    </div>

    <div class="premium-card p-6 rounded-2xl border-l-4 border-gold-500 relative overflow-hidden mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-title font-bold text-xl text-slate-800">Track Attendance</h2>
                <p class="text-xs text-slate-500 mt-1">Meeting Date: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F d, Y \a\t h:i A') }}</span></p>
            </div>
            <div>
                @if($meeting->meeting_type === 'regular')
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">Regular</span>
                @elseif($meeting->meeting_type === 'agm')
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">AGM</span>
                @else
                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">Special</span>
                @endif
            </div>
        </div>
        @if($meeting->notes)
            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600">
                <p class="font-bold text-slate-500 mb-1">Agenda & Notes:</p>
                <p class="leading-relaxed">{{ $meeting->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Attendance Form Checklist -->
    <div class="premium-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold font-title text-slate-800">Chama Member Attendance Checklist</h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Toggle membership present status below</p>
            </div>
            <div class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm" id="summary-header">
                {{ $attendances->where('present', true)->count() }} of {{ $attendances->count() }} Present
            </div>
        </div>

        <form method="POST" action="{{ route('treasurer.meetings.saveAttendance', $meeting) }}">
            @csrf
            <div class="divide-y divide-slate-100">
                @forelse($attendances as $attendance)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gold-500 to-gold-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-slate-800">{{ $attendance->user->name }}</span>
                                <span class="block text-xs text-slate-400 font-medium">{{ $attendance->user->email }}</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <!-- Toggle switch -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="present[]" value="{{ $attendance->user_id }}" class="sr-only peer" {{ $attendance->present ? 'checked' : '' }} onchange="updateSummary()">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-xs font-semibold text-slate-500 select-none peer-checked:text-emerald-600">Present</span>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-400">
                        <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">group</span>
                        No members found in this Chama.
                    </div>
                @endforelse
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
                <a href="{{ route('treasurer.meetings') }}" class="flex-1 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold text-xs rounded-xl text-center hover:bg-slate-50 transition active:scale-95">Cancel</a>
                <button type="submit" class="flex-1 py-2.5 gold-gradient-btn font-bold text-xs rounded-xl shadow-md active:scale-95">Save Attendance Records</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateSummary() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="present[]"]');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const totalCount = checkboxes.length;
        
        const summary = document.getElementById('summary-header');
        if (summary) {
            summary.textContent = `${checkedCount} of ${totalCount} Present`;
        }
    }
</script>
@endpush
