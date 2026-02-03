<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    // Tampilkan Form Verify (Lock Screen)
    public function showVerifyForm() {
        return view('auth.pin-verify');
    }

    // Cek PIN Benar/Salah
    public function verify(Request $request) {
        $request->validate(['pin' => 'required|numeric|digits:6']);

        $user = auth()->user();

        if (Hash::check($request->pin, $user->pin)) {
            // PIN Benar: Simpan status di session
            $request->session()->put('pin_verified', true);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['pin' => 'PIN salah! Coba lagi.']);
    }

    // Tampilkan Form Setup (Untuk User Baru)
    public function showSetupForm() {
        return view('auth.pin-setup');
    }

    // Simpan PIN Baru
    public function setup(Request $request) {
        $request->validate(['pin' => 'required|numeric|digits:6|confirmed']); // butuh field pin_confirmation

        $user = auth()->user();
        $user->update(['pin' => Hash::make($request->pin)]);

        // Auto verify setelah setup
        $request->session()->put('pin_verified', true);

        return redirect()->route('dashboard')->with('success', 'PIN Keamanan berhasil dibuat!');
    }
}
