@extends('layouts.app')
@section('title', 'Order History')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5 p-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="mrn" value="{{ request('mrn') }}" placeholder="Patient MRN" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="protocol_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Protocols</option>
            @foreach($protocols as $p)
                <option value="{{ $p->id }}" {{ request('protocol_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="printed" {{ request('status') === 'printed' ? 'selected' : '' }}>Printed</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2 rounded-lg border border-gray-200 transition">Clear</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Order #</th>
                    <th class="px-4 py-3 text-left">Patient</th>
                    <th class="px-4 py-3 text-left">Protocol</th>
                    <th class="px-4 py-3 text-left">Cycle</th>
                    <th class="px-4 py-3 text-left">BSA</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-blue-700">
                        {{ $order->order_number }}
                        @if($order->is_modified_protocol)
                            <span class="ml-1 text-orange-500" title="Modified Protocol"><i class="fa-solid fa-triangle-exclamation text-xs"></i></span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('patients.show', $order->patient) }}" class="font-medium text-gray-800 hover:text-blue-600">{{ $order->patient->name }}</a>
                        <div class="text-xs text-gray-400">{{ $order->patient->mrn }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div>{{ $order->protocol->name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->protocol->diagnosis->name }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $order->cycle_number }}{{ $order->is_same_cycle ? '*' : '' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $order->bsa }} m²</td>
                    <td class="px-4 py-3 text-gray-500">{{ $order->ordered_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($order->status === 'confirmed')
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Confirmed</span>
                        @elseif($order->status === 'printed')
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Printed</span>
                        @else
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 flex items-center gap-3">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('orders.print', $order) }}" class="text-gray-500 hover:text-gray-700" target="_blank"><i class="fa-solid fa-print"></i></a>
                        @if($order->status === 'draft')
                        <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this draft order?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $orders->links() }}</div>
</div>
@endsection
