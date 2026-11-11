@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $configuredBanks = ($homeSetting && !empty($homeSetting->footer_banks) && is_array($homeSetting->footer_banks))
        ? $homeSetting->footer_banks
        : [];

    $nominalChoices = [10000, 20000, 50000, 100000];
    if (!empty($campaign->nominal_choices) && is_array($campaign->nominal_choices)) {
        $extracted = collect($campaign->nominal_choices)->pluck('nominal')->filter()->map(fn($v) => (int)$v)->values()->all();
        if (count($extracted) > 0) {
            $nominalChoices = $extracted;
        }
    }
    $allowManual = $campaign->allow_manual_nominal ?? true;
    $percent = $campaign->target_amount > 0 ? min(100, round(($campaign->current_amount / $campaign->target_amount) * 100)) : 0;
@endphp

<div class="min-h-screen font-sans text-slate-800 pb-28 lg:pb-16">
    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 md:px-8 h-16 flex items-center gap-4">
            <a href="/campaign/{{ $campaign->slug }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="font-extrabold text-slate-900 text-sm md:text-base">Sempurnakan Niat Baik Anda</h1>
                <p class="text-[11px] text-slate-400 hidden sm:block">{{ $campaign->title }}</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 md:px-8 pt-6 md:pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- KOLOM KIRI (7 KOLOM DI DESKTOP): FORM CHECKOUT -->
            <div class="lg:col-span-7 space-y-5">

                <!-- Ringkasan Campaign Mobile (< lg) -->
                <div class="lg:hidden bg-white p-4 rounded-2xl flex items-center gap-4 border border-slate-200/80 shadow-sm">
                    <div class="w-16 h-12 bg-slate-200 rounded-xl overflow-hidden shrink-0">
                        @if($campaign->cover_image)
                            <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <h2 class="font-bold text-slate-900 text-xs line-clamp-2 leading-snug">{{ $campaign->title }}</h2>
                </div>

                <!-- 1. Pilih Nominal Donasi -->
                <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <label class="block font-extrabold text-slate-900 text-sm md:text-base mb-4">1. Pilih Nominal Donasi</label>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                        @foreach($nominalChoices as $nom)
                            <button
                                type="button"
                                wire:click="setAmount({{ $nom }})"
                                class="{{ $amount == $nom ? 'bg-blue-50 border-brand-primary text-brand-primary ring-1 ring-brand-primary' : 'bg-white border-slate-200 text-slate-700 hover:border-brand-primary' }} border-2 font-extrabold py-3 px-3 rounded-2xl text-xs md:text-sm transition"
                            >
                                Rp {{ number_format($nom, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>

                    @if($allowManual)
                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-brand-primary/30 focus-within:border-brand-primary transition">
                            <span class="pl-4 pr-2 font-extrabold text-slate-500 text-sm md:text-base">Rp</span>
                            <input
                                type="number"
                                wire:model.live="amount"
                                class="w-full bg-transparent text-slate-900 text-lg font-extrabold py-3.5 pr-4 outline-none placeholder:text-slate-400 placeholder:font-normal"
                                placeholder="Masukkan nominal lainnya"
                            >
                        </div>
                    @endif
                    @error('amount') <span class="text-rose-600 text-xs mt-2 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- 2. Profil Donatur & Doa -->
                <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm md:text-base">2. Data Donatur & Doa</h3>
                    
                    <div>
                        <input type="text" wire:model="name" placeholder="Nama Lengkap (Opsional)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs md:text-sm font-medium rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-brand-primary/30 focus:border-brand-primary outline-none transition">
                    </div>

                    <div>
                        <input type="text" wire:model="phone" placeholder="Nomor WhatsApp Aktif (Wajib, cth: 08123456789)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs md:text-sm font-medium rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-brand-primary/30 focus:border-brand-primary outline-none transition">
                        @error('phone') <span class="text-rose-600 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <input type="email" wire:model="email" placeholder="Alamat Email (Opsional)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs md:text-sm font-medium rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-brand-primary/30 focus:border-brand-primary outline-none transition">
                        @error('email') <span class="text-rose-600 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                    
                    <label class="flex items-center justify-between cursor-pointer py-1.5 px-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-xs md:text-sm font-bold text-slate-700">Sembunyikan nama saya (Hamba Allah)</span>
                        <div class="relative">
                            <input type="checkbox" wire:model="is_anonymous" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                        </div>
                    </label>

                    <div>
                        <textarea wire:model="prayer" rows="3" placeholder="Tulis doa terbaik Anda untuk program ini (Opsional)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs md:text-sm rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-brand-primary/30 focus:border-brand-primary outline-none transition resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- 3. Metode Pembayaran -->
                <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-sm md:text-base mb-4">3. Pilih Metode Pembayaran</h3>
                    
                    <div class="space-y-2.5">
                        @if(count($configuredBanks) > 0)
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Transfer Rekening Resmi</div>
                            @foreach($configuredBanks as $bank)
                                @php
                                    $bankMethodValue = $bank['bank_name'] ?? ('Bank ' . ($loop->index + 1));
                                @endphp
                                <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:bg-blue-50/50 transition {{ $payment_method === $bankMethodValue ? 'border-brand-primary bg-blue-50/60 ring-1 ring-brand-primary' : 'border-slate-200' }}">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        @if(!empty($bank['logo']))
                                            <div class="w-14 h-10 bg-white border border-slate-100 rounded-xl p-1 flex items-center justify-center shrink-0">
                                                <img src="{{ Storage::url($bank['logo']) }}" class="max-h-full max-w-full object-contain" alt="{{ $bankMethodValue }}">
                                            </div>
                                        @else
                                            <div class="w-14 h-10 bg-blue-900/10 rounded-xl text-xs font-extrabold text-blue-900 flex items-center justify-center shrink-0 uppercase px-1 text-center truncate">
                                                {{ $bankMethodValue }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="text-xs md:text-sm font-bold text-slate-800 block truncate">Transfer {{ $bankMethodValue }}</span>
                                            <span class="text-[11px] text-slate-500 block truncate">{{ $bank['account_number'] ?? '' }} — A.n. {{ $bank['account_name'] ?? '' }}</span>
                                        </div>
                                    </div>
                                    <input type="radio" wire:model.live="payment_method" value="{{ $bankMethodValue }}" class="w-4 h-4 text-brand-primary focus:ring-brand-primary shrink-0">
                                </label>
                            @endforeach
                        @else
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pembayaran Instan (E-Wallet & QRIS)</div>
                            <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:bg-blue-50/50 transition {{ $payment_method == 'qris' ? 'border-brand-primary bg-blue-50/60 ring-1 ring-brand-primary' : 'border-slate-200' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-9 bg-blue-100 rounded-xl text-xs font-extrabold text-blue-700 flex items-center justify-center">QRIS</div>
                                    <div>
                                        <span class="text-xs md:text-sm font-bold text-slate-800 block">QRIS Semua E-Wallet & Mobile Banking</span>
                                        <span class="text-[11px] text-slate-400">GoPay, OVO, DANA, ShopeePay, LinkAja</span>
                                    </div>
                                </div>
                                <input type="radio" wire:model.live="payment_method" value="qris" class="w-4 h-4 text-brand-primary focus:ring-brand-primary">
                            </label>

                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider pt-3 mb-1 block">Transfer Bank / Virtual Account</div>
                            <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:bg-blue-50/50 transition {{ $payment_method == 'va_bca' ? 'border-brand-primary bg-blue-50/60 ring-1 ring-brand-primary' : 'border-slate-200' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-9 bg-blue-900/10 rounded-xl text-xs font-extrabold text-blue-900 flex items-center justify-center">BCA</div>
                                    <span class="text-xs md:text-sm font-bold text-slate-800">Transfer / VA BCA</span>
                                </div>
                                <input type="radio" wire:model.live="payment_method" value="va_bca" class="w-4 h-4 text-brand-primary focus:ring-brand-primary">
                            </label>

                            <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:bg-blue-50/50 transition {{ $payment_method == 'va_mandiri' ? 'border-brand-primary bg-blue-50/60 ring-1 ring-brand-primary' : 'border-slate-200' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-9 bg-amber-500/10 rounded-xl text-xs font-extrabold text-amber-600 flex items-center justify-center">MANDIRI</div>
                                    <span class="text-xs md:text-sm font-bold text-slate-800">Transfer / VA Mandiri</span>
                                </div>
                                <input type="radio" wire:model.live="payment_method" value="va_mandiri" class="w-4 h-4 text-brand-primary focus:ring-brand-primary">
                            </label>

                            <label class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:bg-blue-50/50 transition {{ $payment_method == 'va_bni' ? 'border-brand-primary bg-blue-50/60 ring-1 ring-brand-primary' : 'border-slate-200' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-9 bg-orange-600/10 rounded-xl text-xs font-extrabold text-orange-600 flex items-center justify-center">BNI</div>
                                    <span class="text-xs md:text-sm font-bold text-slate-800">Transfer / VA BNI</span>
                                </div>
                                <input type="radio" wire:model.live="payment_method" value="va_bni" class="w-4 h-4 text-brand-primary focus:ring-brand-primary">
                            </label>
                        @endif
                    </div>
                    @error('payment_method') <span class="text-rose-600 text-xs mt-2 block font-semibold">Pilih metode pembayaran terlebih dahulu.</span> @enderror
                </div>
            </div>

            <!-- KOLOM KANAN (5 KOLOM STICKY DI DESKTOP): RINGKASAN DONASI -->
            <aside class="lg:col-span-5 lg:sticky lg:top-24 space-y-5">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                    <h3 class="font-extrabold text-slate-900 text-base mb-4">Ringkasan Donasi</h3>

                    <div class="flex gap-3.5 items-center pb-4 mb-4 border-b border-slate-100">
                        <div class="w-20 h-16 rounded-2xl bg-slate-100 overflow-hidden shrink-0">
                            @if($campaign->cover_image)
                                <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-slate-900 text-xs md:text-sm line-clamp-2">{{ $campaign->title }}</h4>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $campaign->user->name ?? 'DonasiKu' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs md:text-sm mb-6">
                        <div class="flex justify-between text-slate-500">
                            <span>Nominal Donasi</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format((int)$amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Metode Pembayaran</span>
                            <span class="font-bold text-slate-800 uppercase">{{ $payment_method ? str_replace('va_', 'VA ', $payment_method) : 'Belum dipilih' }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                            <span class="font-bold text-slate-900">Total Pembayaran</span>
                            <span class="text-xl font-extrabold text-brand-primary">Rp {{ number_format((int)$amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button wire:click="processPayment" class="hidden lg:flex w-full bg-[#E11D48] hover:bg-rose-700 text-white font-extrabold text-sm py-4 rounded-2xl shadow-lg shadow-rose-500/25 transition justify-center items-center gap-2">
                        <span>Lanjutkan Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <!-- Cross-Selling -->
                @if($otherCampaigns->count() > 0)
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                        <h3 class="font-extrabold text-slate-900 text-sm mb-3">Sekalian sedekah di program lain?</h3>
                        <div class="space-y-3">
                            @foreach($otherCampaigns as $other)
                                <div wire:key="cross-{{ $other->id }}" class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-50 transition">
                                    <div class="w-16 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                        @if($other->cover_image)
                                            <img src="{{ Storage::url($other->cover_image) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $other->title }}</h4>
                                        <span class="text-[11px] font-bold text-brand-primary">Rp {{ number_format($other->current_amount ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="/campaign/{{ $other->slug }}/checkout" class="text-[11px] bg-slate-100 hover:bg-brand-primary hover:text-white text-slate-700 font-bold px-3 py-1.5 rounded-xl transition shrink-0">Pilih</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

        </div>
    </main>

    <!-- Sticky Bottom CTA (Mobile < lg) -->
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 p-4 shadow-[0_-8px_20px_rgba(0,0,0,0.06)]">
        <button wire:click="processPayment" class="w-full bg-[#E11D48] hover:bg-rose-700 text-white font-extrabold text-sm py-3.5 rounded-xl shadow-md transition flex justify-between items-center px-5">
            <span>Lanjutkan Pembayaran</span>
            <span>Rp {{ number_format((int)$amount, 0, ',', '.') }}</span>
        </button>
    </div>
</div>
