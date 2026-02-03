<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PasswordController extends Controller
{
    public function index(Request $request) {
    $query = Password::where('user_id', auth()->id());

    // Cek apakah ada pencarian
    if ($request->has('search')) {
        $search = $request->search;
        // Grouping where agar logika OR tidak bocor keluar user_id
        $query->where(function($q) use ($search) {
            $q->where('site_name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    $passwords = $query->latest()->get();
    
    return view('dashboard', compact('passwords'));
}

    public function store(Request $request) {
        $request->validate([
            'site_name' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        Password::create([
            'user_id' => auth()->id(),
            'site_name' => $request->site_name,
            'username' => $request->username,
            'site_url' => $request->site_url,
            // ENKRIPSI DISINI SEBELUM MASUK DB
            'encrypted_password' => Crypt::encryptString($request->password),
        ]);

        return back()->with('success', 'Password berhasil diamankan!');
    }

    public function decrypt($id) {
        $password = Password::where('user_id', auth()->id())->findOrFail($id);
        // DEKRIPSI UNTUK DITAMPILKAN KE USER
        return response()->json([
            'raw_password' => Crypt::decryptString($password->encrypted_password)
        ]);
    }
    public function update(Request $request, $id)
    {
        $password = Password::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'site_name' => 'required',
            'username' => 'required',
        ]);

        // Siapkan data yang mau diupdate
        $data = [
            'site_name' => $request->site_name,
            'username' => $request->username,
            'site_url' => $request->site_url,
        ];

        // Cek: Apakah user mengisi password baru?
        if ($request->filled('password')) {
            // Kalau diisi, kita enkripsi password barunya
            $data['encrypted_password'] = Crypt::encryptString($request->password);
        }

        $password->update($data);

        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id) {
        Password::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('success', 'Data dihapus.');
    }
}
