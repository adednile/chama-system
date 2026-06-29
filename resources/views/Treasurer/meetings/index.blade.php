@extends('layouts.app')
@section('title', "Treasurer's Meeting Log")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-slate-800 font-medium">Meetings Management</span>
            </nav>
            <h2 class="font-title font-bold text-2xl text-slate-800">Treasurer's Meeting Log</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-xl">Review past deliberations, manage upcoming agendas, and track member participation across all Chama sessions.</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 hover:bg-slate-50 transition active:scale-95">
                <span class="material-symbols-outlined text-sm">download</span> Export Report
            </button>
            <button class="gold-gradient-btn px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md" onclick="openModal()">
                <span class="material-symbols-outlined text-sm">add</span> Create Meeting
            </button>
        </div>
    </div>

    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 font-medium">
        <div class="premium-card p-6 rounded-2xl border-t-4 border-emerald-500 relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-xl">groups</span>
                </div>
                <span class="text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Attendance Rate</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold">Average Attendance</p>
            <h3 class="text-2xl font-title font-black text-slate-800 mt-1">{{ $averageAttendance }}%</h3>
        </div>
        <div class="premium-card p-6 rounded-2xl border-t-4 border-gold-500 relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-xl">history</span>
                </div>
                <span class="text-amber-600 text-[10px] font-bold uppercase tracking-wider">Historical Log</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold">Meetings Conducted</p>
            <h3 class="text-2xl font-title font-black text-slate-800 mt-1">{{ $meetings->total() }}</h3>
        </div>
        <div class="premium-card p-6 rounded-2xl border-t-4 border-blue-500 relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-xl">calendar_today</span>
                </div>
                <span class="text-blue-600 text-[10px] font-bold uppercase tracking-wider">Agenda</span>
            </div>
            <p class="text-slate-500 text-xs font-semibold">Next Scheduled Meeting</p>
            <h3 class="text-lg font-title font-bold text-slate-800 mt-1">
                @if($nextMeeting)
                    {{ \Carbon\Carbon::parse($nextMeeting->meeting_date)->format('M d, Y') }} ({{ ucfirst($nextMeeting->meeting_type) }})
                @else
                    None Scheduled
                @endif
            </h3>
        </div>
    </div>

    <!-- Premium Table Section -->
    <div class="premium-card rounded-2xl overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold font-title text-slate-800">Recent & Scheduled Meetings</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 font-label-sm text-xs uppercase tracking-wider border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-3.5 font-semibold">Date</th>
                        <th class="px-6 py-3.5 font-semibold">Meeting Type</th>
                        <th class="px-6 py-3.5 font-semibold">Attendance</th>
                        <th class="px-6 py-3.5 font-semibold">Notes & Summary</th>
                        <th class="px-6 py-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 text-sm">
                    @forelse($meetings as $meeting)
                        @php
                            $totalMembers = auth()->user()->chama->users()->where('role', 'member')->count();
                            $recordCount = $meeting->attendances()->count();
                            $presentCount = $meeting->attendances()->where('present', true)->count();
                            $attendancePercentage = $recordCount > 0 && $totalMembers > 0 
                                ? round(($presentCount / $totalMembers) * 100) 
                                : null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-6 py-4.5">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}</span>
                                    <span class="text-xs text-slate-400 font-medium">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4.5">
                                @if($meeting->meeting_type === 'regular')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">Regular</span>
                                @elseif($meeting->meeting_type === 'agm')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">AGM</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">Special</span>
                                @endif
                            </td>
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3">
                                    @if(is_null($attendancePercentage))
                                        <span class="text-xs text-slate-400 italic">Not Logged</span>
                                    @else
                                        <div class="flex-1 h-1.5 w-24 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $attendancePercentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700">{{ $attendancePercentage }}%</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4.5">
                                <p class="text-xs text-slate-500 max-w-xs truncate" title="{{ $meeting->notes }}">{{ $meeting->notes ?: 'No description provided.' }}</p>
                            </td>
                            <td class="px-6 py-4.5 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end w-full">
                                    <a href="{{ route('treasurer.meetings.attendance', $meeting) }}" class="inline-flex items-center gap-1 text-xs font-bold text-gold-600 hover:text-gold-700 bg-amber-50 border border-amber-200 hover:bg-amber-100/50 py-1.5 px-2.5 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm font-bold">edit_calendar</span> Track
                                    </a>
                                    <button onclick="openEditModal({{ $meeting->id }}, '{{ $meeting->meeting_date->format('Y-m-d\TH:i') }}', '{{ $meeting->meeting_type }}', '{{ addslashes($meeting->notes) }}')" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100/50 py-1.5 px-2.5 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm font-bold">edit</span> Postpone
                                    </button>
                                    <form action="{{ route('treasurer.meetings.destroy', $meeting) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this meeting?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100/50 py-1.5 px-2.5 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm font-bold">delete</span> Cancel
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">calendar_clock</span>
                                No meetings created yet. Click "Create Meeting" to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetings->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $meetings->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add New Meeting Modal -->
<div class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="meeting-modal">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <!-- Modal Content -->
    <div class="relative w-full max-w-lg bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200 translate-y-8 transition-transform duration-500">
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-gold-600">
                    <span class="material-symbols-outlined">calendar_today</span>
                </div>
                <div>
                    <h3 class="font-title font-bold text-sm text-slate-800">Add New Meeting</h3>
                    <p class="text-[10px] text-slate-400 font-semibold">Schedule a new session for the group</p>
                </div>
            </div>
            <button class="p-1.5 hover:bg-slate-100 rounded-full transition-colors text-slate-400 hover:text-slate-600" onclick="closeModal()">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" id="meeting-form" method="POST" action="{{ route('treasurer.meetings.store') }}">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Date & Time</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">event</span>
                    <input name="meeting_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}"/>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Type</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">category</span>
                    <select name="meeting_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none appearance-none transition-all">
                        <option value="regular">Regular Monthly Meeting</option>
                        <option value="agm">Annual General Meeting (AGM)</option>
                        <option value="special">Special Call Meeting</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Notes / Agenda</label>
                <textarea name="notes" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none resize-none placeholder:text-slate-300" placeholder="Outline the main topics for discussion..." rows="4"></textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition active:scale-95" onclick="closeModal()" type="button">Cancel</button>
                <button class="flex-1 py-2.5 gold-gradient-btn font-bold text-xs rounded-xl shadow-md active:scale-95" type="submit">Save Meeting</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Meeting Modal -->
<div class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="edit-meeting-modal">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <!-- Modal Content -->
    <div class="relative w-full max-w-lg bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-200 translate-y-8 transition-transform duration-500">
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">edit_calendar</span>
                </div>
                <div>
                    <h3 class="font-title font-bold text-sm text-slate-800">Edit / Postpone Meeting</h3>
                    <p class="text-[10px] text-slate-400 font-semibold">Change meeting date or agenda details</p>
                </div>
            </div>
            <button class="p-1.5 hover:bg-slate-100 rounded-full transition-colors text-slate-400 hover:text-slate-600" onclick="closeEditModal()">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" id="edit-meeting-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Date & Time</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">event</span>
                    <input name="meeting_date" id="edit-meeting-date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" type="datetime-local"/>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Type</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">category</span>
                    <select name="meeting_type" id="edit-meeting-type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none appearance-none transition-all">
                        <option value="regular">Regular Monthly Meeting</option>
                        <option value="agm">Annual General Meeting (AGM)</option>
                        <option value="special">Special Call Meeting</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Meeting Notes / Agenda</label>
                <textarea name="notes" id="edit-meeting-notes" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none resize-none placeholder:text-slate-300" placeholder="Outline the main topics..." rows="4"></textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition active:scale-95" onclick="closeEditModal()" type="button">Cancel</button>
                <button class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md active:scale-95" type="submit">Update Meeting</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('meeting-modal');
        const content = modal.querySelector('.relative');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        content.classList.remove('translate-y-8');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('meeting-modal');
        const content = modal.querySelector('.relative');
        modal.classList.add('opacity-0', 'pointer-events-none');
        content.classList.add('translate-y-8');
        document.body.style.overflow = '';
    }

    function openEditModal(meetingId, date, type, notes) {
        const form = document.getElementById('edit-meeting-form');
        form.action = `/treasurer/meetings/${meetingId}`;
        
        document.getElementById('edit-meeting-date').value = date;
        document.getElementById('edit-meeting-type').value = type;
        document.getElementById('edit-meeting-notes').value = notes;

        const modal = document.getElementById('edit-meeting-modal');
        const content = modal.querySelector('.relative');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        content.classList.remove('translate-y-8');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('edit-meeting-modal');
        const content = modal.querySelector('.relative');
        modal.classList.add('opacity-0', 'pointer-events-none');
        content.classList.add('translate-y-8');
        document.body.style.overflow = '';
    }
</script>
@endpush
