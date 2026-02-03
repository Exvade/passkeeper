<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- WAJIB UTK AJAX --}}
    <title>Dashboard - PassKeeper</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* Animasi Bintang */
        .star-active {
            fill: #fbbf24;
            color: #fbbf24;
        }

        /* Kuning Emas */
        .star-inactive {
            fill: none;
            color: #64748b;
        }

        /* Abu-abu */
    </style>
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Brankas Kamu</h1>
                <p class="text-slate-400 text-sm mt-1">Total {{ $passwords->count() }} akun tersimpan aman.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form action="{{ route('dashboard') }}" method="GET" class="relative w-full md:w-64">
                    {{-- Simpan filter kategori saat searching --}}
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <i data-feather="search" class="absolute left-3 top-3 w-4 h-4 text-slate-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aplikasi..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition">
                </form>
                <button onclick="openAddModal()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
                    <i data-feather="plus" class="w-5 h-5"></i> Baru
                </button>
            </div>
        </div>

        {{-- [BARU] Category Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-4 mb-4 no-scrollbar">
            @php $categories = ['Semua', 'Sosmed', 'Pekerjaan', 'Keuangan', 'Hiburan', 'Lainnya']; @endphp
            @foreach ($categories as $cat)
                <a href="{{ route('dashboard', ['category' => $cat, 'search' => request('search')]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap border 
                   {{ request('category') == $cat || (!request('category') && $cat == 'Semua')
                       ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-500/25'
                       : 'bg-slate-900 border-slate-700 text-slate-400 hover:border-slate-500 hover:text-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- Grid Passwords --}}
        @if ($passwords->isEmpty())
            <div class="text-center py-20 bg-slate-900/50 rounded-3xl border border-dashed border-slate-800">
                <p class="text-slate-500">Belum ada data di kategori ini / pencarian tidak ditemukan.</p>
                @if (request('category') || request('search'))
                    <a href="{{ route('dashboard') }}" class="text-indigo-400 mt-2 inline-block hover:underline">Reset
                        Filter</a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($passwords as $pass)
                    <div
                        class="bg-slate-900 hover:bg-slate-800/80 p-5 rounded-2xl border transition group relative shadow-sm flex flex-col justify-between
                        {{ $pass->is_favorite ? 'border-amber-500/30 shadow-amber-500/5' : 'border-slate-800 hover:border-indigo-500/30' }}">

                        {{-- Header Card --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                {{-- Favicon Logic --}}
                                @if ($pass->site_url)
                                    <img src="https://www.google.com/s2/favicons?domain={{ $pass->site_url }}&sz=64"
                                        alt="Icon" class="w-10 h-10 rounded-lg bg-white p-0.5 object-contain">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-bold text-lg border border-slate-700">
                                        {{ substr($pass->site_name, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <h3 class="font-bold text-white text-base leading-tight">{{ $pass->site_name }}
                                    </h3>
                                    {{-- [BARU] Badge Kategori --}}
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                        {{ $pass->category }}
                                    </span>
                                </div>
                            </div>

                            {{-- [BARU] Tombol Favorite (Bintang) --}}
                            <div class="flex gap-1">
                                <button onclick="toggleFavorite({{ $pass->id }}, this)"
                                    class="p-1.5 transition rounded-lg hover:bg-slate-800" title="Favorite">
                                    <i data-feather="star"
                                        class="w-5 h-5 {{ $pass->is_favorite ? 'star-active' : 'star-inactive' }}"></i>
                                </button>
                                <button onclick='openEditModal(@json($pass))'
                                    class="p-1.5 text-slate-600 hover:text-indigo-400 transition rounded-lg hover:bg-slate-800">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Username & Password --}}
                        <div class="mb-4 space-y-2">
                            <p class="text-xs text-slate-500 font-medium">Username: <span
                                    class="text-slate-300">{{ $pass->username }}</span></p>
                            <div
                                class="bg-black/30 rounded-xl p-3 flex justify-between items-center border border-white/5">
                                <input type="text" id="pass-{{ $pass->id }}" value="••••••••••••" readonly
                                    class="bg-transparent border-none text-slate-300 w-full focus:ring-0 text-sm font-mono tracking-wider px-0 py-0">
                                <button onclick="revealPassword({{ $pass->id }})"
                                    class="text-slate-500 hover:text-white transition ml-2">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex justify-between items-center pt-4 border-t border-slate-800 mt-auto">
                            <button onclick="copyToClipboard('pass-{{ $pass->id }}')"
                                class="text-xs font-medium text-slate-400 hover:text-indigo-400 flex items-center gap-1.5 transition">
                                <i data-feather="copy" class="w-3.5 h-3.5"></i> Salin
                            </button>
                            @if ($pass->site_url)
                                <a href="{{ $pass->site_url }}" target="_blank"
                                    class="text-xs font-medium text-slate-500 hover:text-indigo-400 flex items-center gap-1.5 transition">
                                    <i data-feather="external-link" class="w-3.5 h-3.5"></i> Buka
                                </a>
                            @endif
                            <button onclick="openDeleteModal('{{ route('passwords.destroy', $pass->id) }}')"
                                class="text-xs font-medium text-slate-500 hover:text-red-400 transition flex items-center gap-1.5">
                                <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    {{-- MODAL TAMBAH/EDIT --}}
    <div id="modalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-slate-900 border border-slate-700 w-full max-w-md p-6 rounded-2xl shadow-2xl relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-white">Simpan Password</h2>
                    <button onclick="closeModal()" class="text-slate-500 hover:text-white"><i data-feather="x"
                            class="w-6 h-6"></i></button>
                </div>

                <form id="modalForm" action="{{ route('passwords.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div id="methodInput"></div>

                    {{-- [BARU] Input Kategori --}}
                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Kategori</label>
                        <select id="inputCategory" name="category"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition appearance-none">
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
                                class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Aplikasi</label>
                            <input type="text" id="inputSiteName" name="site_name" placeholder="Netflix"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none"
                                required>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Username</label>
                            <input type="text" id="inputUsername" name="username" placeholder="User123"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none"
                                required>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">URL</label>
                        <input type="url" id="inputUrl" name="site_url" placeholder="https://"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Password</label>
                        <input type="text" id="inputPassword" name="password" placeholder="Rahasia..."
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none">
                        <p id="editNote" class="hidden text-xs text-slate-500 mt-1 italic">*Kosongkan jika password
                            tidak berubah.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="submitBtn"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-500/25">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE (Copy Paste yang lama, atau gunakan kode sebelumnya) --}}
    <div id="deleteModalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div
                class="bg-slate-900 border border-slate-700 w-full max-w-sm p-6 rounded-2xl shadow-2xl relative text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-500/10 mb-4">
                    <i data-feather="alert-triangle" class="h-6 w-6 text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Hapus Password?</h3>
                <p class="text-sm text-slate-400 mt-2">Data ini akan hilang selamanya.</p>
                <form id="deleteForm" method="POST" class="mt-6 flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()"
                        class="w-full bg-slate-800 text-slate-300 py-2.5 rounded-xl">Batal</button>
                    <button type="submit"
                        class="w-full bg-red-600 text-white font-bold py-2.5 rounded-xl">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- TOAST (Sama seperti sebelumnya) --}}
    <div id="toast"
        class="fixed bottom-5 right-5 z-50 transform transition-all duration-300 translate-y-24 opacity-0">
        <div
            class="bg-slate-800 border border-slate-700 text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3">
            <div class="bg-emerald-500/20 p-2 rounded-full text-emerald-400"><i data-feather="check"
                    class="w-4 h-4"></i></div>
            <div>
                <h4 class="font-bold text-sm">Berhasil</h4>
                <p id="toast-message" class="text-xs text-slate-400 mt-0.5">Notif.</p>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        // --- 1. LOGIK FAVORITE (AJAX) ---
        async function toggleFavorite(id, btnElement) {
            const icon = btnElement.querySelector('i');
            // Optimistic UI Update (Ubah dulu biar cepat rasanya)
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
                // Kalau gagal, balikin lagi warnanya
                showToast('Gagal update favorite.');
                // Revert UI logic here if needed
            }
        }

        // --- 2. LOGIK MODAL ADD/EDIT ---
        const modal = document.getElementById('modalOverlay');
        const form = document.getElementById('modalForm');
        const title = document.getElementById('modalTitle');
        const methodInput = document.getElementById('methodInput');
        const editNote = document.getElementById('editNote');
        const btn = document.getElementById('submitBtn');

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
