<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SocialAccount;

class SocialAccountController extends Controller
{
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        // 1. Validasi Input PIN
        $request->validate([
            'pin' => 'required|numeric|digits:6',
        ]);

        // 2. Cek Kebenaran PIN
        if (!Hash::check($request->pin, $user->pin)) {
            return back()->with('error', 'PIN salah! Gagal menghapus akun.');
        }

        // 3. Cek apakah ini akun terakhir?
        if ($user->socialAccounts()->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus! Kamu harus punya minimal 1 akun Google terhubung.');
        }

        // 4. Hapus Akun
        $account = $user->socialAccounts()->where('id', $id)->firstOrFail();
        $account->delete();

        return back()->with('success', 'Akses akun Google tersebut telah dicabut.');
    }
}