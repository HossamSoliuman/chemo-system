<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OncoChemo') }} — Login</title>
    <script src="/lib/tailwind/tailwind.js"></script>
    <link rel="stylesheet" href="/lib/fontawesome/downloaded/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen flex items-center justify-center font-sans">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <i class="fa-solid fa-hospital-symbol text-4xl text-blue-600 mb-3"></i>
                <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'OncoChemo') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ env('HOSPITAL_NAME', 'Oncology Center') }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700">
                        <i class="fa-solid fa-circle-exclamation mr-2"></i>
                        {{ $errors->first('email') }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="admin@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
                >
                    <i class="fa-solid fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                OncoChemo v1.0 &mdash; Offline Capable
            </div>
        </div>
    </div>
</body>
</html>
