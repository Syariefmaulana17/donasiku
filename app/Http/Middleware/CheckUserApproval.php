<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserApproval
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login, TAPI belum disetujui (is_approved == false)
        if (Auth::check() && !Auth::user()->is_approved) {
            // 1. Keluarkan (Logout) user tersebut
            Auth::logout();

            // 2. Tampilkan halaman cantik "Menunggu Persetujuan"
            return response()->view('waiting-approval');
        }

        return $next($request);
    }
}