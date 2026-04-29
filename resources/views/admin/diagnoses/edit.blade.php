@extends('layouts.app')
@section('title', 'Edit Diagnosis')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Edit Diagnosis</h2>
        <form method="POST" action="{{ route('admin.diagnoses.update', $diagnosis) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Diagnosis Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $diagnosis->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">ICD-10 Code</label>
                <input type="text" name="icd_code" value="{{ old('icd_code', $diagnosis->icd_code) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $diagnosis->description) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition">Update</button>
                <a href="{{ route('admin.diagnoses.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2 rounded-lg border border-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
