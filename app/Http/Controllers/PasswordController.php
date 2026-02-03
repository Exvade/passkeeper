<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PasswordController extends Controller
{
    // Method INDEX (Update untuk Filter & Sortir Favorite)
public function index(Request $request) {
    $query = Password::where('user_id', auth()->id());

    // 1. Filter Pencarian
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('site_name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    // 2. Filter Kategori (BARU)
    if ($request->has('category') && $request->category != 'Semua') {
        $query->where('category', $request->category);
    }

    // 3. Sorting: Favorite duluan, baru tanggal terbaru (BARU)
    $passwords = $query->orderBy('is_favorite', 'desc')
                       ->latest()
                       ->get();

    return view('dashboard', compact('passwords'));
}

// Method STORE (Tambah validasi category)
public function store(Request $request) {
    $request->validate([
        'site_name' => 'required',
        'username' => 'required',
        'password' => 'required',
        'category' => 'required', // Wajib pilih kategori
    ]);

    Password::create([
        'user_id' => auth()->id(),
        'site_name' => $request->site_name,
        'username' => $request->username,
        'site_url' => $request->site_url,
        'category' => $request->category, // Simpan kategori
        'encrypted_password' => Crypt::encryptString($request->password),
    ]);

    return back()->with('success', 'Data tersimpan!');
}

// Method UPDATE (Update kategori juga)
public function update(Request $request, $id) {
    $password = Password::where('user_id', auth()->id())->findOrFail($id);

    $data = [
        'site_name' => $request->site_name,
        'username' => $request->username,
        'site_url' => $request->site_url,
        'category' => $request->category, // Update kategori
    ];

    if ($request->filled('password')) {
        $data['encrypted_password'] = Crypt::encryptString($request->password);
    }

    $password->update($data);
    return back()->with('success', 'Data diperbarui!');
}

// Method TOGGLE FAVORITE (BARU - Khusus AJAX)
public function toggleFavorite($id) {
    $password = Password::where('user_id', auth()->id())->findOrFail($id);

    // Balik status (true jadi false, false jadi true)
    $password->update(['is_favorite' => !$password->is_favorite]);

    return response()->json([
        'status' => 'success', 
        'is_favorite' => $password->is_favorite
    ]);
}

    public function destroy($id) {
        Password::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('success', 'Data dihapus.');
    }
}
