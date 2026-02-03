<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException; // [WAJIB ADA]
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
        
        // Jika request dari AJAX (Realtime Search)
        if ($request->ajax()) {
            return view('partials.password-list', compact('passwords'))->render();
        }

        return view('dashboard', compact('passwords'));
    }

    public function store(Request $request) {
        $request->validate([
            'site_name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'category' => 'required',
        ]);

        Password::create([
            'user_id' => auth()->id(),
            'site_name' => $request->site_name,
            'username' => $request->username,
            'site_url' => $request->site_url,
            'category' => $request->category,
            'encrypted_password' => Crypt::encryptString($request->password),
        ]);

        return back()->with('success', 'Data tersimpan!');
    }

    public function update(Request $request, $id) {
        $password = Password::where('user_id', auth()->id())->findOrFail($id);

        $data = [
            'site_name' => $request->site_name,
            'username' => $request->username,
            'site_url' => $request->site_url,
            'category' => $request->category,
        ];

        if ($request->filled('password')) {
            $data['encrypted_password'] = Crypt::encryptString($request->password);
        }

        $password->update($data);
        return back()->with('success', 'Data diperbarui!');
    }

    // [METHOD YANG HILANG KITA KEMBALIKAN + FITUR ANTI CRASH]
    public function decrypt($id)
    {
        $password = Password::where('user_id', auth()->id())->findOrFail($id);

        try {
            // Coba decrypt normal
            $decrypted = Crypt::decryptString($password->encrypted_password);
        } catch (DecryptException $e) {
            // Jika gagal (data rusak/dummy), tampilkan error text jangan crash 500
            $decrypted = "Error: Data tidak terenkripsi"; 
        }

        return response()->json(['raw_password' => $decrypted]);
    }

    public function toggleFavorite($id) {
        $password = Password::where('user_id', auth()->id())->findOrFail($id);
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
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM Fix
            $delimiter = ';'; 

            // Saya perbaiki headernya juga (Hapus URL biar sesuai data di bawahnya)
            fputcsv($file, ['Kategori', 'Aplikasi', 'Username', 'Password'], $delimiter);

            $passwords = Password::where('user_id', auth()->id())->get();

            foreach ($passwords as $row) {
                // Logic decrypt aman saat export juga
                try {
                    $pass = Crypt::decryptString($row->encrypted_password);
                } catch (DecryptException $e) {
                    $pass = "CORRUPT_DATA";
                }

                fputcsv($file, [
                    $row->category,
                    $row->site_name,
                    $row->username,
                    $pass
                ], $delimiter);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}