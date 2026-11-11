<div class="bg-gray-100 min-h-screen font-sans text-gray-800 flex justify-center antialiased">
    
    <style>
        .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scroll-bar::-webkit-scrollbar { display: none; }
    </style>

    <!-- Kontainer Aplikasi -->
    <div class="w-full max-w-[480px] bg-gray-50 min-h-screen relative shadow-2xl pb-32 border-x border-gray-200">
        
        <!-- Header -->
        <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-4 shadow-sm">
            <a href="/campaign/{{ $campaign->slug }}" class="text-gray-600 hover:bg-gray-200 p-1.5 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-gray-800 text-[15px]">Masukkan Nominal Donasi</h1>
        </div>

        <!-- Ringkasan Campaign -->
        <div class="bg-white p-4 flex items-center gap-4 border-b border-gray-100 shadow-sm mb-2">
            <div class="w-16 h-12 bg-gray-200 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                @if($campaign->cover_image)
                    <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200"></div>
                @endif
            </div>
            <h2 class="font-bold text-gray-900 text-[13px] line-clamp-2 leading-tight">{{ $campaign->title }}</h2>
        </div>

        <div class="p-4 space-y-5">
            
            <!-- Box Input Nominal -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <label class="block font-bold text-gray-800 text-[14px] mb-4">Pilih Nominal Donasi</label>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button wire:click="setAmount(10000)" class="{{ $amount == 10000 ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:border-blue-300' }} border-2 font-bold py-3 rounded-xl text-[14px] transition">Rp 10.000</button>
                    <button wire:click="setAmount(20000)" class="{{ $amount == 20000 ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:border-blue-300' }} border-2 font-bold py-3 rounded-xl text-[14px] transition">Rp 20.000</button>
                    <button wire:click="setAmount(50000)" class="{{ $amount == 50000 ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:border-blue-300' }} border-2 font-bold py-3 rounded-xl text-[14px] transition">Rp 50.000</button>
                    <button wire:click="setAmount(100000)" class="{{ $amount == 100000 ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:border-blue-300' }} border-2 font-bold py-3 rounded-xl text-[14px] transition">Rp 100.000</button>
                </div>

                <!-- Input Nominal Fleksibel Anti Tumpang Tindih -->
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition">
                    <span class="pl-4 pr-2 font-bold text-gray-500 text-[15px]">Rp</span>
                    <input type="number" wire:model.live="amount" class="w-full bg-transparent text-gray-900 text-lg font-bold py-3 pr-4 outline-none placeholder:text-gray-400 placeholder:font-normal" placeholder="Nominal lainnya">
                </div>
                @error('amount') <span class="text-red-500 text-[11px] mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Box Profil Donatur & Doa (Lebih Longgar) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-5">
                <h3 class="font-bold text-gray-800 text-[14px]">Profil Donatur</h3>
                
                <!-- Input Nama -->
                <div>
                    <input type="text" wire:model="name" placeholder="Nama Lengkap (Opsional)" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-[13px] font-medium rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                <!-- Input Nomor Telepon (Wajib) -->
                <div>
                <input type="text" wire:model="phone" placeholder="Nomor WhatsApp / Telepon (Wajib)" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-[13px] font-medium rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    @error('phone') <span class="text-red-500 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Input Email (Opsional) -->
                <div>
                    <input type="email" wire:model="email" placeholder="Alamat Email (Opsional)" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-[13px] font-medium rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    <!-- Penampil pesan error jika format email salah -->
                    @error('email') <span class="text-red-500 text-[11px] mt-1.5 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <!-- Toggle Hamba Allah (Layout Flex Between) -->
                <label class="flex items-center justify-between cursor-pointer py-1">
                    <span class="text-[13px] font-bold text-gray-700">Sembunyikan nama saya (Hamba Allah)</span>
                    <div class="relative">
                        <input type="checkbox" wire:model="is_anonymous" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </div>
                </label>

                <!-- Input Doa -->
                <div>
                    <textarea wire:model="prayer" rows="3" placeholder="Tulis doa untuk penggalang dana atau dirimu sendiri (Opsional)" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none leading-relaxed"></textarea>
                </div>
            </div>

            <!-- Box Metode Pembayaran -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-800 text-[14px] mb-4">Metode Pembayaran</h3>
                
                <div class="space-y-3">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">E-Wallet & QRIS</div>
                    <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 transition {{ $payment_method == 'qris' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 bg-blue-100 rounded text-[11px] font-bold text-blue-700 flex items-center justify-center">QRIS</div>
                            <span class="text-[13px] font-bold text-gray-800">Gopay, OVO, Dana, LinkAja</span>
                        </div>
                        <input type="radio" wire:model="payment_method" value="qris" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 transition {{ $payment_method == 'shopeepay' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 bg-[#EE4D2D]/10 rounded text-[11px] font-bold text-[#EE4D2D] flex items-center justify-center">Shopee</div>
                            <span class="text-[13px] font-bold text-gray-800">ShopeePay</span>
                        </div>
                        <input type="radio" wire:model="payment_method" value="shopeepay" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    </label>

                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-5 mb-2 block">Transfer Virtual Account</div>
                    <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 transition {{ $payment_method == 'va_bca' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 bg-blue-900/10 rounded text-[11px] font-bold text-blue-900 flex items-center justify-center">BCA</div>
                            <span class="text-[13px] font-bold text-gray-800">BCA Virtual Account</span>
                        </div>
                        <input type="radio" wire:model="payment_method" value="va_bca" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 transition {{ $payment_method == 'va_mandiri' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 bg-yellow-500/10 rounded text-[11px] font-bold text-yellow-600 flex items-center justify-center">MANDIRI</div>
                            <span class="text-[13px] font-bold text-gray-800">Mandiri Virtual Account</span>
                        </div>
                        <input type="radio" wire:model="payment_method" value="va_mandiri" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between p-3.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 transition {{ $payment_method == 'va_bni' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-8 bg-orange-600/10 rounded text-[11px] font-bold text-orange-600 flex items-center justify-center">BNI</div>
                            <span class="text-[13px] font-bold text-gray-800">BNI Virtual Account</span>
                        </div>
                        <input type="radio" wire:model="payment_method" value="va_bni" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    </label>
                </div>
                @error('payment_method') <span class="text-red-500 text-[11px] mt-2 block font-medium">Pilih metode pembayaran terlebih dahulu.</span> @enderror
            </div>

            <!-- Bagian Cross-Selling -->
            <div class="mt-8 mb-4">
                <h3 class="font-bold text-gray-900 text-[14px] mb-4">Sekalian sedekah di program lain?</h3>
                <div class="flex overflow-x-auto gap-4 pb-4 snap-x snap-mandatory hide-scroll-bar -mx-4 px-4">
                    @foreach($otherCampaigns as $other)
                        <div wire:key="cross-{{ $other->id }}" class="shrink-0 w-[200px] snap-start bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col shadow-sm">
                            <div class="h-24 bg-gray-200 w-full shrink-0 relative">
                                @if($other->cover_image)
                                    <img src="{{ Storage::url($other->cover_image) }}" class="w-full h-full object-cover">
                                @endif
                                <div class="absolute inset-0 bg-black/20"></div>
                            </div>
                            <div class="p-3 flex flex-col flex-grow">
                                <h4 class="font-bold text-gray-800 text-[12px] line-clamp-2 mb-2">{{ $other->title }}</h4>
                                <div class="mt-auto">
                                    <div class="w-full bg-gray-100 rounded-full h-1 mb-1.5">
                                        @php $pct = $other->target_amount > 0 ? min(100, ($other->current_amount / $other->target_amount) * 100) : 0; @endphp
                                        <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-blue-600">Rp {{ number_format($other->current_amount ?? 0, 0, ',', '.') }}</span>
                                        <a href="/campaign/{{ $other->slug }}/checkout" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-2 py-1 rounded">Pilih</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Bar CTA -->
        <div class="fixed bottom-0 inset-x-0 mx-auto w-full max-w-[480px] z-50 bg-white border-t border-gray-200 p-4 shadow-[0_-5px_15px_rgba(0,0,0,0.08)]">
            <button wire:click="processPayment" class="w-full bg-[#E11D48] hover:bg-rose-700 text-white font-bold text-[15px] py-3.5 rounded-xl shadow-md transition-colors flex justify-between items-center px-5">
                <span>Lanjutkan Pembayaran</span>
                <span>Rp {{ number_format((int)$amount, 0, ',', '.') }}</span>
            </button>
        </div>

    </div>
</div>