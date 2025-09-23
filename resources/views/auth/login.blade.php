<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased text-gray-800 bg-white">
    <!-- Main container -->
    <div class="relative flex flex-col items-center justify-center min-h-screen p-4 overflow-hidden">

        <!-- Login Card -->
        <div class="relative w-full max-w-4xl bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">

                <!-- Left Column: Login Form -->
                <div class="w-full lg:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                    <div class="text-center lg:text-left">
                        <!-- Logo Placeholder -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">AspiraX</h1>
                        <h2 class="text-xl font-medium text-gray-900 mb-4">Masuk ke Akun Wallet Web3 Anda</h2>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Selamat datang, Suaramu merupakan penentu masa depan. Masuk menggunakan MetaMask untuk
                            melanjutkan.
                        </p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 p-3 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- MetaMask Login Button -->
                    <div>
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <button id="metamask-login-page" type="button"
                            data-signature-url="{{ url('/eth/signature') }}"
                            data-authenticate-url="{{ url('/eth/authenticate') }}"
                            data-redirect-url="{{ route('dashboard') }}"
                            class="text-sm font-semibold bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-full shadow-sm transition">
                            🔗 Login with MetaMask
                        </button>
                        <div id="metamask-error-page"
                            class="hidden mt-4 p-2 text-sm text-red-600 bg-red-100 rounded"></div>
                    </div>

                    <p class="text-center text-xs text-gray-500 mt-8">
                        Dengan melanjutkan, Anda menyetujui Syarat & Ketentuan kami.
                    </p>
                </div>

                <!-- Right Column: Image & Description -->
                <div class="hidden lg:flex w-1/2 p-12 flex-col justify-center items-center bg-gray-50 rounded-r-2xl">
                    <!-- Thematic SVG Illustration -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-32 h-32 text-gray-500 mb-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                    </svg>


                    <div class="text-center mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Pantau Perkembangan Di Indonesia</h3>
                        <p class="text-gray-600 max-w-sm">
                            Setelah masuk, kamu bisa bersuara tanpa takut datamu disalahgunakan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
