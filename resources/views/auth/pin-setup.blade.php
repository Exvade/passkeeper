<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup PIN - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-950 h-screen flex items-center justify-center font-sans">

    <div class="w-full max-w-sm px-6">
        <div
            class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl text-center relative overflow-hidden">

            {{-- Decoration --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500">
            </div>

            <div
                class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 border border-slate-700 text-emerald-400">
                <i data-feather="shield" class="w-8 h-8"></i>
            </div>

            <h2 class="text-xl font-bold text-white mb-2">Buat PIN Keamanan</h2>
            <p class="text-slate-400 text-sm mb-8">Lindungi brankas kamu dengan 6 digit PIN Master. Jangan sampai lupa
                ya!</p>

            <form action="{{ route('pin.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Input PIN --}}
                <div class="text-left">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">PIN Baru</label>
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-white font-bold focus:ring-2 focus:ring-emerald-500/50 outline-none placeholder:text-slate-800 placeholder:tracking-normal transition mt-1"
                        placeholder="••••••">
                    @error('pin')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi PIN --}}
                <div class="text-left">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Ulangi PIN</label>
                    <input type="password" name="pin_confirmation" maxlength="6" required
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-white font-bold focus:ring-2 focus:ring-emerald-500/50 outline-none placeholder:text-slate-800 placeholder:tracking-normal transition mt-1"
                        placeholder="••••••">
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-emerald-500/25 mt-4">
                    Simpan & Masuk Dashboard
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-800">
                <p class="text-xs text-slate-500">Login sebagai <span
                        class="text-slate-300">{{ Auth::user()->email }}</span></p>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="text-xs text-red-400 hover:text-red-300">Salah akun? Logout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        feather.replace()
    </script>
</body>

</html>
