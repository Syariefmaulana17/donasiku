<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FrontController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('status', 'active')->latest()->get();
        return view('welcome', compact('campaigns'));
    }

    public function checkout($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('checkout', compact('campaign'));
    }

    public function storeDonation(Request $request, $id)
    {
        $donation = Donation::create([
            'campaign_id' => $id,
            'donor_name' => $request->donor_name,
            'donor_email' => $request->donor_email,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        $midtrans = PaymentGateway::where('name', 'midtrans')->where('is_active', true)->first();

        if ($midtrans) {
            $serverKey = $midtrans->secret_key;
            
            $response = Http::withBasicAuth($serverKey, '')
                ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
                    'transaction_details' => [
                        'order_id' => 'DONASI-' . $donation->id . '-' . time(),
                        'gross_amount' => (int) $donation->amount,
                    ],
                    'customer_details' => [
                        'first_name' => $donation->donor_name,
                        'email' => $donation->donor_email ?: 'hamba_allah@email.com',
                    ]
                ]);

            if ($response->successful()) {
                // Langsung arahkan ke Midtrans
                return redirect($response->json('redirect_url'));
            }
        }

        // Kalau Midtrans sedang error/mati, kembali ke manual
        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('bukti-transfer', 'public');
            $donation->update(['proof_of_payment' => $proofPath]);
        }

        return redirect('/')->with('success', 'Alhamdulillah! Data dan bukti donasi Anda berhasil dicatat secara manual.');
    }

    // TUGAS BARU: Menerima laporan dari Midtrans (Webhook)
    public function paymentCallback(Request $request)
    {
        // 1. Ambil Server Key dari Brankas
        $serverKey = PaymentGateway::where('name', 'midtrans')->value('secret_key');
        
        // 2. Buat kunci verifikasi untuk memastikan ini benar-benar Midtrans yang melapor
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        // 3. Jika kuncinya cocok
        if ($hashed == $request->signature_key) {
            // Pecah Order ID untuk mendapatkan ID Donasi asli kita (Format: DONASI-ID-WAKTU)
            $orderIdParts = explode('-', $request->order_id);
            $donationId = $orderIdParts[1];
            
            $donation = Donation::find($donationId);
            
            if ($donation) {
                // Jika statusnya sukses (settlement/capture)
                if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                    $donation->update(['status' => 'success']);
                } 
                // Jika gagal/batal
                elseif (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $donation->update(['status' => 'failed']);
                }
            }
        }
        
        // Jawab Midtrans dengan senyuman agar mereka tahu laporannya sudah kita terima
        return response()->json(['message' => 'Laporan diterima, terima kasih!']);
    }
}