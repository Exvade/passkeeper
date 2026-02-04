<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PassKeeper</title>
    @vite('resources/css/app.css')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-slate-50 h-screen flex items-center justify-center font-sans antialiased">

    <div class="w-full max-w-sm px-6">
        <div
            class="bg-white p-8 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] border border-slate-100 text-center">

            {{-- HEADER: LOGO (Atas) + BRAND NAME (Bawah) --}}
            <div class="flex flex-col items-center justify-center gap-4 mb-8">
                {{-- Logo --}}
                <div class="w-16 h-16 relative">
                    <img src="{{ asset('square-logo.png') }}" alt="Logo"
                        class="w-full h-full object-cover rounded-2xl shadow-md border border-slate-100">
                </div>
                {{-- Nama Brand --}}
                <span class="font-bold text-2xl text-slate-800 tracking-tight font-sans">PassKeeper</span>
            </div>

            {{-- COPYWRITING --}}
            <h1 class="text-lg font-semibold text-slate-900">Akses Brankas Anda</h1>
            <p class="text-slate-500 mt-2 text-sm mb-8 leading-relaxed px-2">
                Simpan dan kelola semua kata sandi Anda dalam satu tempat yang aman.
            </p>

            {{-- BUTTON GOOGLE --}}
            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-3 w-full bg-white text-slate-700 font-semibold py-3 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-[0.98] group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.2 1 12s.43 3.45 1.18 4.93l2.85-2.84z" />
                    <path fill="#EA4335"
                        d="M12 4.66c1.61 0 3.06.56 4.21 1.64l3.15-3.15C17.45 1.49 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                Lanjutkan dengan Google
            </a>

            <div class="mt-8 pt-6 border-t border-slate-50">
                <p class="text-[10px] uppercase font-bold tracking-widest text-slate-300">
                    AES-256 ENCRYPTED
                </p>
            </div>
        </div>
    </div>
</body>

</html>
