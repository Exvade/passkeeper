<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locked - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-950 h-screen flex items-center justify-center font-sans">

    <div class="w-full max-w-sm px-6">
        <div
            class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl text-center relative overflow-hidden">

            {{-- Decoration --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
            </div>

            <div
                class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 border border-slate-700 text-indigo-400">
                <i data-feather="lock" class="w-8 h-8"></i>
            </div>

            <h2 class="text-xl font-bold text-white mb-2">Keamanan Tambahan</h2>
            <p class="text-slate-400 text-sm mb-8">Masukkan 6 digit PIN Master kamu untuk membuka brankas.</p>

            <form action="{{ route('pin.check') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl py-4 text-center text-2xl tracking-[0.5em] text-white font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none placeholder:text-slate-800 placeholder:tracking-normal transition"
                        placeholder="••••••">
                    @error('pin')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-500/25">
                    Buka Kunci
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-800">
                <p class="text-xs text-slate-500">Masuk sebagai <span
                        class="text-slate-300">{{ Auth::user()->email }}</span></p>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="text-xs text-red-400 hover:text-red-300">Bukan saya? Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        feather.replace()
    </script>
</body>

</html>
