<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi - PassKeeper</title>
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

            {{-- HEADER: Logo Kecil --}}
            <div class="flex flex-col items-center justify-center gap-3 mb-6">
                <img src="{{ asset('passkeeper-logo.png') }}" alt="Logo"
                    class="w-10 h-10 object-contain grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition duration-500">
                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">Security Check</span>
            </div>

            {{-- TITLE --}}
            <h2 class="text-lg font-bold text-slate-900">Buka Brankas</h2>
            <p class="text-sm text-slate-500 mt-1 mb-8">Masukkan PIN Master 6-digit Anda.</p>

            {{-- FORM --}}
            <form action="{{ route('pin.check') }}" method="POST">
                @csrf
                <div class="mb-6 relative">
                    <input type="password" name="pin" maxlength="6" autofocus required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 text-center text-3xl tracking-[0.5em] text-slate-900 font-bold font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none placeholder:text-slate-300 placeholder:tracking-widest transition"
                        placeholder="••••••">

                    @error('pin')
                        <div class="absolute -bottom-6 left-0 w-full flex justify-center">
                            <p class="text-red-500 text-xs font-medium bg-red-50 px-2 py-0.5 rounded-full">
                                {{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 hover:bg-black text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-slate-900/10 active:scale-[0.98]">
                    Buka Akses
                </button>
            </form>

        </div>

        {{-- FOOTER INFO --}}
        <div class="text-center mt-8">
            <p class="text-xs text-slate-400 mb-2">Masuk sebagai <span
                    class="text-slate-600 font-semibold">{{ Auth::user()->email }}</span></p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-xs text-red-500 hover:text-red-600 font-medium hover:underline transition">
                    Logout / Ganti Akun
                </button>
            </form>
        </div>

    </div>
</body>

</html>
