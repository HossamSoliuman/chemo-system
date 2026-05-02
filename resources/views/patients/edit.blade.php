@extends('layouts.app')
@section('title', 'Edit Patient')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Edit Patient — {{ $patient->mrn }}</h2>
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Demographics</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">File Number (MRN) <span class="text-red-500">*</span></label>
                        <input type="text" name="mrn" value="{{ old('mrn', $patient->mrn) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $patient->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="male" {{ $patient->gender==='male'?'selected':'' }}>Male</option>
                            <option value="female" {{ $patient->gender==='female'?'selected':'' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $patient->nationality) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Consultant In-Charge</label>
                        <input type="text" name="consultant_in_charge" value="{{ old('consultant_in_charge', $patient->consultant_in_charge) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Measurements</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Height (cm)</label>
                        <input type="number" step="0.1" name="height_cm" value="{{ old('height_cm', $patient->height_cm) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight_kg" value="{{ old('weight_kg', $patient->weight_kg) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Serum Creatinine (µmol/L)</label>
                        <input type="number" step="0.01" name="serum_creatinine" value="{{ old('serum_creatinine', $patient->serum_creatinine) }}" required min="10" max="2000" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Clinical Details</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Pregnant</label>
                        <select name="pregnant" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="na" {{ ($patient->pregnant??'na')==='na'?'selected':'' }}>N/A</option>
                            <option value="yes" {{ $patient->pregnant==='yes'?'selected':'' }}>Yes</option>
                            <option value="no" {{ $patient->pregnant==='no'?'selected':'' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lactating</label>
                        <select name="lactating" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="na" {{ ($patient->lactating??'na')==='na'?'selected':'' }}>N/A</option>
                            <option value="yes" {{ $patient->lactating==='yes'?'selected':'' }}>Yes</option>
                            <option value="no" {{ $patient->lactating==='no'?'selected':'' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cancer Stage</label>
                        <select name="cancer_stage" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Unknown</option>
                            @foreach(['0','I','II','III','IV'] as $st)
                            <option value="{{ $st }}" {{ $patient->cancer_stage===$st?'selected':'' }}>Stage {{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ECOG Performance Status</label>
                        <select name="ecog_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Not recorded</option>
                            <option value="0" {{ $patient->ecog_status==='0'?'selected':'' }}>0 — Fully active</option>
                            <option value="1" {{ $patient->ecog_status==='1'?'selected':'' }}>1 — Restricted but ambulatory</option>
                            <option value="2" {{ $patient->ecog_status==='2'?'selected':'' }}>2 — Ambulatory, self-care</option>
                            <option value="3" {{ $patient->ecog_status==='3'?'selected':'' }}>3 — Limited self-care</option>
                            <option value="4" {{ $patient->ecog_status==='4'?'selected':'' }}>4 — Completely disabled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Setting of Chemotherapy</label>
                        <select name="chemo_setting" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Not specified</option>
                            @foreach(['Palliative','Neoadjuvant','Adjuvant','Maintenance'] as $s)
                            <option value="{{ $s }}" {{ $patient->chemo_setting===$s?'selected':'' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div x-data="{ hasAllergy: {{ $patient->has_allergy ? 'true' : 'false' }} }">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Allergies</p>
                <div class="flex items-center gap-4 mb-3">
                    <label class="text-sm font-medium text-gray-600">Allergy:</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_allergy" value="1" @click="hasAllergy=true" {{ $patient->has_allergy?'checked':'' }} class="text-blue-600"> Yes
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_allergy" value="0" @click="hasAllergy=false" {{ !$patient->has_allergy?'checked':'' }} class="text-blue-600"> No
                    </label>
                </div>
                <div x-show="hasAllergy">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Specify Allergy</label>
                    <input type="text" name="allergy_details" value="{{ old('allergy_details', $patient->allergy_details) }}" class="w-full border border-orange-300 bg-orange-50 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition">Update Patient</button>
                <a href="{{ route('patients.show', $patient) }}" class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2 rounded-lg border border-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
