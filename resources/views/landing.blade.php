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
                <a href="#" class="flex items-center space-x-2">
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

        <!-- Main Content -->
        <main class="min-h-screen flex flex-col items-center justify-center text-center container mx-auto px-4 -mt-22">

            <h1 id="headline" class="font-display font-bold text-5xl md:text-6xl lg:text-7xl leading-tight"
                style="min-height: 168px;"></h1>

            <p class="mt-6 max-w-xl text-gray-600 animate-fadeInUp" style="animation-delay: 0.3s;">
                Sampaikan pemikiran dan kritik Anda dengan bebas tanpa rasa khawatir. Bagikan suara Anda, picu perubahan
                yang berarti
            </p>

            <p class="font-display font-semibold text-2xl md:text-3xl mt-8 animate-fadeInUp"
                style="animation-delay: 0.4s;">
                #KritikAsikTanpaTerusik
            </p>

            <a href="#"
                class="mt-10 inline-flex items-center gap-2 bg-black text-white font-semibold rounded-lg px-8 py-3 hover:bg-gray-800 transition-all duration-300 transform hover:scale-105 animate-bounce"
                style="animation-delay: 0.5s;">
                <svg class="w-5 h-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M6 4l12 6-12 6V4z" />
                </svg>
                Start Your Opinion
            </a>
        </main>

        <section class="w-full py-16 sm:py-20 md:py-24">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-10">
                    <div class="flex flex-col justify-between space-y-10">
                        <div class="flex justify-between items-start pt-4 border-t border-gray-300">
                            <h1
                                class="text-3xl font-display sm:text-4xl md:text-5xl font-bold text-gray-800 uppercase tracking-widest leading-tight">
                                Why Choose Us? <br> Cause Your Voices <br> Matter
                            </h1>
                            <span class="hidden sm:block text-xs font-medium text-gray-500 whitespace-nowrap pt-2">(01)
                                About Us</span>
                        </div>
                        <div class="w-3/5">
                            <img src="{{ asset('storage/images/tuntutan.png') }}" alt="tuntutan-terbaru"
                                class="w-full h-auto ">
                        </div>
                    </div>
                    <!-- Kolom Kanan -->
                    <div class="relative flex flex-col justify-between pt-4 border-t border-gray-300">
                        <div class="lg:pl-8">
                            <p
                                class="text-sm sm:text-base text-gray-600 leading-relaxed max-w-md uppercase tracking-wider">
                                AspiraX hadir untuk mengembalikan kendali aspirasi kepada masyarakat.
                                Lewat teknologi Web3 dan AI, anda akan menyampaikan kritik secara anonim tanpa harus
                                khawatir data anda disalahgunakan oleh tangan tidak bertanggungjawab.
                                Kami percaya masa depan dibangun bukan hanya oleh para pemimpin,
                                tapi oleh suara rakyat yang tak pernah padam.
                            </p>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center lg:justify-end -z-10 opacity-60">
                            <svg class="w-2/3 sm:w-1/2 lg:w-3/4 h-auto text-gray-200" viewBox="0 0 100 100"
                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 0 L100 50 L50 100 L50 75 L25 75 L25 25 L50 25 Z" />
                            </svg>
                        </div>
                        <div class="text-right mt-10 lg:mt-0">
                            <span class="text-xs sm:text-sm font-medium text-gray-500">Build With Spirit<br> From
                                Students To Civilization</span>
                        </div>
                        <span class="sm:hidden text-xs font-medium text-gray-500 mt-8">(01) About Us</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-black">
            <div class="container mx-auto px-4 my-20 text-center">
                <h2 class="font-display text-4xl font-bold mb-20 text-white">Fitur Unggulan Kami</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                    <div class="flex flex-col items-center">
                        <div class="bg-blue-100 p-4 rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-white">Aman & Anonim</h3>
                        <p class="text-gray-400">Identitas Anda terlindungi dengan teknologi Web3 dan Kriptografi</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-purple-100 p-4 rounded-full mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-white">Terfiltrasi</h3>
                        <p class="text-gray-400">Setiap aspirasi akan diverifikasi dan terfilter agar berfokus pada
                            tujuan yang jelas.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-pink-100 p-4 rounded-full mb-4">
                            <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2V7a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-white">Aspiro</h3>
                        <p class="text-gray-400">Chat Bot untuk mendukung dan membantu anda menyampaikan aspirasi Anda.
                        </p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-green-100 p-4 rounded-full mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-green-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                            </svg>

                        </div>
                        <h3 class="text-xl font-bold mb-2 text-white">Leaderboard</h3>
                        <p class="text-gray-400">Tingkatkan partisipasi dan capai leaderboard tertinggi</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section id="vision-mission" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="font-display text-4xl font-bold mb-4 text-gray-800">Visi & Misi Kami</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Membangun masa depan demokrasi yang lebih transparan,
                        aman, dan partisipatif untuk Indonesia.</p>
                </div>
                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div
                        class="bg-white p-8 rounded-2xl shadow-lg flex flex-col items-center text-center outline outline-2 outline-gray-200">
                        <div class="bg-pink-100 p-4 rounded-full mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>

                        </div>
                        <h3 class="font-display text-3xl font-bold text-gray-800 mb-4">Visi</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Menjadi platform aspirasi digital terdepan yang memberdayakan setiap suara untuk menciptakan
                            perubahan positif dan berkelanjutan di Indonesia, di mana setiap kritik adalah aset untuk
                            kemajuan bersama.
                        </p>
                    </div>
                    <div
                        class="bg-white p-8 rounded-2xl shadow-lg flex flex-col items-center text-center outline outline-2 outline-gray-200">
                        <div class="bg-green-100 p-4 rounded-full mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 7.5-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5m0 2.25-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                            </svg>

                        </div>
                        <h3 class="font-display text-3xl font-bold text-gray-800 mb-4">Misi</h3>
                        <ul class="text-gray-600 leading-relaxed space-y-3 text-left list-disc pl-5">
                            <li>Menyediakan platform yang aman dan anonim dengan memanfaatkan teknologi Web3.</li>
                            <li>Memfasilitasi dialog yang konstruktif dan transparan antara masyarakat dengan para
                                pemangku kebijakan.</li>
                            <li>Mengedukasi publik mengenai pentingnya partisipasi sipil yang cerdas dan berdampak.</li>
                            <li>Menjunjung tinggi integritas dan transparansi dalam setiap proses penyampaian aspirasi.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Inserted Gallery Section -->
        <section class="w-full bg-black text-white flex flex-col overflow-hidden">
            <div class="w-full h-[40vh] md:h-[50vh] whitespace-nowrap overflow-hidden">
                <div class="h-full w-max flex animate-scrolling">
                    <img src="{{ asset('storage/images/Demo-Mahasiswa-Duduki-Gedung-MPR-Mei-1998.jpg') }}" alt="Demonstrasi publik di Indonesia"
                        alt="Demonstrasi publik di Indonesia" class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/buruh.jpg') }}" alt="Barisan aparat kepolisian"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/aparat.jpg') }}" alt="Menyuarakan opini dengan megafon"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/Aksi-Kamisan-ke-815-160524-Bay-5.jpg') }}" alt="Kepalan tangan sebagai simbol perlawanan"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/1330134_720.jpg') }}" alt="Mahasiswa melakukan aksi protes"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/images (2).jpg') }}" alt="Aparat dalam formasi"
                        class="h-full w-auto object-cover grayscale">

                    <img src="{{ asset('storage/images/Demo-Mahasiswa-Duduki-Gedung-MPR-Mei-1998.jpg') }}" alt="Demonstrasi publik di Indonesia"
                        alt="Demonstrasi publik di Indonesia" class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/buruh.jpg') }}" alt="Barisan aparat kepolisian"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/aparat.jpg') }}" alt="Menyuarakan opini dengan megafon"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/Aksi-Kamisan-ke-815-160524-Bay-5.jpg') }}" alt="Kepalan tangan sebagai simbol perlawanan"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/1330134_720.jpg') }}" alt="Mahasiswa melakukan aksi protes"
                        class="h-full w-auto object-cover grayscale">
                    <img src="{{ asset('storage/images/images (2).jpg') }}" alt="Aparat dalam formasi"
                        class="h-full w-auto object-cover grayscale">
                </div>
            </div>

            <div
                class="flex-grow flex flex-col md:flex-row items-center justify-center md:justify-between p-8 md:p-16 lg:p-24 relative">
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 md:left-0 md:translate-x-0 w-11/12 md:w-full h-[1px] bg-gray-700">
                </div>
                <div class="text-center md:text-left md:w-3/5 lg:w-1/2 mb-12 md:mb-0">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold  leading-tight mb-4 font-display">
                        Galeri Perjuangan Rakyat
                    </h1>
                    <p class="text-base md:text-lg text-gray-400 max-w-xl">
                        Lihat bagaimana aspirasi dan kritik membentuk perubahan di Indonesia.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="#"
                        class="group w-36 h-36 md:w-40 md:h-40 rounded-full border border-gray-500 flex items-center justify-center text-center transition-all duration-300 ease-in-out hover:bg-pink-300 hover:border-pink-600">
                        <span
                            class="text-sm font-medium uppercase tracking-wider text-white transition-colors duration-300 group-hover:text-green-600 group-hover:font-bold">
                            Jelajahi <br> Galeri
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="relative bg-black flex flex-col items-center justify-center text-white overflow-hidden p-6 py-20">
            <div class="z-10 flex flex-col items-center w-full max-w-6xl mx-auto">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight font-display">Bumi Yang Subur, Masalah Yang Makmur</h1>
                    <p class="mt-4 max-w-3xl mx-auto text-lg text-gray-300">
                        Kekayaan alam melimpah tidak otomatis membuat bangsa sejahtera, bila korupsi dan salah kelola terus dibiarkan.
                    </p>
                </div>
                <div class="mt-12 mb-10 w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-700">
                    <img src="{{ asset('storage/images/kabinet.jpg') }}"
                        alt="Foto kabinet pemerintahan Indonesia di tangga istana negara"
                        class="w-full h-auto object-cover">
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 w-full max-w-5xl text-center">
                    <div class="stat-item">
                        <span class="text-4xl md:text-5xl font-bold">17.000+</span>
                        <span class="block mt-2 text-sm text-gray-400 uppercase tracking-wider">Pulau</span>
                    </div>
                    <div class="stat-item">
                        <span class="text-4xl md:text-5xl font-bold">100+</span>
                        <span class="block mt-2 text-sm text-gray-400 uppercase tracking-wider">Pejabat Tinggi</span>
                    </div>
                    <div class="stat-item">
                        <span class="text-4xl md:text-5xl font-bold">2.500+</span>
                        <span class="block mt-2 text-sm text-gray-400 uppercase tracking-wider">Kasus Korupsi</span>
                    </div>
                    <div class="stat-item">
                        <span class="text-4xl md:text-5xl font-bold">9.105 T</span>
                        <span class="block mt-2 text-sm text-gray-400 uppercase tracking-wider">Hutang Negara</span>
                    </div>
                    <div class="hidden md:block stat-item">
                        <span class="text-4xl md:text-5xl font-bold">?</span>
                        <span class="block mt-2 text-sm text-gray-400 uppercase tracking-wider">Masa Depan</span>
                    </div>
                </div>
                <p class="mt-12 text-center italic text-gray-400 max-w-3xl">
                    "Kita seolah-olah merayakan demokrasi, tetapi memotong lidah orang-orang yang berani menyatakan pendapat mereka yang merugikan pemerintah."
                </p>
                <button
                    class="mt-8 px-8 py-3 bg-white text-gray-900 font-bold rounded-full hover:bg-gray-200 transition-colors duration-300">
                    Discover News
                </button>
            </div>
            <div class="absolute bottom-0 left-0 w-full bg-black/30 backdrop-blur-sm py-4 overflow-hidden">
                <div class="flex animate-scrolling">
                    <div class="flex-shrink-0 flex items-center w-full justify-around text-xl font-bold text-gray-200">
                        <span class="mx-8">Ibu Pertiwi</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Nusantara</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Negeri Seribu Pulau</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Negeri Zamrud Khatulistiwa</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Macan Asia Yang Tertidur</span> <span class="text-gray-500">*</span>
                    </div>
                    <div class="flex-shrink-0 flex items-center w-full justify-around text-xl font-bold text-gray-200">
                        <span class="mx-8">Ibu Pertiwi</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Nusantara</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Negeri Seribu Pulau</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Negeri Zamrud Khatulistiwa</span> <span class="text-gray-500">*</span>
                        <span class="mx-8">Macan Asia Yang Tertidur</span> <span class="text-gray-500">*</span>
                    </div>
                </div>
            </div>
        </section>
        
        {{-- <!-- Globe Section -->
        <section id="globe-section"
            class="relative py-20 bg-gray-800 text-white overflow-hidden min-h-[600px] flex flex-col justify-center">
            <div id="globeViz" class="absolute top-0 left-0 w-full h-full z-0 opacity-70"></div>
            <div class="container mx-auto px-4 text-center relative z-10">
                <h2 class="font-display text-4xl font-bold mb-4">Jangkauan Luas</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Lihat bagaimana aspirasi serupa disuarakan di seluruh Indonesia bahkan dunia, menghubungkan ide-ide
                    untuk perubahan
                    global secara real-time.
                </p>
            </div>
        </section> --}}

        <!-- ===== Bagian Footer ===== -->
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

    @vite(['resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // JavaScript untuk handle klik menu hamburger
            const hamburgerButton = document.getElementById('hamburger-button');
            const mobileMenu = document.getElementById('mobile-menu');

            hamburgerButton.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileMenu.classList.toggle('hidden');
            });

            // Menutup menu jika mengklik di luar
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !hamburgerButton.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });

            // JavaScript untuk animasi mengetik
            const headlineEl = document.getElementById('headline');
            if (headlineEl) {
                const textToType = '"Kritik Asik Tanpa <br> Harus Terusik"';
                const content = document.createElement('span');
                headlineEl.appendChild(content);

                const cursor = document.createElement('span');
                cursor.className = 'cursor';
                cursor.style.height = getComputedStyle(headlineEl).fontSize;
                headlineEl.appendChild(cursor);

                let textIndex = 0;
                const typingSpeed = 100;
                const erasingSpeed = 50;
                const delayBeforeErase = 2000;
                const delayBeforeTyping = 500;

                function typeWriter() {
                    if (textIndex < textToType.length) {
                        if (textToType.substring(textIndex, textIndex + 4) === '<br>') {
                            content.innerHTML += '<br>';
                            textIndex += 4;
                        } else {
                            content.innerHTML += textToType.charAt(textIndex);
                            textIndex++;
                        }
                        setTimeout(typeWriter, typingSpeed);
                    } else {
                        // Selesai mengetik, tunggu lalu mulai hapus
                        setTimeout(eraseText, delayBeforeErase);
                    }
                }

                function eraseText() {
                    if (content.innerHTML.length > 0) {
                        // Handle tag <br> yang panjangnya 4 karakter
                        if (content.innerHTML.slice(-4) === '<br>') {
                            content.innerHTML = content.innerHTML.slice(0, -4);
                        } else {
                            content.innerHTML = content.innerHTML.slice(0, -1);
                        }
                        setTimeout(eraseText, erasingSpeed);
                    } else {
                        // Selesai menghapus, reset index dan mulai mengetik lagi
                        textIndex = 0;
                        setTimeout(typeWriter, delayBeforeTyping);
                    }
                }
                setTimeout(typeWriter, delayBeforeTyping); // Mulai animasi
            }

            // JavaScript for How It Works Tabs
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');

            const activeColors = {
                '#step1': {
                    bg: 'bg-gray-500',
                    text: 'text-white',
                    shadow: 'shadow-md'
                },
                '#step2': {
                    bg: 'bg-green-600',
                    text: 'text-white',
                    shadow: 'shadow-md'
                },
                '#step3': {
                    bg: 'bg-pink-600',
                    text: 'text-white',
                    shadow: 'shadow-md'
                }
            };

            const defaultClasses = ['hover:bg-gray-200', 'text-gray-700'];

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.dataset.tabTarget;
                    const targetContent = document.querySelector(targetId);

                    tabs.forEach(t => {
                        const id = t.dataset.tabTarget;
                        const colors = activeColors[id];
                        t.classList.remove(colors.bg, colors.text, colors.shadow);
                        t.classList.add(...defaultClasses);
                    });

                    const activeStyle = activeColors[targetId];
                    tab.classList.remove(...defaultClasses);
                    tab.classList.add(activeStyle.bg, activeStyle.text, activeStyle.shadow);

                    contents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    });

                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                        setTimeout(() => {
                            targetContent.classList.add('active');
                        }, 10);
                    }
                });
            });
        });
    </script>

    <script src="//cdn.jsdelivr.net/npm/globe.gl"></script>
    <script type="module">
        import {
            csvParseRows
        } from 'https://esm.sh/d3-dsv';
        import indexBy from 'https://esm.sh/index-array-by';

        const COUNTRY = 'Indonesia';
        const MAP_CENTER = {
            lat: -2.5,
            lng: 118.0,
            altitude: 0.8
        };
        const OPACITY = 0.3;

        const globeSection = document.getElementById('globe-section');
        if (globeSection) {
            const myGlobe = Globe()(document.getElementById('globeViz'))
                .globeImageUrl('//cdn.jsdelivr.net/npm/three-globe/example/img/earth-night.jpg')
                .arcLabel(d => `${d.airline}: ${d.srcIata} &#8594; ${d.dstIata}`)
                .arcStartLat(d => d.srcAirport.lat)
                .arcStartLng(d => d.srcAirport.lng)
                .arcEndLat(d => d.dstAirport.lat)
                .arcEndLng(d => d.dstAirport.lng)
                .arcColor(d => [`rgba(0, 255, 0, ${OPACITY})`, `rgba(255, 0, 0, ${OPACITY})`])
                .arcDashLength(0.4)
                .arcDashGap(0.2)
                .arcDashAnimateTime(1500)
                .onArcHover(hoverArc => myGlobe
                    .arcColor(d => {
                        const op = !hoverArc ? OPACITY : d === hoverArc ? 0.9 : OPACITY / 4;
                        return [`rgba(0, 255, 0, ${op})`, `rgba(255, 0, 0, ${op})`];
                    })
                )
                .pointColor(() => 'orange')
                .pointAltitude(0)
                .pointRadius(0.04)
                .pointsMerge(true);

            // load data
            const airportParse = ([airportId, name, city, country, iata, icao, lat, lng, alt, timezone, dst, tz, type,
                source
            ]) => ({
                airportId,
                name,
                city,
                country,
                iata,
                icao,
                lat,
                lng,
                alt,
                timezone,
                dst,
                tz,
                type,
                source
            });
            const routeParse = ([airline, airlineId, srcIata, srcAirportId, dstIata, dstAirportId, codeshare, stops,
                equipment
            ]) => ({
                airline,
                airlineId,
                srcIata,
                srcAirportId,
                dstIata,
                dstAirportId,
                codeshare,
                stops,
                equipment
            });

            Promise.all([
                fetch('https://raw.githubusercontent.com/jpatokal/openflights/master/data/airports.dat').then(res =>
                    res.text())
                .then(d => csvParseRows(d, airportParse)),
                fetch('https://raw.githubusercontent.com/jpatokal/openflights/master/data/routes.dat').then(res =>
                    res.text())
                .then(d => csvParseRows(d, routeParse))
            ]).then(([airports, routes]) => {

                const filteredAirports = airports.filter(d => d.country === COUNTRY);
                const byIata = indexBy(filteredAirports, 'iata', false);

                const filteredRoutes = routes
                    .filter(d => byIata.hasOwnProperty(d.srcIata) && byIata.hasOwnProperty(d
                        .dstIata)) // exclude unknown airports
                    .filter(d => d.stops === '0') // non-stop flights only
                    .map(d => Object.assign(d, {
                        srcAirport: byIata[d.srcIata],
                        dstAirport: byIata[d.dstIata]
                    }))
                    .filter(d => d.srcAirport.country === COUNTRY && d.dstAirport.country ===
                        COUNTRY); // domestic routes within country

                myGlobe
                    .pointsData(filteredAirports)
                    .arcsData(filteredRoutes)
                    .pointOfView(MAP_CENTER, 4000);
            });
        }
    </script>
</body>

</html>
