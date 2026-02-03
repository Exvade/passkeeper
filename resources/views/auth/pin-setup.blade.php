<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat PIN - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-50 h-screen flex items-center justify-center font-sans antialiased">

    <div class="w-full max-w-sm px-6">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 text-center relative overflow-hidden">

            {{-- Decoration Top Bar (Emerald Gradient) --}}
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400">
            </div>

            <div
                class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600">
                <i data-feather="shield" class="w-8 h-8"></i>
            </div>

            <h2 class="text-xl font-bold text-slate-800 mb-2">Buat PIN Keamanan</h2>
            <p class="text-slate-500 text-sm mb-8">Lindungi brankas kamu dengan 6 digit PIN Master baru.</p>

            <form action="{{ route('pin.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Input PIN --}}
                <div class="text-left">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">PIN Baru</label>
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-slate-800 font-bold focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none placeholder:text-slate-300 placeholder:tracking-normal transition mt-1"
                        placeholder="••••••">
                    @error('pin')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi PIN --}}
                <div class="text-left">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Ulangi PIN</label>
                    <input type="password" name="pin_confirmation" maxlength="6" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-slate-800 font-bold focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 outline-none placeholder:text-slate-300 placeholder:tracking-normal transition mt-1"
                        placeholder="••••••">
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-emerald-600/20 active:scale-[0.98] mt-2">
                    Simpan & Masuk
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400">Login sebagai <span
                        class="text-slate-600 font-semibold">{{ Auth::user()->email }}</span></p>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="text-xs text-red-500 hover:text-red-700 font-medium hover:underline">Bukan akun kamu?
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
