@extends('layouts.app')
@section('title', 'Patients')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or MRN..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm px-4 py-2 rounded-lg transition"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
        <i class="fa-solid fa-user-plus"></i> New Patient
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">MRN</th>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Gender</th>
                    <th class="px-4 py-3 text-left">Age</th>
                    <th class="px-4 py-3 text-left">DOB</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-medium text-blue-700">{{ $patient->mrn }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $patient->name }}</td>
                    <td class="px-4 py-3 text-gray-500 capitalize">{{ $patient->gender }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $patient->age }} yrs</td>
                    <td class="px-4 py-3 text-gray-500">{{ $patient->date_of_birth->format('d M Y') }}</td>
                    <td class="px-4 py-3 flex items-center gap-3">
                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('patients.edit', $patient) }}" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="{{ route('orders.create') }}?patient_id={{ $patient->id }}" class="text-green-600 hover:text-green-800" title="New Order"><i class="fa-solid fa-file-medical"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No patients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $patients->links() }}</div>
</div>
@endsection
