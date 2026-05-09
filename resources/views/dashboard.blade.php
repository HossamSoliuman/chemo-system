@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    {{-- Full-width WhatsApp Contact Banner --}}
    <a href="https://wa.me/201119583047" target="_blank"
        class="flex items-center justify-between w-full bg-green-500 hover:bg-green-600 transition rounded-xl shadow-sm px-6 py-4 mb-5 group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-brands fa-whatsapp text-white text-2xl"></i>
            </div>
            <div>
                <p class="text-white font-bold text-lg leading-tight">Contact Us on WhatsApp</p>
                <p class="text-green-100 text-sm mt-0.5">We're here to help — click to start a conversation</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-white font-mono font-semibold text-lg tracking-wide">+20 111 958 3047</span>
            <i class="fa-solid fa-arrow-right text-white/70 group-hover:text-white transition text-sm"></i>
        </div>
    </a>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fa-solid fa-users text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Patients</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPatients }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fa-solid fa-file-medical text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Orders Today</p>
                <p class="text-3xl font-bold text-gray-800">{{ $ordersToday }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Orders This Month</p>
                <p class="text-3xl font-bold text-gray-800">{{ $ordersThisMonth }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Recent Orders</h2>
            <a href="{{ route('orders.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                <i class="fa-solid fa-plus"></i> New Order
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Order #</th>
                        <th class="px-4 py-3 text-left">Patient</th>
                        <th class="px-4 py-3 text-left">Protocol</th>
                        <th class="px-4 py-3 text-left">Cycle</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-medium text-blue-700">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $order->patient->name }}</div>
                                <div class="text-xs text-gray-400">{{ $order->patient->mrn }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $order->protocol->name }}</td>
                            <td class="px-4 py-3 text-gray-600">Cycle {{ $order->cycle_number }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->ordered_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if ($order->status === 'confirmed')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-green-100 text-green-700"><i
                                            class="fa-solid fa-circle-check"></i> Confirmed</span>
                                @elseif($order->status === 'printed')
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"><i
                                            class="fa-solid fa-print"></i> Printed</span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700"><i
                                            class="fa-solid fa-pen"></i> Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order) }}"
                                    class="text-blue-600 hover:text-blue-800 mr-2"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('orders.print', $order) }}" class="text-gray-500 hover:text-gray-700"
                                    target="_blank"><i class="fa-solid fa-print"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No orders yet. <a
                                    href="{{ route('orders.create') }}" class="text-blue-600 hover:underline">Create the
                                    first order</a>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
