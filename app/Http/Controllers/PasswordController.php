<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PasswordController extends Controller
{
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

        // 2. Filter Kategori
        if ($request->has('category') && $request->category != 'Semua') {
            $query->where('category', $request->category);
        }

        // 3. Sorting
        $passwords = $query->orderBy('is_favorite', 'desc')
                           ->latest()
                           ->get();
        
        // [LOGIC BARU] Jika request datang dari AJAX (Realtime Search)
        if ($request->ajax()) {
            // Kita cuma kembalikan potongan HTML (Partial View)
            return view('partials.password-list', compact('passwords'))->render();
        }

        // Kalau akses biasa, return halaman full dashboard
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

    public function export()
    {
        $fileName = 'passkeeper-backup-' . date('Y-m-d_H-i') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // [TRICK 1] Tambahkan BOM (Byte Order Mark) agar Excel baca UTF-8 dengan benar
            // Ini bikin simbol-simbol aneh jadi bener tampilannya
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // [TRICK 2] Gunakan delimiter ";" (titik koma) bukan "," (koma)
            // Parameter ke-3 di fputcsv adalah separator
            $delimiter = ';'; 

            // Header Kolom
            fputcsv($file, ['Kategori', 'Aplikasi', 'URL', 'Username', 'Password'], $delimiter);

            // Ambil data user
            $passwords = Password::where('user_id', auth()->id())->get();

            foreach ($passwords as $row) {
                fputcsv($file, [
                    $row->category,
                    $row->site_name,
                    $row->username,
                    Crypt::decryptString($row->encrypted_password)
                ], $delimiter); // Jangan lupa delimiter di sini juga
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
