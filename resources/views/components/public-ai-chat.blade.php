<div
    x-data="{
        open: false,
        loading: false,
        message: '',
        messages: [
            {
                from: 'ai',
                text: 'Halo, saya Asisten SILAPAK. Saya bisa membantu menjelaskan cara membuat pengaduan, tracking laporan, OTP, bukti pendukung, dan alur layanan. Jangan tuliskan NIK, alamat lengkap, nomor WhatsApp, atau kronologi detail di chat ini.'
            }
        ],
        async send() {
            const text = this.message.trim();
            if (!text || this.loading) return;

            this.messages.push({ from: 'user', text });
            this.message = '';
            this.loading = true;

            try {
                const response = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();
                const reply = data.reply || 'Maaf, Asisten SILAPAK AI sedang tidak tersedia. Silakan gunakan menu FAQ, halaman tracking, atau hubungi petugas melalui WhatsApp.';
                this.messages.push({ from: 'ai', text: reply });
            } catch (error) {
                this.messages.push({ from: 'ai', text: 'Maaf, Asisten SILAPAK AI sedang tidak tersedia. Silakan gunakan menu FAQ, halaman tracking, atau hubungi petugas melalui WhatsApp.' });
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    const box = this.$refs.messages;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        }
    }"
    class="fixed bottom-5 right-5 z-[9999] flex flex-col items-end font-outfit"
>
    <div x-show="open" x-cloak class="absolute bottom-full right-0 mb-4 w-[calc(100vw-2.5rem)] max-w-sm overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-2xl shadow-slate-900/20">
        <div class="bg-slate-950 p-4 text-white">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.18em] text-orange-300">SILAPAK AI</p>
                    <h2 class="mt-1 text-lg font-black">Asisten Pengaduan Publik</h2>
                </div>
                <button type="button" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" @click="open = false" aria-label="Tutup chatbot">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-300">AI hanya memberi panduan umum. Untuk laporan resmi, gunakan form pengaduan SILAPAK.</p>
        </div>

        <div x-ref="messages" class="max-h-80 space-y-3 overflow-y-auto bg-[#fffaf3] p-4">
            <template x-for="(item, index) in messages" :key="index">
                <div class="flex" :class="item.from === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-theme-xs" :class="item.from === 'user' ? 'bg-orange-500 text-white' : 'bg-white text-slate-700 ring-1 ring-orange-100'">
                        <p x-text="item.text"></p>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-500 ring-1 ring-orange-100">Asisten sedang mengetik...</div>
            </div>
        </div>

        <form class="border-t border-orange-100 bg-white p-3" @submit.prevent="send">
            <div class="flex gap-2">
                <input
                    x-model="message"
                    maxlength="1000"
                    class="min-w-0 flex-1 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    placeholder="Tulis pertanyaan singkat..."
                >
                <button type="submit" class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/25 hover:bg-orange-600 disabled:opacity-60" :disabled="loading || !message.trim()" aria-label="Kirim pesan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <p class="mt-2 text-[11px] leading-4 text-slate-500">Jangan kirim NIK, alamat lengkap, nomor WhatsApp, atau kronologi detail di chat.</p>
        </form>
    </div>

    <button type="button" class="flex h-14 w-14 items-center justify-center gap-3 rounded-full bg-orange-500 p-0 font-black text-white shadow-2xl shadow-orange-500/30 transition hover:-translate-y-1 hover:bg-orange-600 sm:h-auto sm:w-auto sm:px-5 sm:py-4" @click="open = !open" aria-label="Buka Tanya SILAPAK AI">
        <span class="grid h-9 w-9 place-items-center rounded-full bg-white/20"><i class="fa-solid fa-robot"></i></span>
        <span class="hidden sm:inline">Tanya SILAPAK AI</span>
    </button>
</div>
