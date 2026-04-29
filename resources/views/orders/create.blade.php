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
            <p class="text-sm text-gray-600 mb-4">The following drugs will exceed their cumulative lifetime dose limits:</p>
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
                <button type="button" @click="acknowledgeAndSubmit()" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm py-2 rounded-lg transition font-medium">
                    <i class="fa-solid fa-check mr-1"></i> Acknowledge & Proceed
                </button>
                <button type="button" @click="showCapModal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm py-2 rounded-lg hover:bg-gray-50 transition">
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
            <input type="text" x-model="mrnInput" placeholder="Enter MRN..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="button" @click="lookupMrn()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Lookup
            </button>
            <span x-show="patientNotFound" class="text-red-500 text-sm self-center"><i class="fa-solid fa-circle-xmark mr-1"></i> Patient not found</span>
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
        <div x-show="!patient" class="text-sm text-gray-400 italic">No patient selected. Enter MRN above or <a href="{{ route('patients.create') }}" class="text-blue-600 hover:underline">register a new patient</a>.</div>
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
                <select x-model="diagnosisId" @change="loadProtocols()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select diagnosis</option>
                    @foreach($diagnoses as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Protocol</label>
                <select x-model="protocolId" @change="loadDrugTable()" :disabled="!protocols.length" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                    <option value="">Select protocol</option>
                    <template x-for="p in protocols" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
                <input type="hidden" name="protocol_id" :value="protocolId">
            </div>
        </div>
        <div x-show="cycleInfo" x-cloak class="mt-3 flex gap-3">
            <span class="text-sm text-gray-600"><i class="fa-solid fa-rotate-right mr-1 text-blue-500"></i> Cycle <strong x-text="cycleInfo?.cycle_number"></strong></span>
            <span x-show="cycleInfo?.is_same_cycle" class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full"><i class="fa-solid fa-link mr-1"></i> Same Cycle (within 6 days)</span>
        </div>
    </div>

    {{-- Step 4: Dose Modification --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-show="drugs.length" x-cloak>
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">3</span>
            Dose Modification
        </h3>
        <div class="flex items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Global Dose Modification %</label>
                <select x-model="modPct" @change="applyGlobalModification()" name="dose_modification_percent" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="75">75%</option>
                    <option value="80">80%</option>
                    <option value="90">90%</option>
                    <option value="100" selected>100% (Full dose)</option>
                    <option value="110">110%</option>
                    <option value="120">120%</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div x-show="modPct === 'custom'">
                <label class="block text-xs font-medium text-gray-500 mb-1">Custom %</label>
                <input type="number" x-model="customModPct" @change="applyGlobalModification()" min="1" max="200" name="dose_modification_percent_custom" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 mb-1">Reason for Modification</label>
                <input type="text" name="dose_modification_reason" placeholder="e.g. Renal impairment, toxicity" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Step 5: Drug Table --}}
    <div x-show="drugs.length" x-cloak>
        <template x-for="category in ['pre_medication', 'chemotherapy', 'post_medication']" :key="category">
            <div x-show="drugsByCategory(category).length" class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-xs" :class="{'text-green-500': category==='pre_medication', 'text-red-500': category==='chemotherapy', 'text-blue-500': category==='post_medication'}"></i>
                    <h3 class="font-semibold text-gray-700 text-sm" x-text="categoryLabel(category)"></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left w-8">Inc.</th>
                                <th class="px-4 py-2 text-left">Drug</th>
                                <th class="px-4 py-2 text-left">Route</th>
                                <th class="px-4 py-2 text-left">Frequency</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Calc. Dose</th>
                                <th class="px-4 py-2 text-left">Final Dose</th>
                                <th class="px-4 py-2 text-left">Unit</th>
                                <th class="px-4 py-2 text-left">Flags</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="drug in drugsByCategory(category)" :key="drug.protocol_drug_id">
                                <tr :class="!drug.is_included ? 'opacity-50' : ''">
                                    <td class="px-4 py-2">
                                        <input type="checkbox" :name="'drugs['+drug.protocol_drug_id+'][is_included]'" :value="1" x-model="drug.is_included" class="rounded border-gray-300 text-blue-600">
                                    </td>
                                    <td class="px-4 py-2 font-medium text-gray-800" x-text="drug.drug_name"></td>
                                    <td class="px-4 py-2 text-gray-500" x-text="drug.route || '—'"></td>
                                    <td class="px-4 py-2 text-gray-500 text-xs" x-text="drug.frequency || '—'"></td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono text-gray-400" x-text="drug.dose_type?.replace('_', ' ')"></span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600 font-mono text-xs" x-text="parseFloat(drug.calculated_dose).toFixed(2)"></td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01"
                                            :name="'drugs['+drug.protocol_drug_id+'][final_dose]'"
                                            x-model="drug.final_dose"
                                            @change="drug.is_manually_overridden = true"
                                            class="w-24 border border-gray-300 rounded px-2 py-1 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="drug.is_manually_overridden ? 'border-orange-400 bg-orange-50' : ''">
                                        <input type="hidden" :name="'drugs['+drug.protocol_drug_id+'][protocol_drug_id]'" :value="drug.protocol_drug_id">
                                        <input type="hidden" :name="'drugs['+drug.protocol_drug_id+'][is_manually_overridden]'" :value="drug.is_manually_overridden ? 1 : 0">
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-400" x-text="drug.drug_unit"></td>
                                    <td class="px-4 py-2 flex gap-1">
                                        <span x-show="drug.cap_applied" class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded" title="Per-cycle cap applied"><i class="fa-solid fa-circle-minus"></i> Cap</span>
                                        <span x-show="drug.is_manually_overridden" class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded"><i class="fa-solid fa-pen"></i> Modified</span>
                                    </td>
                                </tr>
                                <tr x-show="drug.is_manually_overridden" :class="!drug.is_included ? 'opacity-50' : ''">
                                    <td colspan="9" class="px-4 pb-2">
                                        <input type="text" :name="'drugs['+drug.protocol_drug_id+'][override_reason]'" placeholder="Reason for manual override (required)" class="w-full border border-orange-300 rounded px-2 py-1 text-xs bg-orange-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
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
                <input type="text" name="consultant_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Pharmacist</label>
                <input type="text" name="pharmacist_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nurse</label>
                <input type="text" name="nurse_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Clinical Notes</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
    </div>

    {{-- Submit --}}
    <div x-show="drugs.length" x-cloak class="flex gap-3">
        <button type="button" @click="submitOrder()" :disabled="submitting" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg transition font-medium disabled:opacity-50">
            <i class="fa-solid fa-floppy-disk mr-1"></i>
            <span x-text="submitting ? 'Saving...' : 'Save as Draft'"></span>
        </button>
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-5 py-2.5 rounded-lg border border-gray-200 transition">Cancel</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function orderForm() {
    return {
        mrnInput: '{{ request("mrn", "") }}',
        patient: null,
        patientNotFound: false,
        diagnosisId: '',
        protocolId: '',
        protocols: [],
        drugs: [],
        cycleInfo: null,
        bsa: null,
        crcl: null,
        modPct: '100',
        customModPct: 100,
        showCapModal: false,
        capWarnings: [],
        submitting: false,
        formEl: null,

        init() {
            this.formEl = document.createElement('form');
            @if(request('patient_id'))
            this.loadPatientById({{ request('patient_id') }});
            @endif
        },

        async lookupMrn() {
            this.patientNotFound = false;
            const res = await fetch(`/api/patients/mrn/${encodeURIComponent(this.mrnInput)}`);
            const data = await res.json();
            if (data.found) {
                this.patient = data.patient;
                this.patientNotFound = false;
                if (this.protocolId) this.loadDrugTable();
            } else {
                this.patient = null;
                this.patientNotFound = true;
            }
        },

        async loadPatientById(id) {
            const res = await fetch(`/api/patients/mrn/__none__`).catch(() => null);
        },

        async loadProtocols() {
            this.protocolId = '';
            this.drugs = [];
            this.cycleInfo = null;
            if (!this.diagnosisId) { this.protocols = []; return; }
            const res = await fetch(`/api/protocols?diagnosis_id=${this.diagnosisId}`);
            this.protocols = await res.json();
        },

        async loadDrugTable() {
            if (!this.patient || !this.protocolId) return;
            const effectivePct = this.modPct === 'custom' ? this.customModPct : this.modPct;
            const res = await fetch('/api/orders/calculate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ patient_id: this.patient.id, protocol_id: this.protocolId, dose_modification_percent: effectivePct }),
            });
            const data = await res.json();
            this.bsa = data.bsa;
            this.crcl = data.crcl;
            this.cycleInfo = data.cycle_info;
            this.drugs = data.drugs.map(d => ({ ...d, is_included: true, is_manually_overridden: false }));
        },

        applyGlobalModification() {
            if (!this.drugs.length) return;
            this.loadDrugTable();
        },

        drugsByCategory(cat) {
            return this.drugs.filter(d => d.category === cat);
        },

        categoryLabel(cat) {
            return { pre_medication: 'Pre-Medications', chemotherapy: 'Chemotherapy', post_medication: 'Post-Medications' }[cat] || cat;
        },

        async submitOrder() {
            if (!this.patient || !this.protocolId) { alert('Please select a patient and protocol.'); return; }
            this.submitting = true;
            const effectivePct = this.modPct === 'custom' ? this.customModPct : this.modPct;
            const payload = {
                patient_id: this.patient.id,
                protocol_id: this.protocolId,
                dose_modification_percent: effectivePct,
                dose_modification_reason: document.querySelector('[name=dose_modification_reason]')?.value,
                consultant_name: document.querySelector('[name=consultant_name]')?.value,
                pharmacist_name: document.querySelector('[name=pharmacist_name]')?.value,
                nurse_name: document.querySelector('[name=nurse_name]')?.value,
                notes: document.querySelector('[name=notes]')?.value,
                drugs: this.drugs.map(d => ({
                    protocol_drug_id: d.protocol_drug_id,
                    final_dose: d.final_dose,
                    is_included: d.is_included ? 1 : 0,
                    is_manually_overridden: d.is_manually_overridden ? 1 : 0,
                    override_reason: document.querySelector(`[name="drugs[${d.protocol_drug_id}][override_reason]"]`)?.value,
                })),
            };

            const res = await fetch('/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
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
            const payload = { ...this._pendingPayload, lifetime_cap_acknowledged: true };
            const res = await fetch('/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
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
