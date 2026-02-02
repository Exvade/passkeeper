<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-950 text-slate-200 font-sans antialiased min-h-screen relative">

    {{-- Navbar --}}
    <nav class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-indigo-600 p-1.5 rounded-lg">
                        <i data-feather="shield" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">PassKeeper</span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 hidden sm:block">Halo, {{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-sm text-red-400 hover:text-red-300 font-medium transition flex items-center gap-1">
                            <i data-feather="log-out" class="w-4 h-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header Section --}}
        {{-- Header Section (Judul & Search) --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Brankas Kamu</h1>
                <p class="text-slate-400 text-sm mt-1">
                    @if (request('search'))
                        Menampilkan hasil pencarian: "<span
                            class="text-indigo-400 font-bold">{{ request('search') }}</span>"
                    @else
                        Total {{ $passwords->count() }} akun tersimpan aman.
                    @endif
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                {{-- Form Pencarian --}}
                <form action="{{ route('dashboard') }}" method="GET" class="relative w-full md:w-64">
                    <i data-feather="search" class="absolute left-3 top-3 w-4 h-4 text-slate-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari aplikasi / username..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition shadow-sm">
                </form>

                {{-- Tombol Tambah --}}
                <button onclick="openModal()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2 whitespace-nowrap">
                    <i data-feather="plus" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Baru</span>
                </button>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div id="flash-msg"
                class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-feather="check-circle" class="w-5 h-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="document.getElementById('flash-msg').remove()"
                    class="text-emerald-400 hover:text-white"><i data-feather="x" class="w-4 h-4"></i></button>
            </div>
        @endif

        {{-- Grid Passwords --}}
        @if ($passwords->isEmpty())

            <div class="text-center py-20 bg-slate-900/50 rounded-3xl border border-dashed border-slate-800">
                @if (request('search'))
                    {{-- Tampilan Jika Search Tidak Ketemu --}}
                    <div class="bg-slate-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="search" class="w-8 h-8 text-slate-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white">Tidak ditemukan</h3>
                    <p class="text-slate-500 mb-6">Tidak ada password yang cocok dengan "<span
                            class="text-indigo-400">{{ request('search') }}</span>".</p>
                    <a href="{{ route('dashboard') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Reset
                        Pencarian</a>
                @else
                    {{-- Tampilan Jika Belum Ada Data --}}
                    <div class="bg-slate-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="inbox" class="w-8 h-8 text-slate-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white">Belum ada data</h3>
                    <p class="text-slate-500 mb-6 max-w-sm mx-auto">Mulai amankan akun digitalmu dengan menambahkan
                        password baru sekarang.</p>
                    <button onclick="openModal()" class="text-indigo-400 hover:text-indigo-300 font-medium">Tambah
                        Password &rarr;</button>
                @endif
            </div>
        @else
            {{-- Grid Loop (Tidak Berubah) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- ... (kode foreach yang lama tetap di sini) ... --}}
                @foreach ($passwords as $pass)
                    {{-- ... Isi card password yang lama ... --}}
                    {{-- (Salin ulang isi foreach dari kode sebelumnya kalau terhapus) --}}
                    <div
                        class="bg-slate-900 hover:bg-slate-800/80 p-5 rounded-2xl border border-slate-800 hover:border-indigo-500/30 transition group relative shadow-sm">
                        {{-- Icon & Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-bold text-lg border border-slate-700">
                                    {{ substr($pass->site_name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-base leading-tight">{{ $pass->site_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $pass->username }}</p>
                                </div>
                            </div>
                            @if ($pass->site_url)
                                <a href="{{ $pass->site_url }}" target="_blank"
                                    class="text-slate-600 hover:text-indigo-400 transition" title="Kunjungi Website">
                                    <i data-feather="external-link" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </div>

                        {{-- Password Field --}}
                        <div class="bg-black/30 rounded-xl p-3 flex justify-between items-center border border-white/5">
                            <input type="text" id="pass-{{ $pass->id }}" value="••••••••••••" readonly
                                class="bg-transparent border-none text-slate-300 w-full focus:ring-0 text-sm font-mono tracking-wider px-0 py-0">

                            <button onclick="revealPassword({{ $pass->id }})"
                                class="text-slate-500 hover:text-white transition ml-2" title="Lihat">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-800">
                            <button onclick="copyToClipboard('pass-{{ $pass->id }}')"
                                class="text-xs font-medium text-slate-400 hover:text-indigo-400 flex items-center gap-1.5 transition">
                                <i data-feather="copy" class="w-3.5 h-3.5"></i> Salin
                            </button>

                            <form action="{{ route('passwords.destroy', $pass->id) }}" method="POST"
                                onsubmit="return confirm('Hapus password ini permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs font-medium text-slate-500 hover:text-red-400 transition flex items-center gap-1.5">
                                    <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </main>

    {{-- MODAL TAMBAH PASSWORD (Hidden by default) --}}
    <div id="modalOverlay" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop Blur --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        {{-- Modal Content --}}
        <div class="flex items-center justify-center min-h-screen px-4">
            <div
                class="bg-slate-900 border border-slate-700 w-full max-w-md p-6 rounded-2xl shadow-2xl relative transform transition-all scale-100">

                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Simpan Password Baru</h2>
                    <button onclick="closeModal()" class="text-slate-500 hover:text-white">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form action="{{ route('passwords.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Nama
                            Aplikasi</label>
                        <input type="text" name="site_name" placeholder="Misal: Netflix"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition"
                            required>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Username /
                            Email</label>
                        <input type="text" name="username" placeholder="user@email.com"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">URL
                            (Opsional)</label>
                        <input type="url" name="site_url" placeholder="https://"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <input type="text" name="password" placeholder="Masukkan password..."
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition"
                                required>
                            {{-- Tips: Type text biar user bisa liat saat input, atau ganti type="password" kalau mau hidden --}}
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-500/25">
                            Simpan ke Brankas
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Script --}}
    <script>
        feather.replace();

        // Modal Logic
        const modal = document.getElementById('modalOverlay');

        function openModal() {
            modal.classList.remove('hidden');
            // Sedikit animasi fade in manual jika mau, tapi hidden toggle sudah cukup cepat
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });

        // Decrypt Logic (Sama seperti sebelumnya)
        async function revealPassword(id) {
            const inputField = document.getElementById(`pass-${id}`);
            if (inputField.type === 'text' && inputField.value !== '••••••••••••') {
                inputField.value = '••••••••••••';
                return;
            }
            try {
                // Kasih loading state visual
                inputField.value = 'Loading...';
                const response = await fetch(`/passwords/${id}/decrypt`);
                const data = await response.json();
                inputField.value = data.raw_password;

                setTimeout(() => {
                    inputField.value = '••••••••••••';
                }, 10000);
            } catch (error) {
                inputField.value = 'Error!';
            }
        }

        function copyToClipboard(elementId) {
            const copyText = document.getElementById(elementId);
            if (copyText.value === '••••••••••••' || copyText.value === 'Loading...') {
                alert('Klik ikon mata dulu untuk membuka password!');
                return;
            }
            copyText.select();
            navigator.clipboard.writeText(copyText.value);

            // Visual feedback simple
            const originalVal = copyText.value;
            copyText.value = 'Tersalin!';
            setTimeout(() => {
                copyText.value = originalVal;
            }, 1000);
        }
    </script>
</body>

</html>
