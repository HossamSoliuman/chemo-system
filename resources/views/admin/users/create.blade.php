@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                    placeholder="john@example.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fa-solid fa-save mr-2"></i> Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
