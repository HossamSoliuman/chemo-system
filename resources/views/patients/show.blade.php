@extends('layouts.app')
@section('title', 'Patient Profile')

@section('content')
<div class="flex items-start gap-6">

    <div class="w-72 flex-shrink-0 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-{{ $patient->gender === 'female' ? 'person-dress' : 'person' }} text-blue-600"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800">{{ $patient->name }}</h2>
                    <p class="text-xs text-gray-400 font-mono">{{ $patient->mrn }}</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Age</dt><dd class="font-medium">{{ $patient->age }} years</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Gender</dt><dd class="font-medium capitalize">{{ $patient->gender }}</dd></div>
                @if($patient->nationality)
                <div class="flex justify-between"><dt class="text-gray-500">Nationality</dt><dd class="font-medium">{{ $patient->nationality }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-gray-500">DOB</dt><dd class="font-medium">{{ $patient->date_of_birth->format('d M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Height</dt><dd class="font-medium">{{ $patient->height_cm }} cm</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Weight</dt><dd class="font-medium">{{ $patient->weight_kg }} kg</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Creatinine</dt><dd class="font-medium">{{ $patient->serum_creatinine }} µmol/L</dd></div>
                @if($patient->cancer_stage)
                <div class="flex justify-between"><dt class="text-gray-500">Stage</dt><dd class="font-medium">Stage {{ $patient->cancer_stage }}</dd></div>
                @endif
                @if($patient->ecog_status !== null && $patient->ecog_status !== '')
                <div class="flex justify-between"><dt class="text-gray-500">ECOG</dt><dd class="font-medium">{{ $patient->ecog_status }}</dd></div>
                @endif
                @if($patient->chemo_setting)
                <div class="flex justify-between"><dt class="text-gray-500">Setting</dt><dd class="font-medium">{{ $patient->chemo_setting }}</dd></div>
                @endif
                @if($patient->consultant_in_charge)
                <div class="flex justify-between"><dt class="text-gray-500">Consultant</dt><dd class="font-medium text-xs">{{ $patient->consultant_in_charge }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-gray-500">Pregnant</dt><dd class="font-medium capitalize">{{ $patient->pregnant ?? 'N/A' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Lactating</dt><dd class="font-medium capitalize">{{ $patient->lactating ?? 'N/A' }}</dd></div>
                <div class="flex justify-between items-start"><dt class="text-gray-500">Allergy</dt>
                    <dd class="font-medium text-right">
                        @if($patient->has_allergy)
                            <span class="text-red-600">Yes</span>
                            @if($patient->allergy_details) <div class="text-xs text-red-500">{{ $patient->allergy_details }}</div> @endif
                        @else
                            <span class="text-green-600">No</span>
                        @endif
                    </dd>
                </div>
            </dl>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('patients.edit', $patient) }}" class="flex-1 text-center text-sm border border-gray-200 rounded-lg py-1.5 text-gray-600 hover:bg-gray-50 transition"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</a>
                <a href="{{ route('orders.create') }}?patient_id={{ $patient->id }}" class="flex-1 text-center text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-1.5 transition"><i class="fa-solid fa-plus mr-1"></i> New Order</a>
            </div>
        </div>

        @if($cumulativeDoses->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Cumulative Doses</h3>
            <div class="space-y-3">
                @foreach($cumulativeDoses as $cd)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600 font-medium">{{ $cd->drug->name }}</span>
                        <span class="text-gray-500">{{ number_format($cd->total_dose, 2) }} {{ $cd->drug->unit }}</span>
                    </div>
                    @if($cd->lifetime_cap)
                    @php $pct = min(100, ($cd->total_dose / $cd->lifetime_cap) * 100) @endphp
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5">Cap: {{ number_format($cd->lifetime_cap, 2) }} {{ $cd->lifetime_cap_unit }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="flex-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Order History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Order #</th>
                            <th class="px-4 py-3 text-left">Protocol</th>
                            <th class="px-4 py-3 text-left">Cycle</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($patient->orders->sortByDesc('ordered_at') as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-blue-700">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $order->protocol->name }}</div>
                                <div class="text-xs text-gray-400">{{ $order->protocol->diagnosis->name }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $order->cycle_number }}{{ $order->is_same_cycle ? ' (same)' : '' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->ordered_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($order->status === 'confirmed')
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Confirmed</span>
                                @elseif($order->status === 'printed')
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Printed</span>
                                @else
                                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('orders.print', $order) }}" class="text-gray-500 hover:text-gray-700" target="_blank"><i class="fa-solid fa-print"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No orders found for this patient.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
