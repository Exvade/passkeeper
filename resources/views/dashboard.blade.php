<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 3. META TAGS UNTUK WHATSAPP/SOSMED --}}
    <meta property="og:title" content="PassKeeper - Brankas Password Aman">
    <meta property="og:description" content="Kelola dan amankan akses digital Anda dalam satu tempat terenkripsi.">
    <meta property="og:image" content="{{ asset('passkeeper-logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <title>Dashboard - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .star-active {
            fill: #fbbf24;
            color: #fbbf24;
        }

        .star-inactive {
            fill: none;
            color: #cbd5e1;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen relative">

    {{-- Navbar --}}
    <nav
        class="bg-white/80 backdrop-blur-xl border-b border-slate-200 sticky top-0 z-40 supports-[backdrop-filter]:bg-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('passkeeper-logo.png') }}" alt="Logo" class="object-contain rounded-lg"
                        style="width: 36px; height: 36px;">
                    <span class="font-bold text-xl tracking-tight text-slate-800">PassKeeper</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-sm font-medium text-slate-500 hidden md:block">
                        Hai, {{ Auth::user()->name }}
                    </span>
                    <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

                    <button onclick="openSettingsModal()"
                        class="flex items-center gap-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-lg transition group"
                        title="Pengaturan Akun">
                        <i data-feather="settings"
                            class="w-4 h-4 group-hover:rotate-90 transition-transform duration-500"></i>
                        <span class="text-sm font-semibold hidden sm:block">Settings</span>
                    </button>

                    {{-- 2. LOGOUT BUTTON (Sekarang memicu Modal, bukan langsung submit) --}}
                    <form id="realLogoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                    <button onclick="openLogoutModal()"
                        class="flex items-center gap-2 text-slate-500 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition group"
                        title="Keluar Aplikasi">
                        <i data-feather="log-out" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                        <span class="text-sm font-semibold hidden sm:block">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-5">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Brankas Password</h1>
                <p class="text-slate-500 text-sm mt-1">Total <span
                        class="font-bold text-indigo-600">{{ $passwords->count() }}</span> akun tersimpan aman.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form action="{{ route('dashboard') }}" method="GET" class="relative w-full md:w-64">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <i data-feather="search" class="absolute left-3.5 top-3 w-4 h-4 text-slate-400"></i>
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Cari aplikasi..."
                        class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition shadow-sm"
                        onkeyup="debouncedSearch()">
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('passwords.export') }}" target="_blank"
                        class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-4 py-2.5 rounded-xl font-semibold transition flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                        <i data-feather="download" class="w-4 h-4 text-slate-500"></i>
                        <span>Export CSV</span>
                    </a>

                    <button onclick="openAddModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-2 whitespace-nowrap">
                        <i data-feather="plus" class="w-5 h-5"></i>
                        <span>Tambah</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-2 mb-6 no-scrollbar">
            @php $categories = ['Semua', 'Sosmed', 'Pekerjaan', 'Keuangan', 'Hiburan', 'Lainnya']; @endphp
            @foreach ($categories as $cat)
                <a href="{{ route('dashboard', ['category' => $cat, 'search' => request('search')]) }}"
                    class="px-5 py-2 rounded-full text-sm font-semibold transition whitespace-nowrap border 
                   {{ request('category') == $cat || (!request('category') && $cat == 'Semua')
                       ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-500/20'
                       : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-800 hover:shadow-sm' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- Password List Container --}}
        <div id="password-container">
            @include('partials.password-list')
        </div>
    </main>

    {{-- MODAL TAMBAH/EDIT --}}
    <div id="modalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl relative transform transition-all">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-slate-800">Simpan Password</h2>
                    <button onclick="closeModal()"
                        class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition"><i
                            data-feather="x" class="w-5 h-5"></i></button>
                </div>

                <form id="modalForm" action="{{ route('passwords.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div id="methodInput"></div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Kategori</label>
                        <select id="inputCategory" name="category"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition font-medium">
                            <option value="Sosmed">Sosmed</option>
                            <option value="Pekerjaan">Pekerjaan</option>
                            <option value="Keuangan">Keuangan</option>
                            <option value="Hiburan">Hiburan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Aplikasi</label>
                            <input type="text" id="inputSiteName" name="site_name" placeholder="Netflix"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none font-medium"
                                required>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Username</label>
                            <input type="text" id="inputUsername" name="username" placeholder="User123"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none font-medium"
                                required>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">URL</label>
                        <input type="url" id="inputUrl" name="site_url" placeholder="https://"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none font-medium">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Password</label>
                        <input type="text" id="inputPassword" name="password" placeholder="Rahasia..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none font-medium">
                        <p id="editNote" class="hidden text-xs text-slate-400 mt-2 flex items-center gap-1"><i
                                data-feather="info" class="w-3 h-3"></i> Kosongkan jika password tidak berubah.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="submitBtn"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-indigo-600/20 active:scale-[0.98]">Simpan
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SETTINGS MODAL --}}
    <div id="settingsModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeSettingsModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Akun Terhubung</h2>
                    <button onclick="closeSettingsModal()" class="text-slate-400 hover:text-slate-600 transition"><i
                            data-feather="x" class="w-6 h-6"></i></button>
                </div>
                <div class="space-y-3 mb-6">
                    @foreach (Auth::user()->socialAccounts as $acc)
                        <div
                            class="flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="flex flex-col">
                                <p class="text-sm text-slate-800 font-semibold">{{ $acc->email }}</p>
                                <p class="text-xs text-slate-500">{{ $acc->created_at->diffForHumans() }}</p>
                            </div>
                            @if (Auth::user()->socialAccounts->count() > 1)
                                <button
                                    onclick="openUnlinkModal('{{ route('social-accounts.destroy', $acc->id) }}', '{{ $acc->email }}')"
                                    class="text-slate-400 hover:text-red-600 p-2 transition bg-white border border-slate-200 rounded-lg hover:border-red-200 hover:bg-red-50"
                                    title="Putuskan"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                            @else
                                <span
                                    class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100 uppercase tracking-wide">Utama</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('auth.google') }}"
                    class="flex items-center justify-center gap-2 w-full bg-white text-slate-700 border border-slate-300 font-semibold py-3 rounded-xl transition hover:bg-slate-50 active:scale-[0.98]"><i
                        data-feather="plus-circle" class="w-4 h-4"></i> Hubungkan Google Lain</a>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="deleteModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-sm p-6 rounded-3xl shadow-2xl text-center relative">
                <div
                    class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 border-4 border-red-100 mb-4 text-red-500">
                    <i data-feather="alert-triangle" class="h-6 w-6"></i></div>
                <h3 class="text-lg font-bold text-slate-800">Hapus Permanen?</h3>
                <p class="text-sm text-slate-500 mt-2 px-4">Tindakan ini tidak bisa dibatalkan.</p>
                <form id="deleteForm" method="POST" class="mt-6 flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()"
                        class="w-full bg-white border border-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-red-500/30">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- UNLINK MODAL --}}
    <div id="unlinkModalOverlay" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeUnlinkModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-sm p-6 rounded-3xl shadow-2xl text-center relative">
                <div
                    class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 border-4 border-red-100 mb-4 text-red-500">
                    <i data-feather="shield-off" class="h-6 w-6"></i></div>
                <h3 class="text-lg font-bold text-slate-800">Putuskan Akun?</h3>
                <p class="text-sm text-slate-500 mt-2">Akses login untuk <span id="unlinkEmail"
                        class="text-slate-800 font-bold">email</span> akan dihapus.</p>
                <form id="unlinkForm" method="POST" class="mt-6">
                    @csrf @method('DELETE')
                    <div class="mb-4 text-left">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider ml-1">Konfirmasi
                            PIN</label>
                        <input type="password" name="pin" maxlength="6" required placeholder="••••••"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-3 text-slate-800 text-center tracking-[0.5em] text-lg font-bold mt-1 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeUnlinkModal()"
                            class="w-full bg-white border border-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl">Batal</button>
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-red-500/30">Putuskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. LOGOUT CONFIRMATION MODAL (BARU) --}}
    <div id="logoutModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeLogoutModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-sm p-6 rounded-3xl shadow-2xl text-center relative">
                <div
                    class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-slate-50 border-4 border-slate-100 mb-4 text-slate-600">
                    <i data-feather="log-out" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Keluar Aplikasi?</h3>
                <p class="text-sm text-slate-500 mt-2 px-4">Anda harus memasukkan PIN lagi untuk masuk kembali.</p>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeLogoutModal()"
                        class="w-full bg-white border border-slate-300 text-slate-700 font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>

                    {{-- Trigger form logout asli via JS --}}
                    <button onclick="document.getElementById('realLogoutForm').submit()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-red-500/30 transition">
                        Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div id="toast"
        class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-24 opacity-0">
        <div
            class="bg-white border border-slate-100 text-slate-800 px-5 py-4 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] flex items-center gap-4">
            <div id="toast-icon-bg" class="bg-emerald-100 p-2 rounded-full text-emerald-600"><i id="toast-icon"
                    data-feather="check" class="w-4 h-4"></i></div>
            <div>
                <h4 id="toast-title" class="font-bold text-sm text-slate-900">Berhasil</h4>
                <p id="toast-message" class="text-xs text-slate-500 mt-0.5">Notifikasi.</p>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if (session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });

        // --- TOAST SYSTEM ---
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const title = document.getElementById('toast-title');
            const msg = document.getElementById('toast-message');
            const iconBg = document.getElementById('toast-icon-bg');
            const icon = document.getElementById('toast-icon');

            msg.innerText = message;
            if (type === 'error') {
                title.innerText = "Gagal";
                title.classList.add('text-red-600');
                iconBg.classList.remove('bg-emerald-100', 'text-emerald-600');
                iconBg.classList.add('bg-red-100', 'text-red-600');
                icon.innerHTML =
                '<polyline points="12 2 12 12"></polyline><line x1="12" y1="16" x2="12.01" y2="16"></line>';
                icon.setAttribute('data-feather', 'alert-circle');
            } else {
                title.innerText = "Berhasil";
                title.classList.remove('text-red-600');
                iconBg.classList.remove('bg-red-100', 'text-red-600');
                iconBg.classList.add('bg-emerald-100', 'text-emerald-600');
                icon.setAttribute('data-feather', 'check');
            }
            feather.replace();
            toast.classList.remove('translate-y-24', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 3000);
        }

        // --- PASSWORD REVEAL & COPY ---
        async function handlePasswordAction(id, btn) {
            const input = document.getElementById(`pass-input-${id}`);
            const currentState = btn.getAttribute('data-state');

            if (currentState === 'hidden') {
                try {
                    btn.classList.add('animate-pulse');
                    const res = await fetch(`/passwords/${id}/decrypt`);
                    if (!res.ok) throw new Error("Gagal mengambil data");
                    const data = await res.json();
                    input.value = data.raw_password;
                    input.type = 'text';
                    input.classList.remove('text-slate-600', 'tracking-widest');
                    input.classList.add('text-slate-800', 'font-bold', 'tracking-normal');
                    btn.setAttribute('data-state', 'visible');
                    btn.title = "Salin Password";
                    btn.classList.remove('text-slate-400', 'hover:text-indigo-600');
                    btn.classList.add('text-indigo-600', 'bg-indigo-50', 'border-indigo-100');
                    btn.innerHTML =
                        `<i data-feather="copy" class="w-4 h-4"></i> <span class="text-[10px] font-bold ml-1 uppercase">Salin</span>`;
                    feather.replace();
                    btn.classList.remove('animate-pulse');
                } catch (e) {
                    console.error(e);
                    showToast('Gagal membuka password.', 'error');
                    btn.classList.remove('animate-pulse');
                }
            } else {
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(() => {
                    showToast('Password berhasil disalin!', 'success');
                    setTimeout(() => {
                        input.type = 'password';
                        input.value = 'DUMMYPASS123';
                        input.classList.remove('text-slate-800', 'font-bold', 'tracking-normal');
                        input.classList.add('text-slate-600', 'tracking-widest');
                        btn.setAttribute('data-state', 'hidden');
                        btn.title = "Lihat Password";
                        btn.classList.remove('text-indigo-600', 'bg-indigo-50', 'border-indigo-100');
                        btn.classList.add('text-slate-400', 'hover:text-indigo-600');
                        btn.innerHTML = `<i data-feather="eye" class="w-4 h-4"></i>`;
                        feather.replace();
                    }, 2000);
                });
            }
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
            showToast('Username disalin!', 'success');
        }

        async function toggleFavorite(id, btnElement) {
            const icon = btnElement.querySelector('svg') || btnElement.querySelector('i');
            const isActive = icon.classList.contains('star-active');
            if (isActive) {
                icon.classList.remove('star-active');
                icon.classList.add('star-inactive');
            } else {
                icon.classList.add('star-active');
                icon.classList.remove('star-inactive');
            }
            try {
                const response = await fetch(`/passwords/${id}/favorite`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                if (response.ok) {
                    showToast(isActive ? 'Dihapus dari Favorite' : 'Ditambahkan ke Favorite!', 'success');
                } else {
                    throw new Error();
                }
            } catch (e) {
                if (isActive) {
                    icon.classList.add('star-active');
                    icon.classList.remove('star-inactive');
                } else {
                    icon.classList.remove('star-active');
                    icon.classList.add('star-inactive');
                }
                showToast('Gagal koneksi ke server.', 'error');
            }
        }

        let debounceTimer;

        function debouncedSearch() {
            const query = document.getElementById('searchInput').value;
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category') || 'Semua';
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchPasswords(query, category);
            }, 500);
        }
        async function fetchPasswords(search, category) {
            const container = document.getElementById('password-container');
            container.style.opacity = '0.5';
            try {
                const response = await fetch(`/dashboard?search=${search}&category=${category}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                container.innerHTML = html;
                feather.replace();
            } catch (error) {
                console.error(error);
            } finally {
                container.style.opacity = '1';
            }
        }

        // --- MODAL VARIABLES ---
        const modal = document.getElementById('modalOverlay');
        const form = document.getElementById('modalForm');
        const title = document.getElementById('modalTitle');
        const methodInput = document.getElementById('methodInput');
        const editNote = document.getElementById('editNote');
        const btn = document.getElementById('submitBtn');
        const settingsModal = document.getElementById('settingsModalOverlay');
        const unlinkModal = document.getElementById('unlinkModalOverlay');
        const unlinkForm = document.getElementById('unlinkForm');
        const unlinkEmailSpan = document.getElementById('unlinkEmail');
        const delModal = document.getElementById('deleteModalOverlay');
        const delForm = document.getElementById('deleteForm');
        const logoutModal = document.getElementById('logoutModalOverlay');

        // 1. SCROLL LOCK LOGIC (Ditambahkan di semua open/close)
        // Saat modal buka, body dikasih class overflow-hidden biar gak bisa scroll belakangnya

        function openAddModal() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // LOCK SCROLL
            form.action = "{{ route('passwords.store') }}";
            title.innerText = "Simpan Password Baru";
            btn.innerText = "Simpan";
            methodInput.innerHTML = '';
            editNote.classList.add('hidden');
            document.getElementById('inputSiteName').value = '';
            document.getElementById('inputUsername').value = '';
            document.getElementById('inputUrl').value = '';
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputCategory').value = 'Lainnya';
            document.getElementById('inputPassword').required = true;
        }

        function openEditModal(data) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // LOCK SCROLL
            form.action = `/passwords/${data.id}`;
            title.innerText = "Edit Password";
            btn.innerText = "Update";
            methodInput.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            editNote.classList.remove('hidden');
            document.getElementById('inputSiteName').value = data.site_name;
            document.getElementById('inputUsername').value = data.username;
            document.getElementById('inputUrl').value = data.site_url;
            document.getElementById('inputCategory').value = data.category;
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputPassword').required = false;
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // UNLOCK SCROLL
        }

        function openSettingsModal() {
            settingsModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // LOCK SCROLL
        }

        function closeSettingsModal() {
            settingsModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // UNLOCK SCROLL
        }

        function openUnlinkModal(url, email) {
            settingsModal.classList.add('hidden');
            unlinkModal.classList.remove('hidden');
            // Gak perlu lock/unlock body karena transisi dari modal ke modal
            unlinkForm.action = url;
            unlinkEmailSpan.innerText = email;
        }

        function closeUnlinkModal() {
            unlinkModal.classList.add('hidden');
            settingsModal.classList.remove('hidden');
            // Gak perlu unlock karena balik ke settings modal
        }

        function openDeleteModal(url) {
            delForm.action = url;
            delModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // LOCK SCROLL
        }

        function closeDeleteModal() {
            delModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // UNLOCK SCROLL
        }

        // --- NEW: LOGOUT MODAL FUNCTIONS ---
        function openLogoutModal() {
            logoutModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // LOCK SCROLL
        }

        function closeLogoutModal() {
            logoutModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // UNLOCK SCROLL
        }
    </script>
</body>

</html>
