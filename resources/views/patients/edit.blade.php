@extends('layouts.app')
@section('title', 'Edit Patient')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-5">Edit Patient — {{ $patient->mrn }}</h2>
            <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">MRN <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="mrn" value="{{ old('mrn', $patient->mrn) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $patient->name) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Height (cm)</label>
                        <input type="number" step="0.1" name="height_cm"
                            value="{{ old('height_cm', $patient->height_cm) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight_kg"
                            value="{{ old('weight_kg', $patient->weight_kg) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Serum Creatinine (µmol/L)</label>
                        <input type="number" step="0.01" name="serum_creatinine"
                            value="{{ old('serum_creatinine', $patient->serum_creatinine) }}" required min="10"
                            max="2000"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition">Update
                        Patient</button>
                    <a href="{{ route('patients.show', $patient) }}"
                        class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2 rounded-lg border border-gray-200 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
