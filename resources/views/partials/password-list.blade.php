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
                    {{-- [BARU] Username dengan Tombol Copy --}}
                    <div
                        class="flex justify-between items-center text-xs font-medium bg-slate-900/50 p-2 rounded-lg border border-transparent hover:border-slate-700 transition">
                        <span class="text-slate-500">User: <span
                                class="text-slate-300 select-all">{{ $pass->username }}</span></span>
                        <button onclick="copyText('{{ $pass->username }}')" class="text-slate-500 hover:text-indigo-400"
                            title="Copy Username">
                            <i data-feather="copy" class="w-3 h-3"></i>
                        </button>
                    </div>

                    <div class="bg-black/30 rounded-xl p-3 flex justify-between items-center border border-white/5">
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
