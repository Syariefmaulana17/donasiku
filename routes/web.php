<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Livewire\CampaignDetail;
use App\Livewire\CampaignUpdates;
use App\Livewire\CampaignDonations;
use App\Livewire\CheckoutDonation;
use App\Livewire\PaymentInstruction;

Route::get('/', [FrontController::class, 'index']);
Route::get('/donasi/{id}', [FrontController::class, 'checkout']);

// JALUR BARU: Rute untuk memproses pengiriman data formulir (POST)
Route::post('/donasi/{id}', [FrontController::class, 'storeDonation']);
// JALUR API: Webhook untuk Payment Gateway (Kita hilangkan perlindungan CSRF khusus untuk rute ini)
Route::post('/api/payment/callback', [FrontController::class, 'paymentCallback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');
Route::get('/campaign/{slug}', CampaignDetail::class)->name('campaign.detail');
Route::get('/campaign/{slug}/updates', CampaignUpdates::class)->name('campaign.updates');
Route::get('/campaign/{slug}/donations', CampaignDonations::class)->name('campaign.donations');
Route::get('/campaign/{slug}/checkout', CheckoutDonation::class)->name('campaign.checkout');
Route::get('/payment/instruction/{id?}/{method?}', PaymentInstruction::class)->name('payment.instruction');
// Halaman Payment Success dengan Upselling
Route::get('/payment/success/{id}', function($id) {
    $donation = \App\Models\Donation::with('campaign')->findOrFail($id);
    
    // Ambil 3 program lain secara acak untuk upselling
    $otherCampaigns = \App\Models\Campaign::where('id', '!=', $donation->campaign_id)
                        ->where('is_active', true)
                        ->inRandomOrder()
                        ->take(3)
                        ->get();
                        
    return view('payment-success', compact('donation', 'otherCampaigns'));
})->name('payment.success');

// Halaman Cetak/Download Kwitansi
Route::get('/payment/receipt/{id}', function($id) {
    $donation = \App\Models\Donation::with('campaign')->findOrFail($id);
    return view('payment-receipt', compact('donation'));
})->name('payment.receipt');
// Rute untuk Halaman Web Dinamis
Route::get('/halaman/{slug}', function ($slug) {
    // Mencari halaman yang aktif berdasarkan slug
    $page = \App\Models\WebPage::where('slug', $slug)->where('is_active', true)->firstOrFail();

    // Menarik pengaturan warna navbar agar seragam dengan halaman depan
    $homeSetting = \App\Models\HomepageSetting::first();
    $navbarColor = $homeSetting->navbar_color ?? '#ffffff';

    return view('custom-page', compact('page', 'navbarColor'));
});