@if ($passwords->isEmpty())
    <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300 shadow-sm col-span-full">
        <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
            <i data-feather="inbox" class="w-8 h-8"></i>
        </div>
        <h3 class="text-slate-800 font-semibold text-lg">Tidak ada data</h3>
        <p class="text-slate-500 text-sm mt-1">Belum ada password di kategori ini.</p>
        @if (request('category') || request('search'))
            <a href="{{ route('dashboard') }}"
                class="text-indigo-600 mt-3 inline-block font-medium hover:underline text-sm">Reset Filter</a>
        @endif
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($passwords as $pass)
            <div
                class="bg-white hover:shadow-lg hover:-translate-y-1 p-6 rounded-2xl border transition-all duration-300 group relative flex flex-col justify-between
                        {{ $pass->is_favorite ? 'border-amber-200 shadow-amber-100 ring-1 ring-amber-100' : 'border-slate-200 shadow-sm hover:border-indigo-200' }}">

                {{-- Header Card --}}
                <div class="flex justify-between items-start mb-5">
                    <div class="flex items-center gap-4">
                        {{-- Favicon --}}
                        @if ($pass->site_url)
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 p-1 flex items-center justify-center">
                                <img src="https://www.google.com/s2/favicons?domain={{ $pass->site_url }}&sz=64"
                                    class="w-8 h-8 object-contain">
                            </div>
                        @else
                            <div
                                class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                                {{ substr($pass->site_name, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $pass->site_name }}</h3>
                            <span
                                class="inline-flex mt-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">
                                {{ $pass->category }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-1">
                        <button onclick="toggleFavorite({{ $pass->id }}, this)"
                            class="p-2 transition rounded-lg hover:bg-amber-50 text-slate-400 hover:text-amber-500"
                            title="Favorite">
                            <i data-feather="star"
                                class="w-5 h-5 {{ $pass->is_favorite ? 'star-active' : 'star-inactive' }}"></i>
                        </button>
                        <button onclick='openEditModal(@json($pass))'
                            class="p-2 text-slate-400 hover:text-indigo-600 transition rounded-lg hover:bg-indigo-50">
                            <i data-feather="edit-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                {{-- Credentials --}}
                <div class="mb-5 space-y-3">
                    <div
                        class="flex justify-between items-center text-xs font-medium bg-slate-50 p-2.5 rounded-lg border border-slate-200 group-hover:border-slate-300 transition">
                        <span class="text-slate-500">User: <span
                                class="text-slate-700 font-bold select-all">{{ $pass->username }}</span></span>
                        <button onclick="copyText('{{ $pass->username }}')"
                            class="text-slate-400 hover:text-indigo-600 transition" title="Copy Username">
                            <i data-feather="copy" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div
                        class="bg-slate-50 rounded-xl p-2.5 flex justify-between items-center border border-slate-200 group-hover:border-slate-300 transition">
                        <input type="text" id="pass-{{ $pass->id }}" value="••••••••••••" readonly
                            class="bg-transparent border-none text-slate-600 w-full focus:ring-0 text-sm font-mono tracking-wider px-2">
                        <button onclick="revealPassword({{ $pass->id }})"
                            class="p-1.5 text-slate-400 hover:text-indigo-600 transition hover:bg-white rounded-lg shadow-sm">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-between items-center pt-4 border-t border-slate-100 mt-auto">
                    <button onclick="copyToClipboard('pass-{{ $pass->id }}')"
                        class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 transition py-1 px-2 rounded hover:bg-indigo-50">
                        <i data-feather="copy" class="w-3.5 h-3.5"></i> Salin
                    </button>
                    @if ($pass->site_url)
                        <a href="{{ $pass->site_url }}" target="_blank"
                            class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 transition py-1 px-2 rounded hover:bg-indigo-50">
                            <i data-feather="external-link" class="w-3.5 h-3.5"></i> Buka
                        </a>
                    @endif
                    <button onclick="openDeleteModal('{{ route('passwords.destroy', $pass->id) }}')"
                        class="text-xs font-semibold text-slate-400 hover:text-red-600 transition flex items-center gap-1.5 py-1 px-2 rounded hover:bg-red-50">
                        <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Hapus
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
