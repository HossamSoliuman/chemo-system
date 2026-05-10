<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OncoChemo') }} — Login</title>
    <script src="{{ asset('/lib/tailwind/tailwind.js') }}"></script>
    <script defer src="{{ asset('/lib/alpine/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('/lib/fontawesome/downloaded/css/all.min.css') }}">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .demo-box {
            background: #eff6ff;
            border: 1px dashed #93c5fd;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .demo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 9px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .demo-dot {
            width: 6px;
            height: 6px;
            background: #2563eb;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .3;
                transform: scale(.75);
            }
        }

        .demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
        }

        .demo-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #6b7280;
        }

        .demo-value {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: monospace;
            font-size: 12.5px;
            color: #1e3a5f;
        }

        .copy-btn {
            background: none;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            color: #6b7280;
            font-size: 10px;
            padding: 2px 7px;
            cursor: pointer;
            transition: all .15s;
        }

        .copy-btn:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        .copy-btn.copied {
            border-color: #16a34a;
            color: #16a34a;
        }

        .demo-fill-btn {
            width: 100%;
            background: transparent;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            color: #6b7280;
            font-size: 12px;
            padding: 7px;
            cursor: pointer;
            margin-top: 11px;
            transition: all .15s;
        }

        .demo-fill-btn:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }
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

            <!-- Demo Credentials Box -->
            <div class="demo-box">
                <div class="demo-badge">
                    <span class="demo-dot"></span>
                    Demo Access
                </div>
                <div class="demo-row">
                    <span class="demo-label">Email</span>
                    <span class="demo-value">
                        demo@oncochemo.local
                        <button class="copy-btn" onclick="copyText('demo@oncochemo.local', this)">copy</button>
                    </span>
                </div>
                <div class="demo-row" style="margin-top:8px">
                    <span class="demo-label">Password</span>
                    <span class="demo-value">
                        password
                        <button class="copy-btn" onclick="copyText('password', this)">copy</button>
                    </span>
                </div>
                <button class="demo-fill-btn" onclick="fillDemo()">⚡ Fill demo credentials</button>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="admin@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    <i class="fa-solid fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                OncoChemo v1.0 &mdash; Offline Capable
            </div>
        </div>
    </div>

    <script>
        function fillDemo() {
            document.getElementById('email').value = 'demo@oncochemo.local';
            document.getElementById('password').value = 'password';
            ['email', 'password'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.add('ring-2', 'ring-blue-400');
                setTimeout(() => el.classList.remove('ring-2', 'ring-blue-400'), 900);
            });
        }

        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                btn.textContent = '✓ copied';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'copy';
                    btn.classList.remove('copied');
                }, 1800);
            });
        }
    </script>
</body>

</html>
