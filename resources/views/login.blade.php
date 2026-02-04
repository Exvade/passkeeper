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

<body class="bg-slate-50 h-screen flex flex-col items-center justify-center font-sans antialiased text-slate-600">

    <div class="w-full max-w-sm px-6">

        {{-- CARD LOGIN --}}
        <div class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100">

            {{-- HEADER: Logo + Nama (Satu Kesatuan) --}}
            {{-- Hapus shadow, border, dan rounded pada gambar agar clean --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <img src="{{ asset('passkeeper-logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <h1 class="font-bold text-2xl text-slate-900 tracking-tight">PassKeeper</h1>
            </div>

            {{-- TEXT INTRO --}}
            <div class="mb-8 text-center">
                <h2 class="text-lg font-semibold text-slate-900">Selamat Datang</h2>
                <p class="text-sm text-slate-500 mt-1">Akses brankas password digital Anda.</p>
            </div>

            {{-- TOMBOL GOOGLE --}}
            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-3 w-full bg-white text-slate-700 font-semibold py-3 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-[0.98] mb-8">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.2 1 12s.43 3.45 1.18 4.93l2.85-2.84z" />
                    <path fill="#EA4335"
                        d="M12 4.66c1.61 0 3.06.56 4.21 1.64l3.15-3.15C17.45 1.49 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                Masuk dengan Google
            </a>

            {{-- EXTRA CONTENT (ROWS / FLEX-COL) --}}
            {{-- Menggunakan layout baris ke bawah (stack) agar lebih rapi --}}
            <div class="pt-6 border-t border-slate-100 flex flex-col gap-3">

                <div class="flex items-center gap-3 text-slate-600">
                    <div class="text-emerald-500 bg-emerald-50 p-1.5 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium">Terenkripsi AES-256 (Aman)</span>
                </div>

                <div class="flex items-center gap-3 text-slate-600">
                    <div class="text-indigo-500 bg-indigo-50 p-1.5 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </div>
                    <span class="text-xs font-medium">Backup & Export CSV Kapan Saja</span>
                </div>

            </div>

        </div>

        {{-- COPYRIGHT --}}
        <p class="text-center text-xs text-slate-400 mt-8">
            &copy; {{ date('Y') }} PassKeeper Security.
        </p>

    </div>
</body>

</html>
