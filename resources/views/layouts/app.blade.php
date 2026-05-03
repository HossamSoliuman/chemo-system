<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OncoChemo') }} — @yield('title', 'Dashboard')</title>
    <script src="/lib/tailwind/tailwind.js"></script>
    <script defer src="/lib/alpine/alpine.min.js"></script>
    <link rel="stylesheet" href="/lib/fontawesome/downloaded/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { @apply bg-blue-700 text-white; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="no-print w-64 bg-blue-900 text-white flex flex-col flex-shrink-0">
        <div class="px-6 py-5 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-hospital-symbol text-2xl text-blue-300"></i>
                <div>
                    <p class="text-xs text-blue-300 uppercase tracking-wide">OncoChemo</p>
                    <p class="text-sm font-semibold leading-tight">{{ env('HOSPITAL_NAME', 'Oncology Center') }}</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i> Dashboard
            </a>

            <p class="px-3 pt-4 pb-1 text-xs text-blue-400 uppercase tracking-wider">Clinical</p>
            <a href="{{ route('orders.create') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('orders.create') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-file-medical w-5 text-center"></i> New Order
            </a>
            <a href="{{ route('orders.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('orders.*') && !request()->routeIs('orders.create') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i> Order History
            </a>
            <a href="{{ route('patients.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('patients.*') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> Patients
            </a>

            <p class="px-3 pt-4 pb-1 text-xs text-blue-400 uppercase tracking-wider">Administration</p>
            <a href="{{ route('admin.protocols.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('admin.protocols.*') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-flask w-5 text-center"></i> Protocols
            </a>
            <a href="{{ route('admin.diagnoses.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('admin.diagnoses.*') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-stethoscope w-5 text-center"></i> Diagnoses
            </a>
            <a href="{{ route('admin.drugs.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('admin.drugs.*') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-capsules w-5 text-center"></i> Drug Master
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-700' : '' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> Users
            </a>
        </nav>

        <div class="px-4 py-3 border-t border-blue-800 text-xs text-blue-400">
            OncoChemo v1.0 &mdash; Offline
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="no-print bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-700">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <i class="fa-solid fa-circle text-green-500 text-xs"></i> System Online
                    <span>{{ now()->format('d M Y') }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm" x-data="{ open: false }">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <button @click="open = !open" class="text-gray-700 hover:text-gray-900 font-medium flex items-center gap-1">
                        {{ auth()->user()->name }}
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div @click.away="open = false" x-show="open" class="absolute right-6 top-16 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-200">
                            <i class="fa-solid fa-users mr-2"></i> Manage Users
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                <i class="fa-solid fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
