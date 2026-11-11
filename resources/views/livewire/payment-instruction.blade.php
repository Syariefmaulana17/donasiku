<div class="bg-gray-100 min-h-screen font-sans text-gray-800 flex justify-center antialiased relative overflow-hidden" wire:poll.3s="checkPaymentStatus">
    
    <!-- Ornamen Latar Belakang (Blob Kiri & Kanan atas) -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-orange-100/50 rounded-full blur-3xl translate-x-1/3 -translate-y-1/3"></div>

    <!-- Kontainer Utama -->
    <div class="w-full max-w-[480px] bg-white/80 backdrop-blur-md min-h-screen relative shadow-2xl border-x border-gray-200 z-10 pb-20">
        
        <!-- Header (Tombol Back) -->
        <div class="px-4 py-4 flex items-center relative z-20">
            <a href="/" class="text-gray-600 hover:bg-gray-200 p-2 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </div>

        <!-- Bagian Logo & Pesan Terima Kasih -->
        <div class="px-6 text-center mt-2 mb-8">
            <div class="w-24 h-24 mx-auto bg-white rounded-full shadow-md flex items-center justify-center p-2 mb-4 border border-gray-100">
                <!-- Placeholder Logo -->
                <div class="w-full h-full rounded-full border-2 border-blue-500 flex flex-col items-center justify-center text-blue-600">
                    <span class="font-extrabold text-sm leading-tight">DonasiKu</span>
                </div>
            </div>
            
            <p class="text-[13px] text-gray-700 leading-relaxed font-medium">
                Terima kasih Orang Baik atas donasi yang akan Anda berikan pada program kebaikan kami.
            </p>
        </div>

        <!-- Kartu Utama -->
        <div class="px-5 space-y-4">
            
            <!-- Kartu Nomor Rekening / VA -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex items-center justify-between gap-3" x-data="{ copied: false }">
                <div class="flex items-center gap-3.5 min-w-0">
                    @if($bank_logo)
                        <div class="w-14 h-10 bg-slate-50 border border-gray-100 rounded-lg p-1 flex items-center justify-center shrink-0">
                            <img src="{{ Storage::url($bank_logo) }}" class="max-h-full max-w-full object-contain" alt="{{ $bank_name }}">
                        </div>
                    @else
                        <div class="w-14 shrink-0 text-center font-bold text-[13px] text-blue-900 uppercase bg-blue-50 py-2 px-1 rounded-lg truncate">
                            {{ $bank_name }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-blue-600 uppercase">{{ $bank_name }}</div>
                        <div class="font-extrabold text-gray-900 text-lg tracking-wide truncate">{{ $va_number }}</div>
                        <div class="text-[11px] text-gray-500 font-medium truncate">A.n. {{ $account_name }}</div>
                    </div>
                </div>
                <button @click="navigator.clipboard.writeText('{{ $va_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="flex items-center gap-1.5 text-[10px] font-bold transition-colors uppercase outline-none shrink-0 bg-gray-50 hover:bg-blue-50 px-3 py-2 rounded-lg border border-gray-200"
                        :class="copied ? 'text-green-600 border-green-300 bg-green-50' : 'text-gray-600 hover:text-blue-600'">
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <svg x-show="copied" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                </button>
            </div>

            <!-- Kartu Nominal -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex items-center justify-between" x-data="{ copied: false }">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-pink-50 rounded-full flex items-center justify-center text-pink-500 ml-2 shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div>
                        <!-- Menggunakan format number standar -->
                        <div class="font-extrabold text-gray-900 text-xl tracking-tight">Rp {{ number_format((int)$amount, 0, ',', '.') }}</div>
                    </div>
                </div>
                <button @click="navigator.clipboard.writeText('{{ $amount }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="flex items-center gap-1.5 text-[10px] font-bold transition-colors uppercase outline-none"
                        :class="copied ? 'text-green-600' : 'text-gray-500 hover:text-blue-600'">
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <svg x-show="copied" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="copied ? 'Tersalin' : 'Copy'"></span>
                </button>
            </div>

            <p class="text-center text-[10px] font-bold text-gray-900 my-4 italic px-2">
                Harap transfer sesuai nominal diatas agar dapat terkonfirmasi otomatis dan kebaikan ini dapat kami teruskan.
            </p>

            <!-- Form Upload Bukti Transfer -->
            @if($is_success)
                <!-- Pesan Sukses Jika Berhasil Upload -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center mt-6 shadow-sm">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="font-bold text-green-800 text-[16px]">Bukti Berhasil Dikirim!</h3>
                    <p class="text-[13px] text-green-700 mt-2 font-medium">Alhamdulillah. Admin kami akan segera memverifikasi donasi Anda. Terima kasih, Orang Baik!</p>
                    <a href="/" class="mt-5 inline-block w-full bg-green-600 text-white font-bold py-3.5 px-6 rounded-xl text-[14px] hover:bg-green-700 transition">Kembali ke Beranda</a>
                </div>
            @else
                <!-- Area Upload Bukti Transfer Estetik -->
                <div class="mt-6 p-5 border border-gray-200 rounded-xl bg-white shadow-sm">
                    <label class="block font-bold text-gray-800 text-[14px] mb-3">Upload Bukti Transfer</label>
                    
                    <!-- Kotak Tombol Custom -->
                    <div class="relative flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-blue-300 rounded-xl cursor-pointer bg-blue-50/50 hover:bg-blue-50 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-1 text-[13px] text-gray-600 font-medium"><span class="font-bold text-blue-600">Klik untuk pilih gambar</span></p>
                                <p class="text-[11px] text-gray-400">Maksimal ukuran file 2MB (JPG, PNG)</p>
                            </div>
                            <!-- Input asli disembunyikan pakai class "hidden" -->
                            <input id="dropzone-file" type="file" wire:model="proof_of_payment" accept="image/*" class="hidden" />
                        </label>
                    </div>

                    <!-- Preview Gambar yang Dipilih (Otomatis muncul) -->
                    @if ($proof_of_payment)
                        <div class="mt-4 p-2 bg-green-50 rounded-lg border border-green-200 flex items-center gap-3">
                            <img src="{{ $proof_of_payment->temporaryUrl() }}" class="w-12 h-12 object-cover rounded-md border border-gray-200">
                            <div class="text-[12px] font-bold text-green-700 flex-1 truncate">
                                Gambar siap diunggah!
                            </div>
                            <button type="button" wire:click="$set('proof_of_payment', null)" class="text-red-500 p-1.5 hover:bg-red-100 rounded-md transition" title="Batal Pilih">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif

                    <!-- Pesan Error -->
                    @error('proof_of_payment') <span class="text-red-500 text-[11px] font-bold block mt-2">{{ $message }}</span> @enderror
                    
                    <!-- Tombol Konfirmasi dengan Efek Loading -->
                    <button wire:click="submitProof" wire:loading.attr="disabled" class="w-full bg-[#1F2937] hover:bg-gray-900 disabled:bg-gray-400 text-white font-bold text-[14px] py-3.5 rounded-xl shadow-md transition-colors mt-4 flex justify-center items-center">
                        <span wire:loading.remove wire:target="submitProof">Konfirmasi Pembayaran</span>
                        <span wire:loading wire:target="submitProof">⏳ Sedang Mengunggah...</span>
                    </button>
                </div>

                <!-- Timer / Batas Waktu (Sekarang hanya ada 1 kotak) -->
                <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 flex items-center justify-center gap-4 mt-5 text-center shadow-sm">
                    <div class="text-orange-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-left">
                        <div class="text-[12px] text-gray-600 font-medium">Transfer sebelum :</div>
                        <div class="font-bold text-gray-900 text-[13px]">{{ $deadline_formatted }}</div>
                    </div>
                </div>
            @endif
            
        </div>

        <!-- Instruksi Pembayaran (Accordion Model Minimalis) -->
        <div class="px-5 mt-10">
            <h3 class="text-center font-extrabold text-gray-900 text-[15px] mb-4">Instruksi Pembayaran</h3>
            
            <div class="space-y-3">
                <!-- Menu 1 -->
                <div class="bg-gray-100 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 font-bold text-[13px] text-gray-800 outline-none">
                        M-Banking
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" :class="open ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <div x-show="open" class="px-4 pb-4 text-[12px] text-gray-600 space-y-2">
                        <p>1. Login ke aplikasi M-Banking Anda.</p>
                        <p>2. Pilih menu Transfer > Virtual Account.</p>
                        <p>3. Masukkan nomor Virtual Account di atas.</p>
                        <p>4. Masukkan nominal transfer.</p>
                        <p>5. Konfirmasi dengan PIN Anda.</p>
                    </div>
                </div>

                <!-- Menu 2 -->
                <div class="bg-gray-100 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 font-bold text-[13px] text-gray-800 outline-none">
                        ATM
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" :class="open ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <div x-show="open" class="px-4 pb-4 text-[12px] text-gray-600 space-y-2">
                        <p>1. Masukkan kartu ATM dan PIN.</p>
                        <p>2. Pilih menu Transaksi Lainnya > Transfer > Ke Rekening Virtual Account.</p>
                        <p>3. Masukkan nomor VA.</p>
                        <p>4. Pastikan data sesuai lalu tekan Ya.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>