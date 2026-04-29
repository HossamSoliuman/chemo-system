@extends('layouts.app')
@section('title', 'New Chemotherapy Order')

@section('content')
    <div x-data="orderForm()" x-init="init()" class="space-y-5">

        {{-- Lifetime Cap Warning Modal --}}
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
                                Cap: <span x-text="parseFloat(w.cap).toFixed(2)"></span>
                                <span x-text="w.cap_unit"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 mb-5">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    By confirming, you acknowledge this override and accept clinical responsibility.
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="acknowledgeAndSubmit()"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm py-2 rounded-lg transition font-medium">
                        <i class="fa-solid fa-check mr-1"></i> Acknowledge & Proceed
                    </button>
                    <button type="button" @click="showCapModal = false"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        {{-- Step 1: Patient --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">1</span>
                Patient Lookup
            </h3>
            <div class="flex gap-3 mb-4">
                <input type="text" x-model="mrnInput" placeholder="Enter MRN..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" @click="lookupMrn()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Lookup
                </button>
                <span x-show="patientNotFound" class="text-red-500 text-sm self-center"><i
                        class="fa-solid fa-circle-xmark mr-1"></i> Patient not found</span>
            </div>

            <div x-show="patient" x-cloak class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 mb-1">Patient Name</p>
                    <p class="font-semibold text-gray-800 text-sm" x-text="patient?.name"></p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 mb-1">MRN</p>
                    <p class="font-semibold text-gray-800 text-sm font-mono" x-text="patient?.mrn"></p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 mb-1">BSA</p>
                    <p class="font-semibold text-gray-800 text-sm" x-text="bsa ? bsa + ' m²' : '—'"></p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 mb-1">CrCl</p>
                    <p class="font-semibold text-gray-800 text-sm" x-text="crcl ? crcl + ' mL/min' : '—'"></p>
                </div>
                <input type="hidden" name="patient_id" :value="patient?.id">
            </div>
            <div x-show="!patient" class="text-sm text-gray-400 italic">No patient selected. Enter MRN above or <a
                    href="{{ route('patients.create') }}" class="text-blue-600 hover:underline">register a new patient</a>.
            </div>
        </div>

        {{-- Step 2 & 3: Diagnosis + Protocol --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
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
            <div x-show="cycleInfo" x-cloak class="mt-3 flex gap-3">
                <span class="text-sm text-gray-600"><i class="fa-solid fa-rotate-right mr-1 text-blue-500"></i> Cycle
                    <strong x-text="cycleInfo?.cycle_number"></strong></span>
                <span x-show="cycleInfo?.is_same_cycle"
                    class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full"><i
                        class="fa-solid fa-link mr-1"></i> Same Cycle (within 6 days)</span>
            </div>
        </div>

        {{-- Step 4: Dose Modification reason (global reason only, per-drug % is in the table) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-show="drugs.length" x-cloak>
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">3</span>
                Dose Modification
            </h3>
            <p class="text-xs text-gray-500 mb-3">
                <i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
                To reduce a drug dose, change its <strong>Mod %</strong> column in the table below. Each drug can have an
                independent reduction. Leave at 100% for full dose.
            </p>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Reason for Dose Modification (if any)</label>
                <input type="text" name="dose_modification_reason"
                    placeholder="e.g. Grade 2 neurotoxicity, renal impairment, patient request"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Step 5: Drug Table --}}
        <div x-show="drugs.length" x-cloak>
            <template x-for="category in ['pre_medication', 'chemotherapy', 'post_medication']" :key="category">
                <div x-show="drugsByCategory(category).length"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-circle text-xs"
                            :class="{ 'text-green-500': category==='pre_medication', 'text-red-500': category==='chemotherapy', 'text-blue-500': category==='post_medication' }"></i>
                        <h3 class="font-semibold text-gray-700 text-sm" x-text="categoryLabel(category)"></h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-xs uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left w-8">Inc.</th>
                                    <th class="px-3 py-2 text-left">Drug</th>
                                    <th class="px-3 py-2 text-left">Route</th>
                                    <th class="px-3 py-2 text-left">Frequency</th>
                                    <th class="px-3 py-2 text-left">Calc. Dose</th>
                                    <th class="px-3 py-2 text-left w-24">Mod %</th>
                                    <th class="px-3 py-2 text-left">Final Dose</th>
                                    <th class="px-3 py-2 text-left">Unit</th>
                                    <th class="px-3 py-2 text-left">Flags</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="drug in drugsByCategory(category)" :key="drug.protocol_drug_id">
                                    <tr :class="!drug.is_included ? 'opacity-50 bg-gray-50' : ''">
                                        <td class="px-3 py-2">
                                            <input type="checkbox" x-model="drug.is_included"
                                                class="rounded border-gray-300 text-blue-600">
                                        </td>
                                        <td class="px-3 py-2 font-medium text-gray-800" x-text="drug.drug_name"></td>
                                        <td class="px-3 py-2 text-gray-500 text-xs" x-text="drug.route || '—'"></td>
                                        <td class="px-3 py-2 text-gray-500 text-xs" x-text="drug.frequency || '—'"></td>
                                        <td class="px-3 py-2 font-mono text-xs text-gray-500"
                                            x-text="parseFloat(drug.base_dose).toFixed(2)"></td>
                                        <td class="px-3 py-2">
                                            <template x-if="drug.dose_type === 'fixed'">
                                                <span class="text-xs text-gray-400 italic">fixed</span>
                                            </template>
                                            <template x-if="drug.dose_type !== 'fixed'">
                                                <div class="flex items-center gap-1">
                                                    <input type="number" x-model="drug.modification_pct"
                                                        @input="applyDrugModification(drug)" min="1"
                                                        max="200" step="5"
                                                        class="w-16 border rounded px-1.5 py-1 text-xs font-mono text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        :class="drug.modification_pct != 100 ?
                                                            'border-orange-400 bg-orange-50 text-orange-700 font-bold' :
                                                            'border-gray-300'">
                                                    <span class="text-xs text-gray-400">%</span>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.01" x-model="drug.final_dose"
                                                @change="drug.is_manually_overridden = true; drug.modification_pct = 100"
                                                class="w-24 border border-gray-300 rounded px-2 py-1 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                :class="drug.is_manually_overridden ? 'border-orange-400 bg-orange-50' : (drug
                                                    .modification_pct != 100 ? 'border-yellow-400 bg-yellow-50' : ''
                                                    )">
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-400" x-text="drug.drug_unit"></td>
                                        <td class="px-3 py-2">
                                            <div class="flex flex-wrap gap-1">
                                                <span x-show="drug.cap_applied"
                                                    class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded whitespace-nowrap"><i
                                                        class="fa-solid fa-circle-minus"></i> Cap</span>
                                                <span x-show="drug.modification_pct != 100 && !drug.is_manually_overridden"
                                                    class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded whitespace-nowrap"><i
                                                        class="fa-solid fa-percent"></i> <span
                                                        x-text="drug.modification_pct+'%'"></span></span>
                                                <span x-show="drug.is_manually_overridden"
                                                    class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded whitespace-nowrap"><i
                                                        class="fa-solid fa-pen"></i> Override</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr x-show="drug.is_manually_overridden"
                                        :class="!drug.is_included ? 'opacity-50' : ''">
                                        <td colspan="9" class="px-3 pb-2 pt-0">
                                            <input type="text" x-model="drug.override_reason"
                                                placeholder="Reason for manual dose override (required)"
                                                class="w-full border border-orange-300 rounded px-2 py-1 text-xs bg-orange-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>

        {{-- Step 6: Clinician Info & Notes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-show="drugs.length" x-cloak>
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">4</span>
                Clinician &amp; Notes
            </h3>
            <div class="grid grid-cols-3 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Consultant</label>
                    <input type="text" name="consultant_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pharmacist</label>
                    <input type="text" name="pharmacist_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nurse</label>
                    <input type="text" name="nurse_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Clinical Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>

        {{-- Submit --}}
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
                diagnosisId: '',
                protocolId: '',
                protocols: [],
                drugs: [],
                cycleInfo: null,
                bsa: null,
                crcl: null,
                showCapModal: false,
                capWarnings: [],
                submitting: false,
                _pendingPayload: null,

                init() {
                    @if (request('patient_id'))
                        this.loadPatientById({{ request('patient_id') }});
                    @endif
                },

                async lookupMrn() {
                    this.patientNotFound = false;
                    const res = await fetch(`/api/patients/mrn/${encodeURIComponent(this.mrnInput)}`);
                    const data = await res.json();
                    if (data.found) {
                        this.patient = data.patient;
                        if (this.protocolId) this.loadDrugTable();
                    } else {
                        this.patient = null;
                        this.patientNotFound = true;
                    }
                },

                async loadPatientById(id) {},

                async loadProtocols() {
                    this.protocolId = '';
                    this.drugs = [];
                    this.cycleInfo = null;
                    if (!this.diagnosisId) {
                        this.protocols = [];
                        return;
                    }
                    const res = await fetch(`/api/protocols?diagnosis_id=${this.diagnosisId}`);
                    this.protocols = await res.json();
                },

                async loadDrugTable() {
                    if (!this.patient || !this.protocolId) return;
                    const res = await fetch('/api/orders/calculate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            patient_id: this.patient.id,
                            protocol_id: this.protocolId,
                        }),
                    });
                    const data = await res.json();
                    this.bsa = data.bsa;
                    this.crcl = data.crcl;
                    this.cycleInfo = data.cycle_info;
                    this.drugs = data.drugs.map(d => ({
                        ...d,
                        base_dose: d.calculated_dose,
                        final_dose: d.final_dose,
                        modification_pct: 100,
                        is_included: true,
                        is_manually_overridden: false,
                        override_reason: '',
                    }));
                },

                applyDrugModification(drug) {
                    const pct = parseFloat(drug.modification_pct);
                    if (isNaN(pct) || drug.dose_type === 'fixed') return;
                    drug.is_manually_overridden = false;
                    drug.override_reason = '';
                    const newFinal = parseFloat((drug.base_dose * (pct / 100)).toFixed(2));
                    if (drug.cap_applied && drug.per_cycle_cap && newFinal > drug.per_cycle_cap) {
                        drug.final_dose = drug.per_cycle_cap;
                    } else {
                        drug.final_dose = newFinal;
                    }
                },

                drugsByCategory(cat) {
                    return this.drugs.filter(d => d.category === cat);
                },

                categoryLabel(cat) {
                    return {
                        pre_medication: 'Pre-Medications',
                        chemotherapy: 'Chemotherapy',
                        post_medication: 'Post-Medications'
                    } [cat] || cat;
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
