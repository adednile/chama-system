<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Treasurer Dashboard</h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Pending Loans</p>
                    <p class="text-2xl font-semibold">{{ \App\Models\Loan::where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Pending Fines</p>
                    <p class="text-2xl font-semibold">{{ \App\Models\Fine::where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Members</p>
                    <p class="text-2xl font-semibold">{{ \App\Models\User::where('role', 'member')->count() }}</p>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Quick actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('treasurer.sms-parser') }}" class="bg-digital-blue-600 hover:bg-digital-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm">Parse SMS</a>
                    <a href="{{ route('treasurer.penalties') }}" class="bg-digital-blue-600 hover:bg-digital-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm">Manage fines</a>
                    <a href="{{ route('reports.treasurer') }}" class="bg-digital-blue-600 hover:bg-digital-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm">Reports</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
