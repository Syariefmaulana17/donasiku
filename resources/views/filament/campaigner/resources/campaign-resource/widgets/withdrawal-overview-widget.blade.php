<x-filament-widgets::widget>
    <x-filament::section class="mb-8">
        <div style="display: flex; flex-direction: column; gap: 1rem;">

            <!-- Baris Total Raihan -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(128, 128, 128, 0.2); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-weight: 600; font-size: 1rem; margin: 0;">Total Raihan Donasi</h3>
                    <p style="opacity: 0.6; font-size: 0.75rem; margin: 0; padding-top: 0.25rem;">Keseluruhan donasi yang berhasil masuk</p>
                </div>
                <div style="font-size: 1.125rem; font-weight: 700;">
                    Rp {{ number_format($totalDonasi, 0, ',', '.') }}
                </div>
            </div>

            <!-- Baris Biaya Platform -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(128, 128, 128, 0.2); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-weight: 600; font-size: 1rem; margin: 0;">Biaya Platform (10%)</h3>
                    <p style="opacity: 0.6; font-size: 0.75rem; margin: 0; padding-top: 0.25rem;">Biaya pemeliharaan sistem dan operasional</p>
                </div>
                <div style="font-size: 1rem; font-weight: 600; color: #ef4444;">
                    - Rp {{ number_format($biayaPlatform, 0, ',', '.') }}
                </div>
            </div>

            <!-- Baris Dana Telah Dicairkan -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(128, 128, 128, 0.2); padding-bottom: 1rem;">
                <div>
                    <h3 style="font-weight: 600; font-size: 1rem; margin: 0;">Dana yang sudah dicairkan</h3>
                    <p style="opacity: 0.6; font-size: 0.75rem; margin: 0; padding-top: 0.25rem;">Total penarikan yang telah disetujui/selesai</p>
                </div>
                <div style="font-size: 1rem; font-weight: 600; color: #ef4444;">
                    - Rp {{ number_format($totalDicairkan, 0, ',', '.') }}
                </div>
            </div>

            <!-- Baris Saldo Tersedia -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #3b82f6;">
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <h3 style="font-weight: 700; font-size: 1.125rem; margin: 0;">Dana yang dapat dicairkan</h3>
                </div>
                <div style="font-size: 1.5rem; font-weight: 900; color: #3b82f6;">
                    Rp {{ number_format($sisaSaldo, 0, ',', '.') }}
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>