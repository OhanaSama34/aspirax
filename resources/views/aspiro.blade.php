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
                Hi there, <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-green-400">Jidan</span>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-green-400 mb-12">
                Ready to Discuss with AI?
            </h2>

            
            <!-- Toolbar -->
            <div class="w-full max-w-2xl mb-3 flex justify-end">
                <button id="aspiro-new" class="text-sm px-3 py-1.5 rounded-full border bg-white hover:bg-gray-50 text-black">Chat Baru</button>
            </div>
            <!-- Chat output moved above input -->
            <div id="aspiro-output" class="w-full max-w-2xl mb-4 text-left space-y-3"></div>

            <!-- Input Bar -->
            <div class="w-full max-w-2xl relative" id="aspiro-chat">
                <input id="aspiro-input" type="text" placeholder="Tanyakan isu politik terkini" class="w-full py-4 px-6 rounded-full bg-white text-black placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-shadow duration-300 shadow-md pl-28 pr-24">
            
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
                    <button id="aspiro-send" class="bg-gradient-to-br from-pink-500 to-green-500 text-white rounded-full p-2 hover:opacity-90 transition-opacity">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </button>
                </div>
            </div>
            
        </main>

    </div>


@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('aspiro-input');
    const sendBtn = document.getElementById('aspiro-send');
    const output = document.getElementById('aspiro-output');
    const newBtn = document.getElementById('aspiro-new');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function renderTextAsHtml(str) {
        // Preserve newlines; no markdown, avoid HTML injection
        return escapeHtml(str).replace(/\n/g, '<br>');
    }

    function showIntro() {
        if (!output) return;
        // Only show if empty (new visit or reset)
        if (output.children.length === 0) {
            const intro = document.createElement('div');
            intro.className = 'p-3 rounded-xl bg-white border text-black';
            intro.innerHTML = renderTextAsHtml(
                'Halo, saya Aspiro. Saya asisten netral untuk berdiskusi isu politik terkini.\n'
            );
            output.appendChild(intro);
        }
    }

    async function sendMessage() {
        const text = (input.value || '').trim();
        if (!text) return;
        const userBubble = document.createElement('div');
        userBubble.className = 'p-3 rounded-xl bg-gray-100 text-black';
        userBubble.textContent = text;
        output.appendChild(userBubble);
        input.value = '';
        const loading = document.createElement('div');
        loading.className = 'text-sm text-slate-500';
        loading.textContent = 'Aspiro mengetik…';
        output.appendChild(loading);
        // Disable send while in-flight
        const previousDisabled = sendBtn.disabled;
        sendBtn.disabled = true;
        const previousOpacity = sendBtn.style.opacity;
        sendBtn.style.opacity = '0.7';
        try {
            const res = await fetch('/aspiro/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ message: text }),
            });
            const data = await res.json();
            output.removeChild(loading);
            if (data && data.blocked) {
                const warn = document.createElement('div');
                warn.className = 'p-3 rounded-xl bg-red-50 text-red-700';
                warn.textContent = 'Diblokir: ' + (data.reasons || []).join(', ');
                output.appendChild(warn);
                output.scrollTop = output.scrollHeight;
                return;
            }
            const botBubble = document.createElement('div');
            botBubble.className = 'p-3 rounded-xl bg-white border text-black';
            botBubble.innerHTML = (data && data.reply) ? renderTextAsHtml(data.reply) : '(tidak ada balasan)';
            output.appendChild(botBubble);
            output.scrollTop = output.scrollHeight;
        } catch (e) {
            output.removeChild(loading);
            const err = document.createElement('div');
            err.className = 'p-3 rounded-xl bg-red-50 text-red-700';
            err.textContent = 'Gagal mengirim pesan.';
            output.appendChild(err);
        } finally {
            sendBtn.disabled = previousDisabled;
            sendBtn.style.opacity = previousOpacity;
        }
    }

    sendBtn?.addEventListener('click', sendMessage);
    input?.addEventListener('keydown', (ev) => {
        if (ev.key === 'Enter' && !ev.shiftKey) {
            ev.preventDefault();
            sendMessage();
        }
    });

    newBtn?.addEventListener('click', async () => {
        try {
            await fetch('/aspiro/reset', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: '{}'
            });
        } catch (e) { /* ignore */ }
        // Clear UI
        output.innerHTML = '';
        input.value = '';
        input.focus();
        showIntro();
    });

    // Intro on first load
    showIntro();
});
</script>