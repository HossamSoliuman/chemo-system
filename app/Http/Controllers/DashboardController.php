<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $ordersToday = Order::whereDate('created_at', today())->count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $recentOrders = Order::with(['patient', 'protocol'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard', compact('totalPatients', 'ordersToday', 'ordersThisMonth', 'recentOrders'));
    }
}
