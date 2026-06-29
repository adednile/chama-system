@extends('layouts.app')
@section('title', 'Group Financial Reports')

@section('content')
<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-medium">
        <div class="premium-card rounded-2xl p-6 text-center border-t-4 border-blue-500 relative overflow-hidden">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Total Savings Contributions</span>
            <span class="text-2xl font-title font-black text-slate-800">Ksh {{ number_format($contributions->sum('amount'), 2) }}</span>
        </div>
        <div class="premium-card rounded-2xl p-6 text-center border-t-4 border-gold-500 relative overflow-hidden">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Total Loan Capital Disbursed</span>
            <span class="text-2xl font-title font-black text-gold-600">Ksh {{ number_format($loans->sum('amount'), 2) }}</span>
        </div>
        <div class="premium-card rounded-2xl p-6 text-center border-t-4 border-rose-500 relative overflow-hidden">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Total Late Fines Collected</span>
            <span class="text-2xl font-title font-black text-rose-700">Ksh {{ number_format($fines->where('status', 'paid')->sum('amount'), 2) }}</span>
        </div>
    </div>

    {{-- Member Summary --}}
    <div class="premium-card rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-sm font-bold font-title text-slate-800">Chama Group Member Account Balance</h3>
            <a href="{{ route('reports.treasurer') }}?download=pdf" class="gold-gradient-btn px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <span class="material-symbols-outlined text-xs font-bold">picture_as_pdf</span> Download PDF Sheet
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Chama Member</th>
                        <th class="px-6 py-4">Contributions</th>
                        <th class="px-6 py-4">Loans Taken</th>
                        <th class="px-6 py-4">Late Fines Assessed</th>
                        <th class="px-6 py-4 text-right">Net Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($users ?? [] as $user)
                    @php
                        $userContributions = $contributions->where('user_id', $user->id)->sum('amount');
                        $userLoans = $loans->where('user_id', $user->id)->sum('amount');
                        $userFines = $fines->where('user_id', $user->id)->sum('amount');
                        $netBalance = $userContributions - $userLoans;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition font-medium">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4">Ksh {{ number_format($userContributions, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500">Ksh {{ number_format($userLoans, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500 font-medium">Ksh {{ number_format($userFines, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-gold-600">
                            Ksh {{ number_format($netBalance, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 opacity-50">assessment</span>
                            No members assigned to this Chama.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection