@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Users</h2>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fa-solid fa-plus mr-2"></i> Add User
        </a>
    </div>

    @if ($users->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <i class="fa-solid fa-users text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No users found. Create one to get started.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fa-solid fa-edit mr-1"></i> Edit
                                </a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                            <i class="fa-solid fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
