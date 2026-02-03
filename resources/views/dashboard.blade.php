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

    {{-- Navbar (SAMA SEPERTI SEBELUMNYA) --}}
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

        {{-- Header Section & Search (SAMA SEPERTI SEBELUMNYA) --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Brankas Kamu</h1>
                <p class="text-slate-400 text-sm mt-1">Total {{ $passwords->count() }} akun tersimpan aman.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form action="{{ route('dashboard') }}" method="GET" class="relative w-full md:w-64">
                    <i data-feather="search" class="absolute left-3 top-3 w-4 h-4 text-slate-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aplikasi..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition">
                </form>
                {{-- [UBAH] Tombol Tambah memanggil fungsi openAddModal --}}
                <button onclick="openAddModal()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-2">
                    <i data-feather="plus" class="w-5 h-5"></i> Baru
                </button>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div id="flash-msg"
                class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2"><i data-feather="check-circle"
                        class="w-5 h-5"></i><span>{{ session('success') }}</span></div>
                <button onclick="document.getElementById('flash-msg').remove()"
                    class="text-emerald-400 hover:text-white"><i data-feather="x" class="w-4 h-4"></i></button>
            </div>
        @endif

        {{-- Grid Passwords --}}
        @if ($passwords->isEmpty())
            {{-- Empty State (Kode lama kamu simpan disini) --}}
            <div class="text-center py-20 bg-slate-900/50 rounded-3xl border border-dashed border-slate-800">
                <p class="text-slate-500">Belum ada data / tidak ditemukan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($passwords as $pass)
                    <div
                        class="bg-slate-900 hover:bg-slate-800/80 p-5 rounded-2xl border border-slate-800 hover:border-indigo-500/30 transition group relative shadow-sm">

                        {{-- [BARU] Header Card dengan Favicon Otomatis --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">

                                {{-- Logic Favicon: Kalau ada URL, ambil dari Google. Kalau tidak, pakai inisial. --}}
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
                                    <h3 class="font-bold text-white text-base leading-tight">{{ $pass->site_name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $pass->username }}</p>
                                </div>
                            </div>

                            {{-- [BARU] Tombol Edit (Pensil) --}}
                            <div class="flex gap-2">
                                <button onclick='openEditModal(@json($pass))'
                                    class="text-slate-600 hover:text-indigo-400 transition" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>

                                @if ($pass->site_url)
                                    <a href="{{ $pass->site_url }}" target="_blank"
                                        class="text-slate-600 hover:text-indigo-400 transition">
                                        <i data-feather="external-link" class="w-4 h-4"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Password Field (Sama seperti sebelumnya) --}}
                        <div class="bg-black/30 rounded-xl p-3 flex justify-between items-center border border-white/5">
                            <input type="text" id="pass-{{ $pass->id }}" value="••••••••••••" readonly
                                class="bg-transparent border-none text-slate-300 w-full focus:ring-0 text-sm font-mono tracking-wider px-0 py-0">
                            <button onclick="revealPassword({{ $pass->id }})"
                                class="text-slate-500 hover:text-white transition ml-2">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-800">
                            <button onclick="copyToClipboard('pass-{{ $pass->id }}')"
                                class="text-xs font-medium text-slate-400 hover:text-indigo-400 flex items-center gap-1.5 transition">
                                <i data-feather="copy" class="w-3.5 h-3.5"></i> Salin
                            </button>

                            {{-- Tombol Delete Baru --}}
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

    {{-- [BARU] MODAL PINTAR (Bisa Add / Edit) --}}
    <div id="modalOverlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div
                class="bg-slate-900 border border-slate-700 w-full max-w-md p-6 rounded-2xl shadow-2xl relative transform transition-all scale-100">

                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-white">Simpan Password Baru</h2>
                    <button onclick="closeModal()" class="text-slate-500 hover:text-white"><i data-feather="x"
                            class="w-6 h-6"></i></button>
                </div>

                <form id="modalForm" action="{{ route('passwords.store') }}" method="POST" class="space-y-4">
                    @csrf
                    {{-- Input Hidden untuk Method PUT saat Edit --}}
                    <div id="methodInput"></div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Nama
                            Aplikasi</label>
                        <input type="text" id="inputSiteName" name="site_name" placeholder="Misal: Netflix"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                            required>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Username /
                            Email</label>
                        <input type="text" id="inputUsername" name="username" placeholder="user@email.com"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">URL
                            (Opsional)</label>
                        <input type="url" id="inputUrl" name="site_url" placeholder="https://"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">Password</label>
                        <input type="text" id="inputPassword" name="password" placeholder="Masukkan password..."
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition">
                        <p id="editNote" class="hidden text-xs text-slate-500 mt-1 italic">*Kosongkan jika tidak
                            ingin mengganti password lama.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="submitBtn"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-500/25">Simpan
                            ke Brankas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI DELETE --}}
    <div id="deleteModalOverlay" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()">
        </div>

        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-slate-900 border border-slate-700 w-full max-w-sm p-6 rounded-2xl shadow-2xl relative">

                {{-- Icon Warning Besar --}}
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-500/10 mb-4">
                    <i data-feather="alert-triangle" class="h-6 w-6 text-red-500"></i>
                </div>

                <div class="text-center">
                    <h3 class="text-lg font-bold text-white">Hapus Password Ini?</h3>
                    <p class="text-sm text-slate-400 mt-2">
                        Tindakan ini tidak bisa dibatalkan. Data akun ini akan hilang selamanya dari brankas kamu.
                    </p>
                </div>

                {{-- Form Delete (Action-nya akan diisi via JS) --}}
                <form id="deleteForm" method="POST" class="mt-6 flex gap-3">
                    @csrf
                    @method('DELETE')

                    <button type="button" onclick="closeDeleteModal()"
                        class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-2.5 rounded-xl transition border border-slate-700">
                        Batal
                    </button>

                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-red-500/20">
                        Ya, Hapus
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION COMPONENT --}}
    <div id="toast"
        class="fixed bottom-5 right-5 z-50 transform transition-all duration-300 translate-y-24 opacity-0">
        <div
            class="bg-slate-800 border border-slate-700 text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3">
            <div class="bg-emerald-500/20 p-2 rounded-full text-emerald-400">
                <i data-feather="check" class="w-4 h-4"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm">Berhasil</h4>
                <p id="toast-message" class="text-xs text-slate-400 mt-0.5">Pesan notifikasi.</p>
            </div>
        </div>
    </div>

    {{-- Script Logic --}}
    <script>
        feather.replace();
        const modal = document.getElementById('modalOverlay');
        const form = document.getElementById('modalForm');
        const title = document.getElementById('modalTitle');
        const methodInput = document.getElementById('methodInput');
        const editNote = document.getElementById('editNote');
        const btn = document.getElementById('submitBtn');
        const deleteModal = document.getElementById('deleteModalOverlay');
        const deleteForm = document.getElementById('deleteForm');

        // --- LOGIK TOAST (BARU) ---
        function showToast(message) {
            const toast = document.getElementById('toast');
            const msgElement = document.getElementById('toast-message');

            // Set pesan
            msgElement.innerText = message;

            // Munculkan (Hapus class hide, tambah class show)
            toast.classList.remove('translate-y-24', 'opacity-0');

            // Hilangkan otomatis setelah 3 detik
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 3000);
        }

        // Fungsi Buka Modal TAMBAH
        function openAddModal() {
            modal.classList.remove('hidden');
            form.action = "{{ route('passwords.store') }}"; // Reset URL ke Store
            title.innerText = "Simpan Password Baru";
            btn.innerText = "Simpan ke Brankas";
            methodInput.innerHTML = ''; // Hapus method PUT
            editNote.classList.add('hidden'); // Sembunyikan catatan

            // Reset isi form
            document.getElementById('inputSiteName').value = '';
            document.getElementById('inputUsername').value = '';
            document.getElementById('inputUrl').value = '';
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputPassword').required = true; // Password wajib kalau baru
        }

        // Fungsi Buka Modal EDIT (Menerima data JSON dari tombol)
        function openEditModal(data) {
            modal.classList.remove('hidden');
            form.action = `/passwords/${data.id}`; // Ubah URL ke Update ID
            title.innerText = "Edit Password";
            btn.innerText = "Update Data";

            // Tambahkan method PUT karena HTML form cuma support GET/POST
            methodInput.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            editNote.classList.remove('hidden'); // Munculkan catatan

            // Isi form dengan data lama
            document.getElementById('inputSiteName').value = data.site_name;
            document.getElementById('inputUsername').value = data.username;
            document.getElementById('inputUrl').value = data.site_url;
            document.getElementById('inputPassword').value = ''; // Password dikosongkan
            document.getElementById('inputPassword').required = false; // Password opsional kalau edit
        }

        function openDeleteModal(actionUrl) {
            // 1. Set Action URL form delete sesuai ID yang diklik
            deleteForm.action = actionUrl;

            // 2. Munculkan Modal
            deleteModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
        }

        // Tutup modal delete pakai tombol ESC juga
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeDeleteModal();
                closeModal(); // Tutup modal add/edit juga kalau terbuka
            }
        });

        function closeModal() {
            modal.classList.add('hidden');
        }

        // ... (Fungsi Decrypt & Copy Paste tetap sama seperti sebelumnya) ...
        async function revealPassword(id) {
            const inputField = document.getElementById(`pass-${id}`);
            if (inputField.type === 'text' && inputField.value !== '••••••••••••') {
                inputField.value = '••••••••••••';
                return;
            }
            try {
                inputField.value = 'Loading...';
                const response = await fetch(`/passwords/${id}/decrypt`);
                const data = await response.json();
                inputField.value = data.raw_password;
                setTimeout(() => {
                    inputField.value = '••••••••••••';
                }, 10000);
            } catch (error) {
                showToast('Gagal mendekripsi password!');
            }
        }

        function copyToClipboard(elementId) {
            const copyText = document.getElementById(elementId);
            if (copyText.value === '••••••••••••' || copyText.value === 'Loading...') {
                showToast('Buka (klik mata) dulu sebelum copy!');
                return;
            }
            copyText.select();
            navigator.clipboard.writeText(copyText.value);
            showToast('Password berhasil disalin ke clipboard!');
        }
    </script>
</body>

</html>
