@extends('layouts.app')
@section('title', 'Diagnoses')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.diagnoses.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
        <i class="fa-solid fa-plus"></i> New Diagnosis
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">ICD Code</th>
                    <th class="px-4 py-3 text-left">Protocols</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($diagnoses as $diagnosis)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $diagnosis->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $diagnosis->icd_code ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">{{ $diagnosis->protocols_count }} protocols</span>
                    </td>
                    <td class="px-4 py-3 flex items-center gap-3">
                        <a href="{{ route('admin.diagnoses.edit', $diagnosis) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form method="POST" action="{{ route('admin.diagnoses.destroy', $diagnosis) }}" onsubmit="return confirm('Delete this diagnosis?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No diagnoses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $diagnoses->links() }}</div>
</div>
@endsection
