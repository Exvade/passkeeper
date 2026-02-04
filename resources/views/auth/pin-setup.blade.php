<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat PIN - PassKeeper</title>
    @vite('resources/css/app.css')

    {{-- FONT LOAD --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-slate-50 h-screen flex flex-col items-center justify-center font-sans antialiased text-slate-600">

    <div class="w-full max-w-sm px-6">

        {{-- CARD --}}
        <div class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 text-center">

            {{-- HEADER --}}
            <div class="flex flex-col items-center justify-center gap-3 mb-6">
                <img src="{{ asset('passkeeper-logo.png') }}" alt="Logo"
                    class="w-10 h-10 object-contain grayscale opacity-80">
                <span
                    class="text-xs font-bold tracking-widest text-emerald-600 uppercase bg-emerald-50 px-2 py-1 rounded-md">Setup
                    Baru</span>
            </div>

            {{-- TITLE --}}
            <h2 class="text-lg font-bold text-slate-900">Buat PIN Master</h2>
            <p class="text-sm text-slate-500 mt-1 mb-8">PIN ini digunakan untuk membuka enkripsi data.</p>

            {{-- FORM --}}
            <form action="{{ route('pin.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Input 1 --}}
                <div class="text-left">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">PIN 6-Digit
                        Baru</label>
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-slate-900 font-bold font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none placeholder:text-slate-300 placeholder:tracking-widest transition mt-1"
                        placeholder="••••••">
                    @error('pin')
                        <p class="text-red-500 text-xs mt-1.5 font-medium pl-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input 2 --}}
                <div class="text-left">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Konfirmasi
                        Ulang</label>
                    <input type="password" name="pin_confirmation" maxlength="6" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 text-center text-2xl tracking-[0.5em] text-slate-900 font-bold font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none placeholder:text-slate-300 placeholder:tracking-widest transition mt-1"
                        placeholder="••••••">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-emerald-600/20 active:scale-[0.98]">
                        Simpan & Kunci Brankas
                    </button>
                </div>
            </form>

        </div>

        {{-- FOOTER --}}
        <div class="text-center mt-8">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-xs text-slate-400 hover:text-slate-600 font-medium transition">
                    &larr; Batal & Logout
                </button>
            </form>
        </div>

    </div>
</body>

</html>
