@extends('layouts.app')
@section('title', 'New Patient')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">New Patient Registration</h2>
        <form method="POST" action="{{ route('patients.store') }}" class="space-y-5">
            @csrf

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Demographics</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">File Number (MRN) <span class="text-red-500">*</span></label>
                        <input type="text" name="mrn" value="{{ old('mrn') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Saudi, Egyptian..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Consultant In-Charge</label>
                        <input type="text" name="consultant_in_charge" value="{{ old('consultant_in_charge') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Measurements</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Height (cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" name="height_cm" value="{{ old('height_cm') }}" required min="50" max="250" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="weight_kg" value="{{ old('weight_kg') }}" required min="1" max="500" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Serum Creatinine (µmol/L) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="serum_creatinine" value="{{ old('serum_creatinine') }}" required min="10" max="2000" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-100 pb-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Clinical Details</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Pregnant</label>
                        <select name="pregnant" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="na" {{ old('pregnant','na')==='na'?'selected':'' }}>N/A</option>
                            <option value="yes" {{ old('pregnant')==='yes'?'selected':'' }}>Yes</option>
                            <option value="no" {{ old('pregnant')==='no'?'selected':'' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Lactating</label>
                        <select name="lactating" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="na" {{ old('lactating','na')==='na'?'selected':'' }}>N/A</option>
                            <option value="yes" {{ old('lactating')==='yes'?'selected':'' }}>Yes</option>
                            <option value="no" {{ old('lactating')==='no'?'selected':'' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cancer Stage</label>
                        <select name="cancer_stage" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Unknown</option>
                            <option value="0" {{ old('cancer_stage')==='0'?'selected':'' }}>Stage 0</option>
                            <option value="I" {{ old('cancer_stage')==='I'?'selected':'' }}>Stage I</option>
                            <option value="II" {{ old('cancer_stage')==='II'?'selected':'' }}>Stage II</option>
                            <option value="III" {{ old('cancer_stage')==='III'?'selected':'' }}>Stage III</option>
                            <option value="IV" {{ old('cancer_stage')==='IV'?'selected':'' }}>Stage IV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ECOG Performance Status</label>
                        <select name="ecog_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Not recorded</option>
                            <option value="0" {{ old('ecog_status')==='0'?'selected':'' }}>0 — Fully active</option>
                            <option value="1" {{ old('ecog_status')==='1'?'selected':'' }}>1 — Restricted but ambulatory</option>
                            <option value="2" {{ old('ecog_status')==='2'?'selected':'' }}>2 — Ambulatory, self-care, no work</option>
                            <option value="3" {{ old('ecog_status')==='3'?'selected':'' }}>3 — Limited self-care</option>
                            <option value="4" {{ old('ecog_status')==='4'?'selected':'' }}>4 — Completely disabled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Setting of Chemotherapy</label>
                        <select name="chemo_setting" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Not specified</option>
                            <option value="Palliative" {{ old('chemo_setting')==='Palliative'?'selected':'' }}>Palliative</option>
                            <option value="Neoadjuvant" {{ old('chemo_setting')==='Neoadjuvant'?'selected':'' }}>Neoadjuvant</option>
                            <option value="Adjuvant" {{ old('chemo_setting')==='Adjuvant'?'selected':'' }}>Adjuvant</option>
                            <option value="Maintenance" {{ old('chemo_setting')==='Maintenance'?'selected':'' }}>Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>

            <div x-data="{ hasAllergy: {{ old('has_allergy') ? 'true' : 'false' }} }">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-3">Allergies</p>
                <div class="flex items-center gap-4 mb-3">
                    <label class="text-sm font-medium text-gray-600">Allergy:</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_allergy" value="1" @click="hasAllergy=true" {{ old('has_allergy')=='1'?'checked':'' }} class="text-blue-600"> Yes
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="has_allergy" value="0" @click="hasAllergy=false" {{ old('has_allergy','0')=='0'?'checked':'' }} class="text-blue-600"> No
                    </label>
                </div>
                <div x-show="hasAllergy" class="mt-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Specify Allergy</label>
                    <input type="text" name="allergy_details" value="{{ old('allergy_details') }}" placeholder="e.g. Penicillin, Contrast dye..." class="w-full border border-orange-300 bg-orange-50 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg transition">Save Patient</button>
                <a href="{{ route('patients.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2 rounded-lg border border-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
