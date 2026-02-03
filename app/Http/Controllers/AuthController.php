<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account']) // <--- INI KUNCINYA
            ->redirect();
    }

    public function callback() {
        try {
            $googleUser = Socialite::driver('google')->user();

            // --- SKENARIO 1: USER SUDAH LOGIN (Mau Nambah Akun) ---
            if (Auth::check()) {
                $currentUser = Auth::user();

                // Cek: Apakah google id ini sudah dipakai orang lain?
                $existingAccount = \App\Models\SocialAccount::where('provider_id', $googleUser->getId())->first();
                
                if ($existingAccount) {
                    // Kalau ternyata ini akun dia sendiri, ya sudah biarkan
                    if ($existingAccount->user_id == $currentUser->id) {
                        return redirect()->route('dashboard')->with('success', 'Akun ini sudah terhubung kok.');
                    }
                    // Kalau punya orang lain, tolak!
                    return redirect()->route('dashboard')->with('error', 'Gagal! Akun Google ini sudah dipakai user lain.');
                }

                // Link-kan akun baru ini ke user yang sedang login
                $currentUser->socialAccounts()->create([
                    'provider_id' => $googleUser->getId(),
                    'provider_name' => 'google',
                    'email' => $googleUser->getEmail(),
                ]);

                return redirect()->route('dashboard')->with('success', 'Akun Google baru berhasil ditambahkan!');
            }

            // --- SKENARIO 2: USER BELUM LOGIN (Login Biasa) ---
            // (Kode lama kamu paste di sini, tidak berubah)
            $account = \App\Models\SocialAccount::where('provider_id', $googleUser->getId())->first();

            if ($account) {
                Auth::login($account->user);
            } else {
                $user = \App\Models\User::where('email', $googleUser->getEmail())->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'email' => $googleUser->getEmail(),
                        'name' => $googleUser->getName(),
                        // Password null
                    ]);
                }
                $user->socialAccounts()->create([
                    'provider_id' => $googleUser->getId(),
                    'provider_name' => 'google',
                    'email' => $googleUser->getEmail(),
                ]);
                Auth::login($user);
            }

            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}