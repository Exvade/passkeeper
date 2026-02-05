<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google (Login & Linking).
     */
    public function handleProviderCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // Cari apakah Google ID ini sudah ada di database
            $existingSocialAccount = SocialAccount::where('provider_id', $googleUser->getId())
                                                  ->where('provider_name', 'google')
                                                  ->first();

            // ---------------------------------------------------------
            // SKENARIO 1: USER SUDAH LOGIN (FITUR LINKING AKUN)
            // ---------------------------------------------------------
            if (Auth::check()) {
                $currentUser = Auth::user();

                // Cek Konflik: Akun Google sudah ada, TAPI punya orang lain
                if ($existingSocialAccount && $existingSocialAccount->user_id != $currentUser->id) {
                    return redirect()->route('dashboard')
                        ->with('error', 'Gagal! Akun Google ini sudah terdaftar di brankas lain.');
                }

                // Cek Redudansi: Akun Google sudah ada di akun ini sendiri (User iseng klik 2x)
                if ($existingSocialAccount && $existingSocialAccount->user_id == $currentUser->id) {
                    return redirect()->route('dashboard')
                        ->with('success', 'Akun Google ini sudah terhubung sebelumnya.');
                }

                // Jika Aman: Buat Link Baru
                SocialAccount::create([
                    'user_id'       => $currentUser->id,
                    'provider_id'   => $googleUser->getId(),
                    'provider_name' => 'google',
                    'email'         => $googleUser->getEmail(), // Simpan email buat display di UI
                ]);

                return redirect()->route('dashboard')
                    ->with('success', 'Berhasil! Akun Google baru telah ditambahkan.');
            }

            // ---------------------------------------------------------
            // SKENARIO 2: USER BELUM LOGIN (LOGIN / REGISTER)
            // ---------------------------------------------------------
            
            // Jika akun Google sudah terdaftar -> Login langsung
            if ($existingSocialAccount) {
                Auth::login($existingSocialAccount->user);
                return $this->redirectAfterLogin();
            }

            // Jika belum ada Google Account, cek apakah Email-nya sudah dipakai di User biasa?
            $user = User::where('email', $googleUser->getEmail())->first();

            // Jika User belum ada sama sekali -> Register Baru
            if (!$user) {
                // Gunakan Transaction biar kalau gagal, gak ada data sampah
                DB::transaction(function () use (&$user, $googleUser) {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        // Password random karena login pake sosmed
                        'password' => bcrypt(str()->random(16)), 
                    ]);
                    
                    SocialAccount::create([
                        'user_id'       => $user->id,
                        'provider_id'   => $googleUser->getId(),
                        'provider_name' => 'google',
                        'email'         => $googleUser->getEmail(),
                    ]);
                });
            } else {
                // Jika User sudah ada (daftar manual), tapi belum connect Google -> Connect-kan
                SocialAccount::create([
                    'user_id'       => $user->id,
                    'provider_id'   => $googleUser->getId(),
                    'provider_name' => 'google',
                    'email'         => $googleUser->getEmail(),
                ]);
            }

            // Login user
            Auth::login($user);

            return $this->redirectAfterLogin();

        } catch (Exception $e) {
            // Log error jika perlu: \Log::error($e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat autentikasi Google.');
        }
    }

    /**
     * Helper untuk redirect setelah login.
     * Cek apakah user sudah punya PIN atau belum.
     */
    private function redirectAfterLogin()
    {
        $user = Auth::user();

        // Jika user belum punya PIN, arahkan ke setup PIN
        // Pastikan kolom 'pin' ada di tabel users
        if (empty($user->pin)) {
            return redirect()->route('pin.setup');
        }

        // Jika sudah punya PIN, arahkan ke dashboard (atau verify pin)
        return redirect()->route('dashboard');
    }

    /**
     * Hapus koneksi akun Google (Unlink).
     */
    public function destroy($id)
    {
        $socialAccount = SocialAccount::findOrFail($id);

        // Security: Pastikan akun yang mau dihapus adalah milik user yang sedang login
        if ($socialAccount->user_id != Auth::id()) {
            abort(403);
        }

        // Validasi: Jangan biarkan user menghapus satu-satunya akses login
        if (Auth::user()->socialAccounts()->count() <= 1) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak bisa menghapus! Ini adalah satu-satunya akses login Anda.');
        }

        // Hapus
        $socialAccount->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Koneksi akun Google berhasil diputuskan.');
    }
}