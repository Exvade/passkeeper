<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Slate-300 */
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
                <div class="flex items-center gap-2.5">
                    <div class="bg-indigo-600 text-white p-1.5 rounded-lg shadow-indigo-200 shadow-lg">
                        <i data-feather="shield" class="w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-800">PassKeeper</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-500 hidden sm:block">Hai,
                        {{ Auth::user()->name }}</span>
                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <button onclick="openSettingsModal()"
                        class="text-slate-500 hover:text-indigo-600 transition p-2 hover:bg-indigo-50 rounded-full"
                        title="Pengaturan">
                        <i data-feather="settings" class="w-5 h-5"></i>
                    </button>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-slate-500 hover:text-red-600 transition p-2 hover:bg-red-50 rounded-full"
                            title="Logout">
                            <i data-feather="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
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
                        class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3 py-2.5 rounded-xl font-medium transition flex items-center justify-center shadow-sm"
                        title="Backup CSV">
                        <i data-feather="download" class="w-5 h-5"></i>
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
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition appearance-none font-medium">
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

    {{-- MODAL SETTINGS --}}
    <div id="settingsModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeSettingsModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Akun Terhubung</h2>
                    <button onclick="closeSettingsModal()" class="text-slate-400 hover:text-slate-600"><i
                            data-feather="x" class="w-6 h-6"></i></button>
                </div>
                <div class="space-y-3 mb-6">
                    @foreach (Auth::user()->socialAccounts as $acc)
                        <div
                            class="flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="bg-white shadow-sm p-1.5 rounded-full border border-slate-100">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"
                                        alt="G" class="w-5 h-5">
                                </div>
                                <div>
                                    <p class="text-sm text-slate-800 font-semibold">{{ $acc->email }}</p>
                                    <p class="text-xs text-slate-500">{{ $acc->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if (Auth::user()->socialAccounts->count() > 1)
                                <button
                                    onclick="openUnlinkModal('{{ route('social-accounts.destroy', $acc->id) }}', '{{ $acc->email }}')"
                                    class="text-slate-400 hover:text-red-600 p-2 transition bg-white border border-slate-200 rounded-lg hover:border-red-200 hover:bg-red-50"
                                    title="Putuskan">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            @else
                                <span
                                    class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">UTAMA</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('auth.google') }}"
                    class="flex items-center justify-center gap-2 w-full bg-white text-slate-700 border border-slate-300 font-semibold py-3 rounded-xl transition hover:bg-slate-50">
                    <i data-feather="plus-circle" class="w-4 h-4"></i> Hubungkan Google Lain
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE & UNLINK (STYLE UPDATE) --}}
    <div id="deleteModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-sm p-6 rounded-3xl shadow-2xl text-center relative">
                <div
                    class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 border-4 border-red-100 mb-4 text-red-500">
                    <i data-feather="alert-triangle" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Hapus Permanen?</h3>
                <p class="text-sm text-slate-500 mt-2 px-4">Tindakan ini tidak bisa dibatalkan. Data akan hilang.</p>
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

    <div id="unlinkModalOverlay" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" onclick="closeUnlinkModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white w-full max-w-sm p-6 rounded-3xl shadow-2xl text-center relative">
                <div
                    class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 border-4 border-red-100 mb-4 text-red-500">
                    <i data-feather="shield-off" class="h-6 w-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Putuskan Akun?</h3>
                <p class="text-sm text-slate-500 mt-2">Akses login untuk <br><span id="unlinkEmail"
                        class="text-slate-800 font-bold bg-slate-100 px-1 rounded">email</span> akan dihapus.</p>
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

    {{-- TOAST (Light Mode) --}}
    <div id="toast"
        class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 translate-y-24 opacity-0">
        <div
            class="bg-white border border-slate-100 text-slate-800 px-5 py-4 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] flex items-center gap-4">
            <div class="bg-emerald-100 p-2 rounded-full text-emerald-600"><i data-feather="check"
                    class="w-4 h-4"></i></div>
            <div>
                <h4 class="font-bold text-sm text-slate-900">Berhasil</h4>
                <p id="toast-message" class="text-xs text-slate-500 mt-0.5">Notifikasi.</p>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        // --- 1. LOGIK FAVORITE (AJAX) ---
        async function toggleFavorite(id, btnElement) {
            // [PERBAIKAN] Cari <svg> dulu (karena sudah direplace feather), baru cari <i>
            const icon = btnElement.querySelector('svg') || btnElement.querySelector('i');

            // Optimistic UI Update
            const isActive = icon.classList.contains('star-active');
            if (isActive) {
                icon.classList.remove('star-active');
                icon.classList.add('star-inactive');
            } else {
                icon.classList.add('star-active');
                icon.classList.remove('star-inactive');
            }

            try {
                // Kirim Request ke Server
                const response = await fetch(`/passwords/${id}/favorite`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    showToast(isActive ? 'Dihapus dari Favorite' : 'Ditambahkan ke Favorite!');
                } else {
                    throw new Error();
                }
            } catch (e) {
                // Kalau gagal, balikin lagi warnanya (Rollback)
                if (isActive) {
                    icon.classList.add('star-active');
                    icon.classList.remove('star-inactive');
                } else {
                    icon.classList.remove('star-active');
                    icon.classList.add('star-inactive');
                }
                showToast('Gagal update favorite.');
            }
        }

        // --- 2. LOGIK MODAL ADD/EDIT ---
        const modal = document.getElementById('modalOverlay');
        const form = document.getElementById('modalForm');
        const title = document.getElementById('modalTitle');
        const methodInput = document.getElementById('methodInput');
        const editNote = document.getElementById('editNote');
        const btn = document.getElementById('submitBtn');

        // --- SETTINGS MODAL ---
        const settingsModal = document.getElementById('settingsModalOverlay');

        function openSettingsModal() {
            settingsModal.classList.remove('hidden');
        }

        function closeSettingsModal() {
            settingsModal.classList.add('hidden');
        }

        // --- REALTIME SEARCH (DEBOUNCE) ---
        let debounceTimer;

        function debouncedSearch() {
            // 1. Ambil nilai input
            const query = document.getElementById('searchInput').value;

            // Ambil juga kategori yang sedang aktif (opsional, biar filter gak ilang)
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category') || 'Semua';

            // 2. Clear timer sebelumnya (Reset waktu tunggu)
            clearTimeout(debounceTimer);

            // 3. Set timer baru (Tunggu 500ms sebelum kirim request)
            debounceTimer = setTimeout(() => {
                fetchPasswords(query, category);
            }, 500); // 500ms = setengah detik delay
        }

        async function fetchPasswords(search, category) {
            const container = document.getElementById('password-container');

            // Efek loading tipis (opsional: ubah opacity)
            container.style.opacity = '0.5';

            try {
                // Kirim request ke server via AJAX
                const response = await fetch(`/dashboard?search=${search}&category=${category}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Tandai sebagai AJAX
                    }
                });

                const html = await response.text();

                // Ganti isi container dengan HTML baru dari server
                container.innerHTML = html;

                // PENTING: Render ulang ikon karena konten baru masuk
                feather.replace();

            } catch (error) {
                console.error('Gagal mencari:', error);
            } finally {
                // Balikin opacity
                container.style.opacity = '1';
            }
        }

        // --- UNLINK MODAL ---
        const unlinkModal = document.getElementById('unlinkModalOverlay');
        const unlinkForm = document.getElementById('unlinkForm');
        const unlinkEmailSpan = document.getElementById('unlinkEmail');

        function openUnlinkModal(url, email) {
            settingsModal.classList.add('hidden'); // Tutup settings dulu biar ga numpuk
            unlinkModal.classList.remove('hidden');
            unlinkForm.action = url;
            unlinkEmailSpan.innerText = email;
        }

        function closeUnlinkModal() {
            unlinkModal.classList.add('hidden');
            settingsModal.classList.remove('hidden'); // Buka settings lagi
        }

        function openAddModal() {
            modal.classList.remove('hidden');
            form.action = "{{ route('passwords.store') }}";
            title.innerText = "Simpan Password Baru";
            btn.innerText = "Simpan";
            methodInput.innerHTML = '';
            editNote.classList.add('hidden');
            document.getElementById('inputSiteName').value = '';
            document.getElementById('inputUsername').value = '';
            document.getElementById('inputUrl').value = '';
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputCategory').value = 'Lainnya'; // Default category
            document.getElementById('inputPassword').required = true;
        }

        function openEditModal(data) {
            modal.classList.remove('hidden');
            form.action = `/passwords/${data.id}`;
            title.innerText = "Edit Password";
            btn.innerText = "Update";
            methodInput.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            editNote.classList.remove('hidden');
            document.getElementById('inputSiteName').value = data.site_name;
            document.getElementById('inputUsername').value = data.username;
            document.getElementById('inputUrl').value = data.site_url;
            document.getElementById('inputCategory').value = data.category; // Set category lama
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputPassword').required = false;
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        // --- 3. TOAST & UTILS ---
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').innerText = message;
            toast.classList.remove('translate-y-24', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 3000);
        }

        // Decrypt & Copy (Sama seperti sebelumnya)
        async function revealPassword(id) {
            const inputField = document.getElementById(`pass-${id}`);
            if (inputField.type === 'text' && inputField.value !== '••••••••••••') {
                inputField.value = '••••••••••••';
                return;
            }
            try {
                inputField.value = 'Loading...';
                const res = await fetch(`/passwords/${id}/decrypt`);
                const data = await res.json();
                inputField.value = data.raw_password;
                setTimeout(() => {
                    inputField.value = '••••••••••••';
                }, 10000);
            } catch (e) {
                showToast('Gagal decrypt!');
            }
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
            showToast('Teks berhasil disalin!');
        }

        function copyToClipboard(id) {
            const el = document.getElementById(id);
            if (el.value === '••••••••••••' || el.value === 'Loading...') {
                showToast('Buka password dulu!');
                return;
            }
            el.select();
            navigator.clipboard.writeText(el.value);
            showToast('Password disalin!');
        }

        // Delete Modal Logic
        const delModal = document.getElementById('deleteModalOverlay');
        const delForm = document.getElementById('deleteForm');

        function openDeleteModal(url) {
            delForm.action = url;
            delModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            delModal.classList.add('hidden');
        }
    </script>
</body>

</html>
