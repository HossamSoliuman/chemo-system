@extends('layouts.app')
@section('title', 'Edit Protocol')

@section('content')
@php
$existingDrugs = $protocol->protocolDrugs->map(function($pd, $i) {
    return [
        '_key' => $i,
        'drug_id' => $pd->drug_id,
        'drug_label' => $pd->drug->name . ' (' . $pd->drug->unit . ')',
        'category' => $pd->category,
        'dose_type' => $pd->dose_type,
        'dose_per_unit' => $pd->dose_per_unit,
        'dose_label' => $pd->dose_label,
        'fixed_dose' => $pd->fixed_dose,
        'target_auc' => $pd->target_auc,
        'per_cycle_cap' => $pd->per_cycle_cap,
        'per_cycle_cap_unit' => $pd->per_cycle_cap_unit,
        'lifetime_cap' => $pd->lifetime_cap,
        'lifetime_cap_unit' => $pd->lifetime_cap_unit,
        'route' => $pd->route,
        'frequency' => $pd->frequency,
        'duration_days' => $pd->duration_days,
        'notes' => $pd->notes,
    ];
})->values()->toArray();
@endphp

<div x-data="protocolBuilder({{ json_encode($existingDrugs) }})" x-init="init()">
    <form method="POST" action="{{ route('admin.protocols.update', $protocol) }}">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Protocol Info</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Protocol Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $protocol->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Diagnosis <span class="text-red-500">*</span></label>
                            <select name="diagnosis_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($diagnoses as $d)
                                    <option value="{{ $d->id }}" {{ $d->id == $protocol->diagnosis_id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cycle Duration (days)</label>
                            <input type="number" name="cycle_duration_days" value="{{ old('cycle_duration_days', $protocol->cycle_duration_days) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                            <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $protocol->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                <i class="fa-solid fa-flask-vial mr-1 text-amber-500"></i>
                                Required Tests / Investigations
                                <span class="ml-1 text-gray-400 font-normal">(internal — not printed)</span>
                            </label>
                            <textarea name="tests_reminder" rows="2" placeholder="e.g. CBC & diff, platelets, creatinine & LFT before each cycle." class="w-full border border-amber-200 bg-amber-50 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old('tests_reminder', $protocol->tests_reminder) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 mb-3">Add Drug</h3>
                    <div class="space-y-2">
                        <select x-model="newDrug.drug_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select drug</option>
                            @foreach($drugs as $drug)
                                <option value="{{ $drug->id }}">{{ $drug->name }} ({{ $drug->unit }})</option>
                            @endforeach
                        </select>
                        <select x-model="newDrug.category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pre_medication">Pre-Medication</option>
                            <option value="chemotherapy" selected>Chemotherapy</option>
                            <option value="post_medication">Post-Medication</option>
                        </select>
                        <button type="button" @click="addDrug()" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-2 rounded-lg transition">
                            <i class="fa-solid fa-plus mr-1"></i> Add to Protocol
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 flex">
                        <button type="button" @click="tab='pre_medication'" :class="tab==='pre_medication'?'border-b-2 border-blue-600 text-blue-600':'text-gray-500'" class="px-5 py-3 text-sm font-medium">Pre-Meds <span class="ml-1 text-xs bg-gray-100 rounded-full px-2 py-0.5" x-text="drugsByCategory('pre_medication').length"></span></button>
                        <button type="button" @click="tab='chemotherapy'" :class="tab==='chemotherapy'?'border-b-2 border-blue-600 text-blue-600':'text-gray-500'" class="px-5 py-3 text-sm font-medium">Chemo <span class="ml-1 text-xs bg-gray-100 rounded-full px-2 py-0.5" x-text="drugsByCategory('chemotherapy').length"></span></button>
                        <button type="button" @click="tab='post_medication'" :class="tab==='post_medication'?'border-b-2 border-blue-600 text-blue-600':'text-gray-500'" class="px-5 py-3 text-sm font-medium">Post-Meds <span class="ml-1 text-xs bg-gray-100 rounded-full px-2 py-0.5" x-text="drugsByCategory('post_medication').length"></span></button>
                    </div>

                    <div class="p-4">
                        <template x-if="drugsByCategory(tab).length === 0">
                            <div class="text-center py-10 text-gray-400 text-sm"><i class="fa-solid fa-capsules text-3xl mb-3 block opacity-30"></i>No drugs in this category.</div>
                        </template>
                        <template x-for="drug in drugsByCategory(tab)" :key="drug._key">
                            <div class="border border-gray-200 rounded-lg p-4 mb-3 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-medium text-sm text-gray-800" x-text="drug.drug_label"></span>
                                    <button type="button" @click="removeDrug(drug._key)" class="text-red-500 hover:text-red-700 text-xs"><i class="fa-solid fa-xmark"></i> Remove</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <label class="block text-gray-500 mb-1">Dose Type</label>
                                        <select x-model="drug.dose_type" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <option value="bsa_based">BSA-based</option>
                                            <option value="weight_based">Weight-based</option>
                                            <option value="crcl_based">CrCl-based</option>
                                            <option value="carboplatin_calvert">Carboplatin Calvert</option>
                                            <option value="fixed">Fixed</option>
                                        </select>
                                    </div>
                                    <div x-show="drug.dose_type !== 'fixed' && drug.dose_type !== 'carboplatin_calvert'">
                                        <label class="block text-gray-500 mb-1">Dose per Unit</label>
                                        <input type="number" step="0.0001" x-model="drug.dose_per_unit" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div x-show="drug.dose_type !== 'fixed' && drug.dose_type !== 'carboplatin_calvert'">
                                        <label class="block text-gray-500 mb-1">Dose Label Override</label>
                                        <input type="text" x-model="drug.dose_label" placeholder="e.g. 180 mg/m²" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div x-show="drug.dose_type === 'fixed'">
                                        <label class="block text-gray-500 mb-1">Fixed Dose</label>
                                        <input type="number" step="0.0001" x-model="drug.fixed_dose" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div x-show="drug.dose_type === 'carboplatin_calvert'">
                                        <label class="block text-gray-500 mb-1">Target AUC</label>
                                        <input type="number" step="0.01" x-model="drug.target_auc" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Route</label>
                                        <select x-model="drug.route" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                            <option value="">Select route</option>
                                            <option value="Oral">Oral</option>
                                            <option value="Sublingual">Sublingual</option>
                                            <option value="Continuous IV infusion">Continuous IV infusion</option>
                                            <option value="IV bolus">Intravenous bolus</option>
                                            <option value="IV push">Intravenous push</option>
                                            <option value="SC">Subcutaneous</option>
                                            <option value="IM">Intramuscular</option>
                                            <option value="Intrathecal">Intrathecal</option>
                                            <option value="Intrapleural">Intrapleural</option>
                                            <option value="Intravesical">Intravesical</option>
                                            <option value="Intraperitoneal">Intraperitoneal</option>
                                            <option value="Intraarterial">Intraarterial</option>
                                            <option value="Topical">Topical</option>
                                            <option value="Intraventricular">Intraventricular</option>
                                            <option value="Intravitreal">Intravitreal</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Frequency</label>
                                        <input type="text" x-model="drug.frequency" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Per-Cycle Cap</label>
                                        <input type="number" step="0.0001" x-model="drug.per_cycle_cap" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Cap Unit</label>
                                        <input type="text" x-model="drug.per_cycle_cap_unit" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Lifetime Cap</label>
                                        <input type="number" step="0.0001" x-model="drug.lifetime_cap" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Lifetime Cap Unit</label>
                                        <input type="text" x-model="drug.lifetime_cap_unit" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-gray-500 mb-1">Notes</label>
                                        <input type="text" x-model="drug.notes" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-500 mb-1">Duration (days)</label>
                                        <input type="text" x-model="drug.duration_days" placeholder="e.g. 5" class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                                    </div>
                                </div>
                                <input type="hidden" :name="'drugs['+drug._key+'][drug_id]'" :value="drug.drug_id">
                                <input type="hidden" :name="'drugs['+drug._key+'][category]'" :value="drug.category">
                                <input type="hidden" :name="'drugs['+drug._key+'][dose_type]'" :value="drug.dose_type">
                                <input type="hidden" :name="'drugs['+drug._key+'][dose_per_unit]'" :value="drug.dose_per_unit">
                                <input type="hidden" :name="'drugs['+drug._key+'][dose_label]'" :value="drug.dose_label">
                                <input type="hidden" :name="'drugs['+drug._key+'][fixed_dose]'" :value="drug.fixed_dose">
                                <input type="hidden" :name="'drugs['+drug._key+'][target_auc]'" :value="drug.target_auc">
                                <input type="hidden" :name="'drugs['+drug._key+'][per_cycle_cap]'" :value="drug.per_cycle_cap">
                                <input type="hidden" :name="'drugs['+drug._key+'][per_cycle_cap_unit]'" :value="drug.per_cycle_cap_unit">
                                <input type="hidden" :name="'drugs['+drug._key+'][lifetime_cap]'" :value="drug.lifetime_cap">
                                <input type="hidden" :name="'drugs['+drug._key+'][lifetime_cap_unit]'" :value="drug.lifetime_cap_unit">
                                <input type="hidden" :name="'drugs['+drug._key+'][route]'" :value="drug.route">
                                <input type="hidden" :name="'drugs['+drug._key+'][frequency]'" :value="drug.frequency">
                                <input type="hidden" :name="'drugs['+drug._key+'][duration_days]'" :value="drug.duration_days">
                                <input type="hidden" :name="'drugs['+drug._key+'][notes]'" :value="drug.notes">
                            </div>
                        </template>
                    </div>

                    <div class="px-4 pb-4 border-t border-gray-100 pt-4 flex gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-lg transition">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Update Protocol
                        </button>
                        <a href="{{ route('admin.protocols.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2 rounded-lg border border-gray-200 transition">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function protocolBuilder(existingDrugs = []) {
    return {
        tab: 'chemotherapy',
        drugs: existingDrugs.map((d, i) => ({ ...d, _key: i })),
        keyCounter: existingDrugs.length,
        newDrug: { drug_id: '', category: 'chemotherapy' },
        drugsData: @json($drugs->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'unit' => $d->unit])),

        init() {},

        drugsByCategory(cat) { return this.drugs.filter(d => d.category === cat); },

        addDrug() {
            if (!this.newDrug.drug_id) return;
            const d = this.drugsData.find(x => x.id == this.newDrug.drug_id);
            this.drugs.push({
                _key: this.keyCounter++,
                drug_id: this.newDrug.drug_id,
                drug_label: d ? d.name + ' (' + d.unit + ')' : '',
                category: this.newDrug.category,
                dose_type: 'bsa_based', dose_per_unit: '', dose_label: '', fixed_dose: '', target_auc: '',
                per_cycle_cap: '', per_cycle_cap_unit: '', lifetime_cap: '', lifetime_cap_unit: '',
                route: 'IV', frequency: '', duration_days: '', notes: '',
            });
            this.tab = this.newDrug.category;
            this.newDrug.drug_id = '';
        },

        removeDrug(key) { this.drugs = this.drugs.filter(d => d._key !== key); },
    };
}
</script>
@endpush
