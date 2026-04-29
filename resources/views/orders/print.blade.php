<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemotherapy Order — {{ $order->order_number }}</title>
    <script src="/lib/tailwind/tailwind.js"></script>
    <link rel="stylesheet" href="/lib/fontawesome/downloaded/css/all.min.css">
    <style>
        @media print {
            @page {
                margin: 1.5cm;
            }

            .no-print {
                display: none !important;
            }

            body {
                font-size: 11px !important;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-gray-900 p-6">

    <div class="no-print flex gap-3 mb-6">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">
            <i class="fa-solid fa-print mr-1"></i> Print
        </button>
        <a href="{{ route('orders.show', $order) }}"
            class="border border-gray-200 text-gray-600 text-sm px-5 py-2 rounded-lg hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    {{-- Header --}}
    <div class="border-2 border-gray-800 rounded mb-4">
        <div class="bg-gray-800 text-white px-4 py-2 flex justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-300">Chemotherapy Order Form</p>
                <p class="font-bold text-lg">{{ env('HOSPITAL_NAME', 'Oncology Center') }}</p>
            </div>
            <div class="text-right">
                <p class="font-mono font-bold text-lg">{{ $order->order_number }}</p>
                <p class="text-xs text-gray-300">{{ $order->ordered_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    @if ($order->is_modified_protocol)
        <div class="border-2 border-red-600 bg-red-50 rounded px-4 py-2 mb-4 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            <div>
                <p class="font-bold text-red-800 uppercase text-sm tracking-wide">Modified Protocol Order</p>
                @if ($order->dose_modification_reason)
                    <p class="text-xs text-red-700">Reason: {{ $order->dose_modification_reason }}</p>
                @endif
            </div>
            <div class="ml-auto text-right text-sm">
                <p class="font-semibold text-red-800">{{ $order->dose_modification_percent }}% Dose</p>
            </div>
        </div>
    @endif

    {{-- Patient Info --}}
    <div class="border border-gray-300 rounded mb-4">
        <div class="bg-gray-100 px-3 py-1 border-b border-gray-300">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-600">Patient Information</p>
        </div>
        <div class="grid grid-cols-4 gap-0 text-xs">
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Patient Name</p>
                <p class="font-bold text-sm">{{ $order->patient->name }}</p>
            </div>
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">MRN</p>
                <p class="font-bold font-mono text-sm">{{ $order->patient->mrn }}</p>
            </div>
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Date of Birth / Gender</p>
                <p class="font-bold">{{ $order->patient->date_of_birth->format('d M Y') }} /
                    {{ ucfirst($order->patient->gender) }}</p>
            </div>
            <div class="px-3 py-2">
                <p class="text-gray-500 mb-0.5">Age</p>
                <p class="font-bold">{{ $order->patient->age }} years</p>
            </div>
            <div class="border-t border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Height</p>
                <p class="font-bold">{{ $order->patient->height_cm }} cm</p>
            </div>
            <div class="border-t border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Weight</p>
                <p class="font-bold">{{ $order->patient->weight_kg }} kg</p>
            </div>
            <div class="border-t border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">BSA</p>
                <p class="font-bold">{{ $order->bsa }} m²</p>
            </div>
            <div class="border-t border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">CrCl (Cockcroft-Gault)</p>
                <p class="font-bold">{{ $order->crcl }} mL/min</p>
            </div>
        </div>
    </div>

    {{-- Protocol Info --}}
    <div class="border border-gray-300 rounded mb-4">
        <div class="bg-gray-100 px-3 py-1 border-b border-gray-300">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-600">Protocol Information</p>
        </div>
        <div class="grid grid-cols-4 gap-0 text-xs">
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Diagnosis</p>
                <p class="font-bold">{{ $order->protocol->diagnosis->name }}</p>
            </div>
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Protocol</p>
                <p class="font-bold">{{ $order->protocol->name }}</p>
            </div>
            <div class="border-r border-gray-200 px-3 py-2">
                <p class="text-gray-500 mb-0.5">Cycle Number</p>
                <p class="font-bold">{{ $order->cycle_number }}{{ $order->is_same_cycle ? ' (Same Cycle)' : '' }}</p>
            </div>
            <div class="px-3 py-2">
                <p class="text-gray-500 mb-0.5">Order Date</p>
                <p class="font-bold">{{ $order->ordered_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Drug Tables --}}
    @foreach (['pre_medication' => 'Pre-Medications', 'chemotherapy' => 'Chemotherapy Agents', 'post_medication' => 'Post-Medications'] as $cat => $catLabel)
        @php $catDrugs = $order->orderDrugs->where('category', $cat)->where('is_included', true)->values(); @endphp
        @if ($catDrugs->count() > 0)
            <div class="border border-gray-300 rounded mb-4">
                <div
                    class="px-3 py-1 border-b border-gray-300 {{ $cat === 'chemotherapy' ? 'bg-red-50' : 'bg-gray-100' }}">
                    <p
                        class="text-xs font-bold uppercase tracking-wide {{ $cat === 'chemotherapy' ? 'text-red-700' : 'text-gray-600' }}">
                        @if ($cat === 'chemotherapy')
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        @endif
                        {{ $catLabel }}
                    </p>
                </div>
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Drug Name</th>
                            <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Route</th>
                            <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Frequency</th>
                            <th class="px-3 py-1.5 text-right font-semibold text-gray-600">Calc. Dose</th>
                            <th
                                class="px-3 py-1.5 text-right font-semibold text-gray-600 {{ $cat === 'chemotherapy' ? 'text-red-700 bg-red-50' : '' }}">
                                Final Dose</th>
                            <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Unit</th>
                            <th class="px-3 py-1.5 text-left font-semibold text-gray-600">Notes / Flags</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($catDrugs as $od)
                            <tr class="{{ $od->is_manually_overridden ? 'bg-orange-50' : '' }}">
                                <td class="px-3 py-2 font-semibold">{{ $od->drug->name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $od->protocolDrug->route ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $od->protocolDrug->frequency ?? '—' }}</td>
                                <td class="px-3 py-2 text-right font-mono text-gray-500">
                                    {{ number_format($od->calculated_dose, 2) }}</td>
                                <td
                                    class="px-3 py-2 text-right font-mono font-bold {{ $od->is_manually_overridden ? 'text-orange-700' : '' }} {{ $cat === 'chemotherapy' ? 'text-red-800' : '' }}">
                                    {{ number_format($od->final_dose, 2) }}
                                </td>
                                <td class="px-3 py-2">{{ $od->drug->unit }}</td>
                                <td class="px-3 py-2">
                                    @if ($od->cap_applied)
                                        <span class="bg-yellow-100 text-yellow-700 px-1 py-0.5 rounded text-xs mr-1">Cap
                                            Applied</span>
                                    @endif
                                    @if ($od->is_manually_overridden)
                                        <span class="bg-orange-100 text-orange-700 px-1 py-0.5 rounded text-xs"
                                            title="{{ $od->override_reason }}">Override{{ $od->override_reason ? ': ' . $od->override_reason : '' }}</span>
                                    @endif
                                    @if ($od->protocolDrug->notes)
                                        <span class="text-gray-500">{{ $od->protocolDrug->notes }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    {{-- Excluded drugs (if any) --}}
    @php $excludedDrugs = $order->orderDrugs->where('is_included', false); @endphp
    @if ($excludedDrugs->count() > 0)
        <div class="border border-gray-200 rounded mb-4 bg-gray-50">
            <div class="px-3 py-1 border-b border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase">Excluded Drugs</p>
            </div>
            <p class="px-3 py-2 text-xs text-gray-500">
                {{ $excludedDrugs->pluck('drug.name')->implode(', ') }}
            </p>
        </div>
    @endif

    {{-- Notes --}}
    @if ($order->notes)
        <div class="border border-gray-300 rounded mb-4 p-3 text-sm">
            <p class="font-semibold text-gray-600 text-xs uppercase mb-1">Clinical Notes</p>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
    @endif

    {{-- Signatures --}}
    <div class="border border-gray-300 rounded">
        <div class="bg-gray-100 px-3 py-1 border-b border-gray-300">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-600">Signatures</p>
        </div>
        <div class="grid grid-cols-3 divide-x divide-gray-200 text-xs">
            <div class="px-4 py-4">
                <p class="text-gray-500 mb-1">Consultant Physician</p>
                <p class="font-semibold mb-6">{{ $order->consultant_name ?: '____________________________' }}</p>
                <div class="border-b border-gray-400 mt-2 mb-1"></div>
                <p class="text-gray-400">Signature &amp; Date</p>
            </div>
            <div class="px-4 py-4">
                <p class="text-gray-500 mb-1">Clinical Pharmacist</p>
                <p class="font-semibold mb-6">{{ $order->pharmacist_name ?: '____________________________' }}</p>
                <div class="border-b border-gray-400 mt-2 mb-1"></div>
                <p class="text-gray-400">Signature &amp; Date</p>
            </div>
            <div class="px-4 py-4">
                <p class="text-gray-500 mb-1">Nurse</p>
                <p class="font-semibold mb-6">{{ $order->nurse_name ?: '____________________________' }}</p>
                <div class="border-b border-gray-400 mt-2 mb-1"></div>
                <p class="text-gray-400">Signature &amp; Date</p>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center text-xs text-gray-400">
        Printed: {{ now()->format('d M Y H:i') }} &mdash; {{ env('HOSPITAL_NAME') }} &mdash;
        {{ $order->order_number }} &mdash; CONFIDENTIAL MEDICAL DOCUMENT
    </div>
</body>

</html>
