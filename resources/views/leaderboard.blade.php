<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AspiraX</title>

    <!-- Impor Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Impor Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Menambahkan font kustom */
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        .font-display {
            font-family: 'Times New Roman', Times, serif;
        }

        /* Animasi Masuk untuk Konten */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.7s ease-out forwards;
            opacity: 0;
            /* Mulai dalam keadaan tersembunyi */
        }

        /* Animasi kursor mengetik */
        .cursor {
            display: inline-block;
            width: 4px;
            background-color: #333;
            margin-left: 8px;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                background-color: #333;
            }

            49% {
                background-color: #333;
            }

            50% {
                background-color: transparent;
            }

            99% {
                background-color: transparent;
            }

            100% {
                background-color: #333;
            }
        }

        #globeViz canvas {
            outline: none;
        }

        /* Custom animation for horizontal scroll */
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-scrolling {
            animation: scroll 30s linear infinite;
        }

        /* Animation for tab content fade-in */
        .tab-content {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .tab-content.hidden {
            opacity: 0;
            transform: translateY(10px);
            position: absolute;
            /* Prevent layout shift */
            pointer-events: none;
        }

        .tab-content.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-white text-gray-900">
    <div id="app-wrapper">
        <header class="py-6 relative z-10 container mx-auto px-4">
            <nav class="flex justify-between items-center">
                <a href="/" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-black" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.5 12.5H8.51M11.5 12.5H11.51M14.5 12.5H14.51M4 21.8V5.2C4 4.0799 4 3.51984 4.21799 3.09202C4.40973 2.71569 4.71569 2.40973 5.09202 2.21799C5.51984 2 6.0799 2 7.2 2H16.8C17.9201 2 18.4802 2 18.908 2.21799C19.2843 2.40973 19.5903 2.71569 19.782 3.09202C20 3.51984 20 4.0799 20 5.2V14.8C20 15.9201 20 16.4802 19.782 16.908C19.5903 17.2843 19.2843 17.5903 18.908 17.782C18.4802 18 17.9201 18 16.8 18H8L4 21.8Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="font-bold text-xl text-black">AspiraX</span>
                </a>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden md:flex items-center space-x-4 md:space-x-6">
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-black transition-colors">
                        Leaderboard
                    </a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-black transition-colors">
                        Statistics
                    </a>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-black transition-colors">
                        Aspiro
                    </a>

                    <!-- MetaMask Login Button -->
                    <div>
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <button id="metamask-login-desktop" type="button"
                            data-signature-url="{{ url('/eth/signature') }}"
                            data-authenticate-url="{{ url('/eth/authenticate') }}"
                            data-redirect-url="{{ route('dashboard') }}"
                            class="text-sm font-semibold bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
                            🔗 Login with MetaMask
                        </button>
                        <div id="metamask-error-desktop"
                            class="hidden mt-4 p-2 text-sm text-red-600 bg-red-100 rounded"></div>
                    </div>
                </div>

                <!-- Hamburger Button (Mobile) -->
                <div class="md:hidden flex items-center">
                    <button id="hamburger-button" class="focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg rounded-lg mt-2 p-4 absolute right-4 w-56">
                <a href="#" class="block py-2 px-3 text-gray-600 hover:bg-gray-100 rounded">AspiroBot Edu</a>
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <button id="metamask-login-mobile" type="button" data-signature-url="{{ url('/eth/signature') }}"
                    data-authenticate-url="{{ url('/eth/authenticate') }}" data-redirect-url="{{ route('dashboard') }}"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition">
                    🔗 Login with MetaMask
                </button>
                <div id="metamask-error-mobile" class="hidden mt-4 p-2 text-sm text-red-600 bg-red-100 rounded"></div>

            </div>
        </header>

        <div class="container mx-auto px-4 py-8 md:py-12">
            <header class="mb-8 justify-center text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Peringkat Pengguna Teratas</h1>
                <p class="text-gray-500 mt-1">Lihat siapa yang memimpin papan peringkat bulan ini.</p>
            </header>

            <main>
                <!-- Bagian 3 Pengguna Teratas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                    <!-- Peringkat 2: Perak -->
                    <div
                        class="bg-white p-6 rounded-xl shadow-md border border-gray-200 order-2 md:order-1 lg:order-2 flex flex-col items-center text-center transform hover:-translate-y-2 transition-transform duration-300">
                        <div class="relative mb-4">
                            <img class="w-24 h-24 rounded-full object-cover border-4 border-gray-300"
                                src="https://placehold.co/100x100/E2E8F0/4A5568?text=P2" alt="Avatar Pengguna">
                            <span
                                class="absolute -bottom-2 -right-2 bg-gray-300 text-gray-700 w-10 h-10 rounded-full flex items-center justify-center text-xl font-bold border-4 border-white">2</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Mr. Pablo Moore</h3>
                        <p class="text-indigo-600 font-semibold text-lg">4,200 Poin</p>
                    </div>

                    <!-- Peringkat 1: Emas -->
                    <div
                        class="bg-white p-6 rounded-xl shadow-xl border-2 border-yellow-400 order-1 md:col-span-2 md:order-2 lg:col-span-1 lg:order-1 transform hover:scale-105 transition-transform duration-300 flex flex-col items-center text-center">
                        <div class="relative mb-4">
                            <img class="w-28 h-28 rounded-full object-cover border-4 border-yellow-400"
                                src="https://placehold.co/120x120/FBBF24/4A5568?text=P1" alt="Avatar Pengguna">
                            <!-- Ikon Mahkota Emas -->
                            <span class="absolute -top-5 text-4xl" style="transform: rotate(15deg);">👑</span>
                            <span
                                class="absolute -bottom-2 -right-2 bg-yellow-400 text-white w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold border-4 border-white">1</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Allen Prohaska PhD</h3>
                        <p class="text-indigo-600 font-semibold text-xl">4,550 Poin</p>
                    </div>

                    <!-- Peringkat 3: Perunggu -->
                    <div
                        class="bg-white p-6 rounded-xl shadow-md border border-gray-200 order-3 md:order-3 lg:order-3 flex flex-col items-center text-center transform hover:-translate-y-2 transition-transform duration-300">
                        <div class="relative mb-4">
                            <img class="w-24 h-24 rounded-full object-cover border-4 border-orange-300"
                                src="https://placehold.co/100x100/FED7AA/4A5568?text=P3" alt="Avatar Pengguna">
                            <span
                                class="absolute -bottom-2 -right-2 bg-orange-300 text-orange-800 w-10 h-10 rounded-full flex items-center justify-center text-xl font-bold border-4 border-white">3</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Jesus Marquardt</h3>
                        <p class="text-indigo-600 font-semibold text-lg">4,100 Poin</p>
                    </div>

                </div>

                <!-- Daftar Peringkat Selanjutnya -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <!-- Item Pengguna -->
                        <li
                            class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-500 w-6 text-center">4</span>
                                <img class="w-12 h-12 rounded-full object-cover"
                                    src="https://placehold.co/80x80/A5B4FC/3730A3?text=EP" alt="Avatar Pengguna">
                                <div>
                                    <p class="font-semibold text-gray-900">Eleanor Pena</p>
                                </div>
                            </div>
                            <span class="font-semibold text-indigo-600 text-lg">3,980 Poin</span>
                        </li>
                        <!-- Item Pengguna -->
                        <li
                            class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-500 w-6 text-center">5</span>
                                <img class="w-12 h-12 rounded-full object-cover"
                                    src="https://placehold.co/80x80/A7F3D0/047857?text=JB" alt="Avatar Pengguna">
                                <div>
                                    <p class="font-semibold text-gray-900">Jerome Bell</p>
                                </div>
                            </div>
                            <span class="font-semibold text-indigo-600 text-lg">3,750 Poin</span>
                        </li>
                        <!-- Item Pengguna -->
                        <li
                            class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-500 w-6 text-center">6</span>
                                <img class="w-12 h-12 rounded-full object-cover"
                                    src="https://placehold.co/80x80/FBCFE8/9D2463?text=BS" alt="Avatar Pengguna">
                                <div>
                                    <p class="font-semibold text-gray-900">Brooklyn Simmons</p>
                                </div>
                            </div>
                            <span class="font-semibold text-indigo-600 text-lg">3,500 Poin</span>
                        </li>
                        <!-- Item Pengguna -->
                        <li
                            class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-500 w-6 text-center">7</span>
                                <img class="w-12 h-12 rounded-full object-cover"
                                    src="https://placehold.co/80x80/FDE68A/92400E?text=EH" alt="Avatar Pengguna">
                                <div>
                                    <p class="font-semibold text-gray-900">Esther Howard</p>
                                </div>
                            </div>
                            <span class="font-semibold text-indigo-600 text-lg">3,210 Poin</span>
                        </li>
                        <!-- Item Pengguna -->
                        <li
                            class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-semibold text-gray-500 w-6 text-center">8</span>
                                <img class="w-12 h-12 rounded-full object-cover"
                                    src="https://placehold.co/80x80/BAE6FD/0C4A6E?text=RF" alt="Avatar Pengguna">
                                <div>
                                    <p class="font-semibold text-gray-900">Robert Fox</p>
                                </div>
                            </div>
                            <span class="font-semibold text-indigo-600 text-lg">3,150 Poin</span>
                        </li>
                    </ul>
                </div>
            </main>
        </div>
        <footer class="bg-black text-gray-300">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <a href="#" class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.5 12.5H8.51M11.5 12.5H11.51M14.5 12.5H14.51M4 21.8V5.2C4 4.0799 4 3.51984 4.21799 3.09202C4.40973 2.71569 4.71569 2.40973 5.09202 2.21799C5.51984 2 6.0799 2 7.2 2H16.8C17.9201 2 18.4802 2 18.908 2.21799C19.2843 2.40973 19.5903 2.71569 19.782 3.09202C20 3.51984 20 4.0799 20 5.2V14.8C20 15.9201 20 16.4802 19.782 16.908C19.5903 17.2843 19.2843 17.5903 18.908 17.782C18.4802 18 17.9201 18 16.8 18H8L4 21.8Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span class="font-bold text-xl text-white">AspiraX</span>
                        </a>
                        <p class="mt-4 max-w-md">
                            Bergabunglah dengan AspiraX dan jadilah bagian dari gerakan perubahan yang lebih besar.
                            Suara Anda adalah kekuatan untuk masa depan yang lebih baik.
                        </p>
                        <div class="mt-6 flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <span class="sr-only">YouTube</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.78 22 12 22 12s0 3.22-.42 4.814a2.506 2.506 0 0 1-1.768 1.768c-1.594.42-7.812.42-7.812.42s-6.218 0-7.812-.42a2.506 2.506 0 0 1-1.768-1.768C2 15.22 2 12 2 12s0-3.22.42-4.814a2.506 2.506 0 0 1 1.768-1.768C5.782 5 12 5 12 5s6.218 0 7.812.418ZM9.94 15.582V8.418L15.822 12 9.94 15.582Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>

                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold tracking-wider uppercase text-white">AspiraX</h3>
                        <ul class="mt-4 space-y-4">
                            <li><a href="#" class="hover:text-white">FAQ</a></li>
                            <li><a href="#" class="hover:text-white">Bantuan</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold tracking-wider uppercase text-white">Tentang Kami</h3>
                        <ul class="mt-4 space-y-4">
                            <li><a href="#" class="hover:text-white">Fitur</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-sm">
                    <p>&copy; 2025 AspiraX. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
