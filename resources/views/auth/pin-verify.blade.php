<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keamanan - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-50 h-screen flex items-center justify-center font-sans antialiased">

    <div class="w-full max-w-sm px-6">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 text-center relative overflow-hidden">

            {{-- Decoration Top Bar --}}
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
            </div>

            <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600">
                <i data-feather="lock" class="w-8 h-8"></i>
            </div>

            <h2 class="text-xl font-bold text-slate-800 mb-2">Verifikasi Keamanan</h2>
            <p class="text-slate-500 text-sm mb-8">Masukkan 6 digit PIN Master untuk membuka brankas.</p>

            {{-- Gunakan route pin.check untuk verify, atau pin.store untuk setup (sesuaikan filenya) --}}
            <form action="{{ route('pin.check') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 text-center text-3xl tracking-[0.5em] text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none placeholder:text-slate-300 placeholder:tracking-normal transition"
                        placeholder="••••••">
                    @error('pin')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-indigo-600/20 active:scale-[0.98]">
                    Buka Brankas
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400">Akun: <span
                        class="text-slate-600 font-semibold">{{ Auth::user()->email }}</span></p>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="text-xs text-red-500 hover:text-red-700 font-medium hover:underline">Ganti Akun /
                        Logout</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        feather.replace()
    </script>
</body>

</html>
