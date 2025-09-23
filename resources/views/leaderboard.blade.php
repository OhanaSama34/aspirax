@extends('frontOfficeLayout.app')

@section('content')
        <div class="container mx-auto px-4 py-8 md:py-12">
            <header class="mb-8 justify-center text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-display">Peringkat Pengguna Teratas</h1>
                <p class="text-gray-500 mt-1">Lihat siapa yang memimpin papan peringkat bulan ini.</p>
            </header>

            <main>
                <!-- Bagian Top 3 Pengguna -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach ($leaderboard->take(3) as $index => $user)
                        @php
                            // Tentukan warna dan ukuran border sesuai peringkat
                            $borderColor = match($index) {
                                0 => 'border-yellow-400', // emas
                                1 => 'border-gray-300',   // perak
                                2 => 'border-orange-300', // perunggu
                            };
                            $avatarSize = ($index === 0) ? 'w-28 h-28' : 'w-24 h-24';
                            $textSize = ($index === 0) ? 'text-2xl' : 'text-xl';
                            $pointsSize = ($index === 0) ? 'text-xl' : 'text-lg';
                        @endphp
                        <div class="bg-white p-6 rounded-xl shadow-md {{ $index === 0 ? 'shadow-xl border-2' : 'border' }} {{ $borderColor }} 
                                    flex flex-col items-center text-center transform hover:-translate-y-2 transition-transform duration-300 {{ $index === 0 ? 'hover:scale-105' : '' }}">
                            <div class="relative mb-4">
                                <img class="{{ $avatarSize }} rounded-full object-cover border-4 {{ $borderColor }}"
                                     src="{{ $user->avatar ?? 'https://placehold.co/100x100/cccccc/333333?text=U' }}" 
                                     alt="Avatar {{ $user->name }}">
                                @if($index === 0)
                                    <span class="absolute -top-5 text-4xl" style="transform: rotate(15deg);">👑</span>
                                @endif
                                <span class="absolute -bottom-2 -right-2 bg-{{ match($index) {
                                    0 => 'yellow-400',
                                    1 => 'gray-300',
                                    2 => 'orange-300',
                                } }} text-{{ match($index) {
                                    0 => 'white',
                                    1 => 'gray-700',
                                    2 => 'orange-800',
                                } }} w-10 h-10 {{ $index === 0 ? 'w-12 h-12 text-2xl' : '' }} rounded-full flex items-center justify-center text-xl font-bold border-4 border-white">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <h3 class="font-bold {{ $textSize }} text-gray-900 truncate w-40">
                                {{ $user->name }}
                            </h3>
                            
                            <p class="text-indigo-600 font-semibold {{ $pointsSize }}">{{ number_format($user->point) }} Poin</p>
                        </div>
                    @endforeach
                </div>
            
                <!-- Daftar Peringkat Selanjutnya -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @foreach ($leaderboard->slice(3) as $user)
                            <li class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center space-x-4">
                                    <span class="text-lg font-semibold text-gray-500 w-6 text-center">
                                        {{ $loop->iteration + 3 }}
                                    </span>
                                    <img class="w-12 h-12 rounded-full object-cover"
                                        src="{{ $user->avatar ?? 'https://placehold.co/80x80/cccccc/333333?text=U' }}" 
                                        alt="Avatar {{ $user->name }}">
                                    <div>
                                        <p class="font-semibold text-gray-900 truncate w-40">{{ $user->name }}</p>
                                    </div>
                                </div>
                                <span class="font-semibold text-indigo-600 text-lg">{{ number_format($user->point) }} Poin</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </main>
            
        </div>
        
@endsection