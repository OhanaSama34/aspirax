@extends('frontOfficeLayout.app')

@section('content')
   <div class="min-h-screen flex flex-col items-center justify-center p-4">

        <!-- Kontainer utama untuk konten -->
        <main class="w-full max-w-4xl mx-auto flex flex-col items-center text-center">

            <!-- Orb/Logo di bagian atas -->
            <div class="mb-8 w-24 h-24 rounded-full bg-gradient-to-br from-pink-500 to-green-400 p-1 shadow-lg shadow-pink-500/30">
                 <div class="w-full h-full bg-white rounded-full flex items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-pink-500 to-green-400 opacity-80 blur-md"></div>
                 </div>
            </div>

            <h1 class="text-5xl md:text-6xl font-bold mb-3">
                Hi there,<p class="truncate w-96 text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-green-400 ">@guest User @endguest @auth {{ Auth::user()->name }} @endauth</p>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-green-400 mb-12">
                Ready to Discuss with AI?
            </h2>

            <!-- Grid Kartu Fitur -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mb-12">
                <!-- Kartu 1: Video -->
                <div class="bg-white rounded-2xl p-6 text-left shadow-lg hover:shadow-xl hover:shadow-pink-500/20 transition-shadow duration-300">
                    <div class="w-10 h-10 mb-4 rounded-lg bg-gray-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-white mb-1">Create Cinematic 4K Videos</h3>
                    <p class="text-slate-400 text-sm">AI-generated. Studio quality. Lightning fast.</p>
                </div>

                <!-- Kartu 2: Musik -->
                <div class="bg-white rounded-2xl p-6 text-left shadow-lg hover:shadow-xl hover:shadow-green-500/20 transition-shadow duration-300">
                    <div class="w-10 h-10 mb-4 rounded-lg bg-gray-50 flex items-center justify-center">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 6l12-3" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-white mb-1">Make Music, Voiceovers & FX</h3>
                    <p class="text-slate-400 text-sm">Compose with AI. Your sound, your style.</p>
                </div>

                <!-- Kartu 3: Asisten AI -->
                <div class="bg-white rounded-2xl p-6 text-left shadow-lg hover:shadow-xl hover:shadow-pink-500/20 transition-shadow duration-300">
                    <div class="w-10 h-10 mb-4 rounded-lg bg-gray-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-white mb-1">Ask AI Assistant</h3>
                    <p class="text-slate-400 text-sm">Get insights, automate work, brainstorm ideas.</p>
                </div>
            </div>

            <!-- Input Bar di bagian bawah -->
            <div class="w-full max-w-2xl relative">
                <input type="text" placeholder="Ask anything" class="w-full py-4 px-6 rounded-full bg-white text-black placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-shadow duration-300 shadow-md pl-28 pr-24">
            
                                <!-- Opsi di dalam input (kiri) -->
                <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                    <button class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors py-1.5 px-3 rounded-full hover:bg-gray-50">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">Tools</span>
                    </button>
                </div>
                <!-- Ikon di dalam input (kanan) -->
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
                    <button class="text-slate-400 hover:text-white transition-colors p-2 rounded-full hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                    </button>
                    <button class="bg-gradient-to-br from-pink-500 to-green-500 text-white rounded-full p-2 hover:opacity-90 transition-opacity">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </button>
                </div>
            </div>
        </main>

    </div>


@endsection