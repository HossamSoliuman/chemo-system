@extends('layouts.app')
@section('title', 'Protocols')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.protocols.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
        <i class="fa-solid fa-plus"></i> New Protocol
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Protocol</th>
                    <th class="px-4 py-3 text-left">Diagnosis</th>
                    <th class="px-4 py-3 text-left">Cycle Duration</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($protocols as $protocol)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $protocol->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $protocol->diagnosis->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $protocol->cycle_duration_days }} days</td>
                    <td class="px-4 py-3 flex items-center gap-3">
                        <a href="{{ route('admin.protocols.edit', $protocol) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form method="POST" action="{{ route('admin.protocols.destroy', $protocol) }}" onsubmit="return confirm('Delete this protocol?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No protocols found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $protocols->links() }}</div>
</div>
@endsection
