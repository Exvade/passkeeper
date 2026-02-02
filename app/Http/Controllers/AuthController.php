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

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                ]
            );

            Auth::login($user);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login gagal, coba lagi.');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}