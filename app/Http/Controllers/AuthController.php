<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    public function callback() {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cek apakah Akun Google ini sudah ada di tabel social_accounts?
            $account = \App\Models\SocialAccount::where('provider_id', $googleUser->getId())
                                              ->where('provider_name', 'google')
                                              ->first();

            if ($account) {
                // KASUS A: Akun sudah terhubung -> Login User pemiliknya
                Auth::login($account->user);
            } else {
                // KASUS B: Akun belum terhubung.
                
                // Cek apakah emailnya sudah ada di tabel users? (Mungkin login manual atau linked)
                $user = \App\Models\User::where('email', $googleUser->getEmail())->first();

                if (!$user) {
                    // Kalau user juga belum ada -> Buat User Baru
                    $user = \App\Models\User::create([
                        'email' => $googleUser->getEmail(),
                        'name' => $googleUser->getName(),
                        // Password null karena login via google
                    ]);
                }

                // Link-kan akun google ini ke user tersebut
                $user->socialAccounts()->create([
                    'provider_id' => $googleUser->getId(),
                    'provider_name' => 'google',
                    'email' => $googleUser->getEmail(),
                ]);

                Auth::login($user);
            }

            // Redirect (Nanti akan dicegat Middleware PIN)
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}