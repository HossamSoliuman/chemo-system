@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="flex items-start gap-5">

    <div class="flex-1 space-y-4">

        @if($order->is_modified_protocol)
        <div class="bg-orange-50 border-2 border-orange-400 rounded-xl px-5 py-3 flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-orange-500 text-2xl"></i>
            <div>
                <p class="font-bold text-orange-800 uppercase tracking-wide">Modified Protocol</p>
                @if($order->dose_modification_reason)
                <p class="text-sm text-orange-700">{{ $order->dose_modification_reason }}</p>
                @endif
            </div>
        </div>
        @endif

        @if($order->is_same_cycle)
        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-sm text-blue-700">
            <i class="fa-solid fa-link mr-1"></i> Same-cycle order — linked to original cycle {{ $order->cycle_number }}
        </div>
        @endif

        @foreach(['pre_medication' => 'Pre-Medications', 'chemotherapy' => 'Chemotherapy', 'post_medication' => 'Post-Medications'] as $cat => $catLabel)
        @php $catDrugs = $order->orderDrugs->where('category', $cat)->values(); @endphp
        @if($catDrugs->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-circle text-xs {{ $cat === 'pre_medication' ? 'text-green-500' : ($cat === 'chemotherapy' ? 'text-red-500' : 'text-blue-500') }}"></i>
                <h3 class="font-semibold text-gray-700 text-sm">{{ $catLabel }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Drug</th>
                            <th class="px-4 py-2 text-left">Route</th>
                            <th class="px-4 py-2 text-left">Frequency</th>
                            <th class="px-4 py-2 text-right">Calc. Dose</th>
                            <th class="px-4 py-2 text-right">Final Dose</th>
                            <th class="px-4 py-2 text-left">Unit</th>
                            <th class="px-4 py-2 text-left">Flags</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($catDrugs as $od)
                        <tr class="{{ !$od->is_included ? 'opacity-40 bg-gray-50' : '' }}">
                            <td class="px-4 py-2 font-medium {{ !$od->is_included ? 'line-through' : '' }}">{{ $od->drug->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $od->protocolDrug->route ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $od->protocolDrug->frequency ?? '—' }}</td>
                            <td class="px-4 py-2 text-right font-mono text-gray-500">{{ number_format($od->calculated_dose, 2) }}</td>
                            <td class="px-4 py-2 text-right font-mono font-semibold {{ $od->is_manually_overridden ? 'text-orange-600' : 'text-gray-800' }}">{{ number_format($od->final_dose, 2) }}</td>
                            <td class="px-4 py-2 text-xs text-gray-400">{{ $od->drug->unit }}</td>
                            <td class="px-4 py-2 flex gap-1">
                                @if($od->cap_applied) <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded"><i class="fa-solid fa-circle-minus"></i> Cap</span> @endif
                                @if($od->is_manually_overridden) <span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded" title="{{ $od->override_reason }}"><i class="fa-solid fa-pen"></i> Override</span> @endif
                                @if(!$od->is_included) <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">Excluded</span> @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endforeach

        @if($order->notes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-2 text-sm">Clinical Notes</h3>
            <p class="text-sm text-gray-600">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    <div class="w-64 flex-shrink-0 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Order #</span><span class="font-mono font-medium text-blue-700">{{ $order->order_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span>
                @if($order->status === 'confirmed')
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Confirmed</span>
                @elseif($order->status === 'printed')
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Printed</span>
                @else
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">Draft</span>
                @endif
            </div>
            <div class="flex justify-between"><span class="text-gray-500">Patient</span><a href="{{ route('patients.show', $order->patient) }}" class="font-medium text-blue-600 hover:underline">{{ $order->patient->name }}</a></div>
            <div class="flex justify-between"><span class="text-gray-500">Protocol</span><span class="font-medium">{{ $order->protocol->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Cycle</span><span class="font-medium">{{ $order->cycle_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">BSA</span><span class="font-mono font-medium">{{ $order->bsa }} m²</span></div>
            <div class="flex justify-between"><span class="text-gray-500">CrCl</span><span class="font-mono font-medium">{{ $order->crcl }} mL/min</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Dose Mod.</span><span class="font-medium {{ $order->dose_modification_percent != 100 ? 'text-orange-600' : '' }}">{{ $order->dose_modification_percent }}%</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Date</span><span class="font-medium">{{ $order->ordered_at->format('d M Y H:i') }}</span></div>
        </div>

        @if($order->consultant_name || $order->pharmacist_name || $order->nurse_name)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-2 text-sm">
            @if($order->consultant_name) <div class="flex justify-between"><span class="text-gray-500">Consultant</span><span>{{ $order->consultant_name }}</span></div> @endif
            @if($order->pharmacist_name) <div class="flex justify-between"><span class="text-gray-500">Pharmacist</span><span>{{ $order->pharmacist_name }}</span></div> @endif
            @if($order->nurse_name) <div class="flex justify-between"><span class="text-gray-500">Nurse</span><span>{{ $order->nurse_name }}</span></div> @endif
        </div>
        @endif

        <div class="space-y-2">
            @if($order->status === 'draft')
            <form method="POST" action="{{ route('orders.confirm', $order) }}">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm py-2.5 rounded-lg transition font-medium">
                    <i class="fa-solid fa-circle-check mr-1"></i> Confirm Order
                </button>
            </form>
            @endif
            <a href="{{ route('orders.print', $order) }}" target="_blank" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white text-sm py-2.5 rounded-lg transition font-medium">
                <i class="fa-solid fa-print mr-1"></i> Print Order
            </a>
            @if($order->status === 'draft')
            <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this draft order?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full border border-red-200 text-red-600 text-sm py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="fa-solid fa-trash mr-1"></i> Delete Draft
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
