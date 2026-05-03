@extends('layouts.app')
@section('title', 'New Chemotherapy Order')

@section('content')
    <div x-data="orderForm()" x-init="init()">

        <div x-show="showCapModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">Lifetime Cap Exceeded</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">The following drugs will exceed their cumulative lifetime dose limits:
                </p>
                <div class="space-y-2 mb-5">
                    <template x-for="w in capWarnings" :key="w.drug.id">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                            <div class="font-semibold text-red-800" x-text="w.drug.name"></div>
                            <div class="text-red-600 text-xs mt-1">
                                Current total: <span x-text="parseFloat(w.current_total).toFixed(2)"></span> |
                                New dose: <span x-text="parseFloat(w.new_dose).toFixed(2)"></span> |
                                Cap: <span x-text="parseFloat(w.cap).toFixed(2)"></span> <span x-text="w.cap_unit"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 mb-5">
                    <i class="fa-solid fa-circle-info mr-1"></i> By confirming, you acknowledge this override and accept
                    clinical responsibility.
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="acknowledgeAndSubmit()"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm py-2 rounded-lg transition font-medium">
                        <i class="fa-solid fa-check mr-1"></i> Acknowledge & Proceed
                    </button>
                    <button type="button" @click="showCapModal = false"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </div>
        </div>

        <div x-show="isProtocolModified" x-cloak
            class="mb-4 flex items-center gap-3 bg-orange-50 border-2 border-orange-400 rounded-xl px-5 py-3">
            <i class="fa-solid fa-triangle-exclamation text-orange-500 text-xl"></i>
            <div>
                <p class="font-bold text-orange-800 uppercase tracking-wide text-sm">Protocol Modified</p>
                <p class="text-xs text-orange-700">One or more drugs have been adjusted from the standard protocol doses,
                    frequencies, or durations.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">1</span>
                Patient Lookup
            </h3>
            <div class="flex gap-3 mb-4">
                <input type="text" x-model="mrnInput" @keydown.enter.prevent="lookupMrn()"
                    placeholder="Enter File Number / MRN..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" @click="lookupMrn()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Lookup
                </button>
                <span x-show="patientNotFound" class="text-red-500 text-sm self-center">
                    <i class="fa-solid fa-circle-xmark mr-1"></i> Patient not found
                </span>
            </div>

            <div x-show="patient" x-cloak>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                    <div class="bg-blue-50 rounded-lg p-3 md:col-span-2">
                        <p class="text-xs text-blue-600 mb-1">Patient Name</p>
                        <p class="font-semibold text-gray-800 text-sm" x-text="patient?.name"></p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5" x-text="patient?.mrn"></p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-blue-600 mb-1">Age / Gender</p>
                        <p class="font-semibold text-gray-800 text-sm" x-text="(patient?.age ?? '—') + ' yrs'"></p>
                        <p class="text-xs text-gray-500 capitalize" x-text="patient?.gender"></p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-blue-600 mb-1">BSA</p>
                        <p class="font-semibold text-gray-800 text-sm" x-text="bsa ? bsa + ' m²' : '—'"></p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-blue-600 mb-1">CrCl</p>
                        <p class="font-semibold text-gray-800 text-sm" x-text="crcl ? crcl + ' mL/min' : '—'"></p>
                    </div>
                </div>

                <div class="border border-blue-200 rounded-lg bg-blue-50/40 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Quick Update Measurements
                        </p>
                        <span x-show="quickSaved" class="text-xs text-green-600 font-medium">
                            <i class="fa-solid fa-circle-check mr-1"></i> Saved & recalculated
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Height (cm)</label>
                            <input type="number" step="0.1" x-model="quickEdit.height_cm"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Weight (kg)</label>
                            <input type="number" step="0.01" x-model="quickEdit.weight_kg"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Serum Creatinine (µmol/L)</label>
                            <input type="number" step="0.01" x-model="quickEdit.serum_creatinine"
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="button" @click="saveQuickEdit()"
                        class="mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1.5 rounded transition">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save & Recalculate
                    </button>
                </div>
                <input type="hidden" name="patient_id" :value="patient?.id">
            </div>

            <div x-show="!patient" class="text-sm text-gray-400 italic">
                No patient selected. Enter file number above or
                <a href="{{ route('patients.create') }}" class="text-blue-600 hover:underline">register a new patient</a>.
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">2</span>
                Diagnosis &amp; Protocol
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Diagnosis</label>
                    <select x-model="diagnosisId" @change="loadProtocols()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select diagnosis</option>
                        @foreach ($diagnoses as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Protocol</label>
                    <select x-model="protocolId" @change="loadDrugTable()" :disabled="!protocols.length"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                        <option value="">Select protocol</option>
                        <template x-for="p in protocols" :key="p.id">
                            <option :value="p.id" x-text="p.name"></option>
                        </template>
                    </select>
                    <input type="hidden" name="protocol_id" :value="protocolId">
                </div>
            </div>

            <div x-show="testsReminder" x-cloak class="mt-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                <p class="text-xs font-semibold text-amber-700 mb-1">
                    <i class="fa-solid fa-flask-vial mr-1"></i> Required Tests / Investigations
                </p>
                <p class="text-sm text-amber-800" x-text="testsReminder"></p>
            </div>

            <div x-show="cycleInfo" x-cloak class="mt-3 flex items-center gap-3 flex-wrap">
                <span class="text-sm text-gray-600">
                    <i class="fa-solid fa-rotate-right mr-1 text-blue-500"></i>
                    Cycle <strong x-text="cycleInfo?.cycle_number"></strong>
                </span>
                <span x-show="cycleInfo?.is_same_cycle"
                    class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                    <i class="fa-solid fa-link mr-1"></i> Same-cycle window
                </span>
            </div>

            <div x-show="drugs.length" x-cloak class="mt-3 flex items-start gap-3 border-t border-gray-100 pt-3">
                <input type="checkbox" id="split_cycle" x-model="isSplitCycle"
                    class="mt-0.5 rounded border-gray-300 text-blue-600">
                <div>
                    <label for="split_cycle" class="text-sm text-gray-700 font-medium cursor-pointer">
                        This is part of a split cycle (e.g. Day 1, 8, 15 of a 21-day cycle)
                    </label>
                    <div x-show="isSplitCycle" class="mt-2 flex items-center gap-2">
                        <label class="text-xs text-gray-500">Day / Week label:</label>
                        <input type="text" x-model="cycleDayWeek" placeholder="e.g. Day 1, Day 8..."
                            class="border border-gray-300 rounded px-2 py-1 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4" x-show="drugs.length" x-cloak>
            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">3</span>
                Dose Modification
            </h3>
            <p class="text-xs text-gray-500 mb-3">
                <i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
                Adjust each drug's <strong>Mod %</strong>, Frequency, Duration, or Final Dose independently. Any change will
                trigger the "Protocol Modified" alert.
            </p>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Reason for Modification (if applicable)</label>
                <input type="text" name="dose_modification_reason"
                    placeholder="e.g. Grade 2 neurotoxicity, renal impairment..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div x-show="drugsByCategory('pre_medication').length" x-cloak
            class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-circle text-xs text-green-500"></i>
                <h3 class="font-semibold text-gray-700 text-sm">Pre-Medications</h3>
                <span class="ml-1 text-xs bg-green-100 text-green-700 rounded-full px-2 py-0.5"
                    x-text="drugsByCategory('pre_medication').length"></span>
            </div>
            <div class="p-4 space-y-3">
                <template x-for="drug in drugsByCategory('pre_medication')" :key="drug.protocol_drug_id">
                    <div class="border border-gray-200 rounded-xl p-4"
                        :class="!drug.is_included ? 'opacity-50 bg-gray-50' : 'bg-white'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="drug.is_included"
                                    class="rounded border-gray-300 text-blue-600">
                                <span class="font-semibold text-gray-800 text-sm"
                                    x-text="drug.drug_name + ' (' + drug.drug_unit + ')'"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-gray-500 mb-1">Dose</label>
                                <input type="number" step="0.01" x-model="drug.final_dose"
                                    @change="drug.is_manually_overridden=true; checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.is_manually_overridden ? 'border-orange-400 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Frequency</label>
                                <input type="text" x-model="drug.physician_frequency" @input="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.physician_frequency && drug.physician_frequency !== drug.frequency ?
                                        'border-orange-300 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Route</label>
                                <span
                                    class="block px-2 py-1.5 text-sm text-gray-600 bg-gray-50 rounded-lg border border-gray-200"
                                    x-text="drug.route || '—'"></span>
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Duration (days)</label>
                                <input type="text" x-model="drug.physician_duration" @input="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.physician_duration && drug.physician_duration !== drug.duration_days ?
                                        'border-orange-300 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Unit</label>
                                <select x-model="drug.physician_dose_unit" @change="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">Default</option>
                                    <option value="mg">mg</option>
                                    <option value="mcg">mcg</option>
                                    <option value="g">g</option>
                                    <option value="mL">mL</option>
                                    <option value="IU">IU</option>
                                    <option value="mg/m²">mg/m²</option>
                                    <option value="mg/kg">mg/kg</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-semibold text-green-700 mb-1">
                                <i class="fa-solid fa-notes-medical mr-1"></i> Note / Instructions
                            </label>
                            <textarea x-model="drug.physician_note" @input="checkModified()" rows="2"
                                class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"
                                :class="drug.physician_note ? 'border-blue-300 bg-blue-50' : 'border-gray-300 bg-white'"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="drugsByCategory('chemotherapy').length" x-cloak
            class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-circle text-xs text-red-500"></i>
                <h3 class="font-semibold text-gray-700 text-sm">Chemotherapy</h3>
                <span class="ml-1 text-xs bg-red-100 text-red-700 rounded-full px-2 py-0.5"
                    x-text="drugsByCategory('chemotherapy').length"></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-400 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left w-8">Inc.</th>
                            <th class="px-3 py-2 text-left">Drug</th>
                            <th class="px-3 py-2 text-left">Dose/Unit</th>
                            <th class="px-3 py-2 text-left">Calc.</th>
                            <th class="px-3 py-2 text-left w-16">Mod %</th>
                            <th class="px-3 py-2 text-left">Final Dose</th>
                            <th class="px-3 py-2 text-left">Route</th>
                            <th class="px-3 py-2 text-left">Frequency</th>
                            <th class="px-3 py-2 text-left">Duration</th>
                            <th class="px-3 py-2 text-left">Flags</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="drug in drugsByCategory('chemotherapy')" :key="drug.protocol_drug_id">
                            <tr class="border-t border-gray-100"
                                :class="!drug.is_included ? 'opacity-50 bg-gray-50' : ''">
                                <td class="px-3 py-2">
                                    <input type="checkbox" x-model="drug.is_included"
                                        class="rounded border-gray-300 text-blue-600">
                                </td>
                                <td class="px-3 py-2 font-medium text-gray-800" x-text="drug.drug_name"></td>
                                <td class="px-3 py-2 text-blue-600 font-mono">
                                    <span x-show="drug.dose_type==='carboplatin_calvert'"
                                        x-text="'AUC '+drug.target_auc"></span>
                                    <span x-show="drug.dose_type!=='carboplatin_calvert' && drug.dose_type!=='fixed'"
                                        x-text="drug.dose_label || (drug.dose_per_unit ? parseFloat(drug.dose_per_unit).toFixed(2)+' '+doseUnitLabel(drug.dose_type) : '—')"></span>
                                    <span x-show="drug.dose_type==='fixed'">Fixed</span>
                                </td>
                                <td class="px-3 py-2 font-mono text-gray-500"
                                    x-text="parseFloat(drug.base_dose).toFixed(2)"></td>
                                <td class="px-3 py-2">
                                    <template x-if="drug.dose_type === 'fixed'">
                                        <span class="text-gray-400 italic">—</span>
                                    </template>
                                    <template x-if="drug.dose_type !== 'fixed'">
                                        <div class="flex items-center gap-0.5">
                                            <input type="number" x-model="drug.modification_pct"
                                                @input="applyDrugModification(drug); checkModified()" min="1"
                                                max="200" step="5"
                                                class="w-12 border rounded px-1 py-0.5 font-mono text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                :class="drug.modification_pct != 100 ?
                                                    'border-orange-400 bg-orange-50 text-orange-700 font-bold' :
                                                    'border-gray-300'">
                                            <span class="text-gray-400">%</span>
                                        </div>
                                    </template>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-col gap-0.5">
                                        <input type="number" step="0.01" x-model="drug.final_dose"
                                            @change="drug.is_manually_overridden=true; drug.modification_pct=100; checkModified()"
                                            class="w-24 border rounded px-1.5 py-1 font-mono text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="drug.is_manually_overridden ? 'border-orange-400 bg-orange-50' : (drug
                                                .modification_pct != 100 ? 'border-yellow-300 bg-yellow-50' :
                                                'border-blue-300 bg-blue-50')">
                                        <span class="text-xs text-gray-400 text-center" x-text="drug.drug_unit"></span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-gray-500" x-text="drug.route || '—'"></td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="drug.physician_frequency" @input="checkModified()"
                                        class="w-28 border rounded px-1.5 py-0.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="drug.physician_frequency && drug.physician_frequency !== drug.frequency ?
                                            'border-orange-300 bg-orange-50' : 'border-gray-300'">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="drug.physician_duration" @input="checkModified()"
                                        class="w-16 border rounded px-1.5 py-0.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="drug.physician_duration && drug.physician_duration !== drug.duration_days ?
                                            'border-orange-300 bg-orange-50' : 'border-gray-300'">
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span x-show="drug.cap_applied"
                                            class="text-xs bg-yellow-100 text-yellow-700 px-1 py-0.5 rounded">
                                            <i class="fa-solid fa-circle-minus"></i>
                                        </span>
                                        <span x-show="drug.modification_pct != 100 && !drug.is_manually_overridden"
                                            class="text-xs bg-orange-100 text-orange-700 px-1 py-0.5 rounded"
                                            x-text="drug.modification_pct + '%'"></span>
                                        <span x-show="drug.is_manually_overridden"
                                            class="text-xs bg-red-100 text-red-700 px-1 py-0.5 rounded">
                                            <i class="fa-solid fa-pen"></i>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-gray-100">
                <template x-for="drug in drugsByCategory('chemotherapy')" :key="'note-' + drug.protocol_drug_id">
                    <div :class="!drug.is_included ? 'opacity-50' : 'bg-blue-50/20'">
                        <div class="flex items-start gap-2 px-3 py-2 pb-3">
                            <span class="text-xs font-semibold text-blue-600 mt-1.5 whitespace-nowrap">
                                <i class="fa-solid fa-notes-medical mr-1"></i>
                                <span x-text="drug.drug_name"></span> Note:
                            </span>
                            <textarea x-model="drug.physician_note" @input="checkModified()" rows="2"
                                placeholder="Administration instructions (e.g. IV in 500ml D5W over 90 minutes)..."
                                class="w-full border rounded-lg px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400 resize-none"
                                :class="drug.physician_note ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-white'"></textarea>
                        </div>
                        <div x-show="drug.is_manually_overridden" class="px-3 pb-2">
                            <input type="text" x-model="drug.override_reason"
                                placeholder="Reason for manual dose override (required)"
                                class="w-full border border-orange-300 rounded px-2 py-1 text-xs bg-orange-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="drugsByCategory('post_medication').length" x-cloak
            class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-circle text-xs text-blue-500"></i>
                <h3 class="font-semibold text-gray-700 text-sm">Post-Medications</h3>
                <span class="ml-1 text-xs bg-blue-100 text-blue-700 rounded-full px-2 py-0.5"
                    x-text="drugsByCategory('post_medication').length"></span>
            </div>
            <div class="p-4 space-y-3">
                <template x-for="drug in drugsByCategory('post_medication')" :key="drug.protocol_drug_id">
                    <div class="border border-gray-200 rounded-xl p-4"
                        :class="!drug.is_included ? 'opacity-50 bg-gray-50' : 'bg-white'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="drug.is_included"
                                    class="rounded border-gray-300 text-blue-600">
                                <span class="font-semibold text-gray-800 text-sm"
                                    x-text="drug.drug_name + ' (' + drug.drug_unit + ')'"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-gray-500 mb-1">Dose</label>
                                <input type="number" step="0.01" x-model="drug.final_dose"
                                    @change="drug.is_manually_overridden=true; checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.is_manually_overridden ? 'border-orange-400 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Frequency</label>
                                <input type="text" x-model="drug.physician_frequency" @input="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.physician_frequency && drug.physician_frequency !== drug.frequency ?
                                        'border-orange-300 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Route</label>
                                <span
                                    class="block px-2 py-1.5 text-sm text-gray-600 bg-gray-50 rounded-lg border border-gray-200"
                                    x-text="drug.route || '—'"></span>
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Duration (days)</label>
                                <input type="text" x-model="drug.physician_duration" @input="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="drug.physician_duration && drug.physician_duration !== drug.duration_days ?
                                        'border-orange-300 bg-orange-50' : ''">
                            </div>
                            <div>
                                <label class="block text-gray-500 mb-1">Unit</label>
                                <select x-model="drug.physician_dose_unit" @change="checkModified()"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">Default</option>
                                    <option value="mg">mg</option>
                                    <option value="mcg">mcg</option>
                                    <option value="g">g</option>
                                    <option value="mL">mL</option>
                                    <option value="IU">IU</option>
                                    <option value="mg/m²">mg/m²</option>
                                    <option value="mg/kg">mg/kg</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-semibold text-blue-600 mb-1">
                                <i class="fa-solid fa-notes-medical mr-1"></i> Note / Instructions
                            </label>
                            <textarea x-model="drug.physician_note" @input="checkModified()" rows="2"
                                class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"
                                :class="drug.physician_note ? 'border-blue-300 bg-blue-50' : 'border-gray-300 bg-white'"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4" x-show="drugs.length" x-cloak>
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">4</span>
                Clinician Information
            </h3>
            <div class="grid grid-cols-3 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Consultant Physician</label>
                    <input type="text" name="consultant_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Clinical Pharmacist</label>
                    <input type="text" name="pharmacist_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nurse</label>
                    <input type="text" name="nurse_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Clinical Notes / Allergies</label>
                <textarea name="notes" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>

        <div x-show="drugs.length" x-cloak class="flex gap-3">
            <button type="button" @click="submitOrder()" :disabled="submitting"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg transition font-medium disabled:opacity-50">
                <i class="fa-solid fa-floppy-disk mr-1"></i>
                <span x-text="submitting ? 'Saving...' : 'Save as Draft'"></span>
            </button>
            <a href="{{ route('orders.index') }}"
                class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2.5 rounded-lg border border-gray-200 transition">Cancel</a>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function orderForm() {
            return {
                mrnInput: '{{ request('mrn', '') }}',
                patient: null,
                patientNotFound: false,
                quickEdit: {
                    height_cm: '',
                    weight_kg: '',
                    serum_creatinine: ''
                },
                quickSaved: false,
                diagnosisId: '',
                protocolId: '',
                protocols: [],
                drugs: [],
                cycleInfo: null,
                bsa: null,
                crcl: null,
                testsReminder: null,
                isSplitCycle: false,
                cycleDayWeek: '',
                isProtocolModified: false,
                showCapModal: false,
                capWarnings: [],
                submitting: false,
                _pendingPayload: null,

                init() {},

                async lookupMrn() {
                    this.patientNotFound = false;
                    const res = await fetch(`/api/patients/mrn/${encodeURIComponent(this.mrnInput)}`);
                    if (!res.ok) {
                        this.patient = null;
                        this.patientNotFound = true;
                        return;
                    }
                    const data = await res.json();
                    if (data.found) {
                        this.patient = data.patient;
                        this.quickEdit.height_cm = data.patient.height_cm;
                        this.quickEdit.weight_kg = data.patient.weight_kg;
                        this.quickEdit.serum_creatinine = data.patient.serum_creatinine;
                        if (this.protocolId) this.loadDrugTable();
                    } else {
                        this.patient = null;
                        this.patientNotFound = true;
                    }
                },

                async saveQuickEdit() {
                    if (!this.patient) return;
                    const res = await fetch(`/api/patients/${this.patient.id}/quick-update`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.quickEdit),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.patient = {
                            ...this.patient,
                            ...data.patient
                        };
                        this.bsa = data.bsa;
                        this.crcl = data.crcl;
                        this.quickSaved = true;
                        setTimeout(() => this.quickSaved = false, 3000);
                        if (this.protocolId) this.loadDrugTable();
                    }
                },

                async loadProtocols() {
                    this.protocolId = '';
                    this.drugs = [];
                    this.cycleInfo = null;
                    this.testsReminder = null;
                    if (!this.diagnosisId) {
                        this.protocols = [];
                        return;
                    }
                    const res = await fetch(`/api/protocols?diagnosis_id=${this.diagnosisId}`);
                    this.protocols = await res.json();
                },

                async loadDrugTable() {
                    if (!this.patient || !this.protocolId) return;
                    const selectedProtocol = this.protocols.find(p => p.id == this.protocolId);
                    this.testsReminder = selectedProtocol?.tests_reminder || null;
                    const res = await fetch('/api/orders/calculate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            patient_id: this.patient.id,
                            protocol_id: this.protocolId
                        }),
                    });
                    const data = await res.json();
                    this.bsa = data.bsa;
                    this.crcl = data.crcl;
                    this.cycleInfo = data.cycle_info;
                    this.isProtocolModified = false;
                    this.drugs = data.drugs.map(d => ({
                        ...d,
                        base_dose: d.calculated_dose,
                        final_dose: d.final_dose,
                        dose_label: d.dose_label || '',
                        modification_pct: 100,
                        is_included: true,
                        is_manually_overridden: false,
                        override_reason: '',
                        physician_note: d.notes || '',
                        physician_frequency: d.frequency || '',
                        physician_duration: d.duration_days || '',
                        physician_dose_unit: '',
                    }));
                },

                checkModified() {
                    this.isProtocolModified = this.drugs.some(d =>
                        d.is_manually_overridden ||
                        d.modification_pct != 100 ||
                        (d.physician_frequency && d.physician_frequency !== d.frequency) ||
                        (d.physician_duration && d.physician_duration !== d.duration_days)
                    );
                },

                applyDrugModification(drug) {
                    if (drug.dose_type === 'fixed') return;
                    const pct = parseFloat(drug.modification_pct);
                    if (isNaN(pct)) return;
                    drug.is_manually_overridden = false;
                    drug.override_reason = '';
                    let newFinal = parseFloat((drug.base_dose * (pct / 100)).toFixed(2));
                    if (drug.per_cycle_cap && newFinal > drug.per_cycle_cap) {
                        newFinal = drug.per_cycle_cap;
                        drug.cap_applied = true;
                    }
                    drug.final_dose = newFinal;
                },

                drugsByCategory(cat) {
                    return this.drugs.filter(d => d.category === cat);
                },

                doseUnitLabel(dt) {
                    return {
                        bsa_based: 'mg/m²',
                        weight_based: 'mg/kg',
                        crcl_based: 'mg/mL/min'
                    } [dt] || '';
                },

                async submitOrder() {
                    if (!this.patient || !this.protocolId) {
                        alert('Please select a patient and protocol.');
                        return;
                    }
                    this.submitting = true;
                    const payload = {
                        patient_id: this.patient.id,
                        protocol_id: this.protocolId,
                        is_split_cycle: this.isSplitCycle ? 1 : 0,
                        cycle_day_week: this.isSplitCycle ? this.cycleDayWeek : null,
                        dose_modification_reason: document.querySelector('[name=dose_modification_reason]')?.value,
                        consultant_name: document.querySelector('[name=consultant_name]')?.value,
                        pharmacist_name: document.querySelector('[name=pharmacist_name]')?.value,
                        nurse_name: document.querySelector('[name=nurse_name]')?.value,
                        notes: document.querySelector('[name=notes]')?.value,
                        drugs: this.drugs.map(d => ({
                            protocol_drug_id: d.protocol_drug_id,
                            final_dose: d.final_dose,
                            modification_pct: d.modification_pct,
                            is_included: d.is_included ? 1 : 0,
                            is_manually_overridden: d.is_manually_overridden ? 1 : 0,
                            override_reason: d.override_reason || '',
                            physician_note: d.physician_note || '',
                            physician_frequency: d.physician_frequency || '',
                            physician_duration: d.physician_duration || '',
                            physician_dose_unit: d.physician_dose_unit || '',
                        })),
                    };
                    const res = await fetch('/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    this.submitting = false;
                    if (data.requires_acknowledgment) {
                        this.capWarnings = data.lifetime_warnings;
                        this.showCapModal = true;
                        this._pendingPayload = payload;
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                },

                async acknowledgeAndSubmit() {
                    this.showCapModal = false;
                    this.submitting = true;
                    const payload = {
                        ...this._pendingPayload,
                        lifetime_cap_acknowledged: true
                    };
                    const res = await fetch('/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    this.submitting = false;
                    if (data.redirect) window.location.href = data.redirect;
                },
            };
        }
    </script>
@endpush
