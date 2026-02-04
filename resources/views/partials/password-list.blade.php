@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($passwords as $pass)
        <div
            class="bg-white hover:shadow-lg hover:-translate-y-1 p-6 rounded-2xl border transition-all duration-300 group relative flex flex-col justify-between {{ $pass->is_favorite ? 'border-amber-200 shadow-amber-100 ring-1 ring-amber-100' : 'border-slate-200 shadow-sm hover:border-indigo-200' }}">

            {{-- Header Card --}}
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-4">
                    {{-- Favicon / Icon --}}
                    @if ($pass->site_url)
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 p-1 flex items-center justify-center shrink-0">
                            <img src="https://www.google.com/s2/favicons?domain={{ $pass->site_url }}&sz=64"
                                class="w-8 h-8 object-contain">
                        </div>
                    @else
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl shrink-0">
                            {{ substr($pass->site_name, 0, 1) }}
                        </div>
                    @endif

                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-800 text-lg leading-tight truncate"
                            title="{{ $pass->site_name }}">{{ $pass->site_name }}</h3>
                        <span
                            class="inline-flex mt-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">
                            {{ $pass->category }}
                        </span>
                    </div>
                </div>

                {{-- Top Actions --}}
                <div class="flex gap-1 shrink-0">
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

            {{-- Credentials Area --}}
            <div class="mb-5 space-y-3">
                {{-- Username Row --}}
                <div
                    class="flex justify-between items-center text-xs font-medium bg-slate-50 p-2.5 rounded-lg border border-slate-200 group-hover:border-slate-300 transition">
                    <span class="text-slate-500 truncate mr-2">User: <span
                            class="text-slate-700 font-bold select-all">{{ $pass->username }}</span></span>
                    <button onclick="copyText('{{ $pass->username }}')"
                        class="text-slate-400 hover:text-indigo-600 transition shrink-0" title="Salin Username">
                        <i data-feather="copy" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Password Row (Updated: Eye replaced with Copy) --}}
                <div
                    class="bg-slate-50 rounded-xl p-2.5 flex justify-between items-center border border-slate-200 group-hover:border-slate-300 transition relative">
                    {{-- Hidden Input for Copy Logic --}}
                    <input type="text" id="pass-val-{{ $pass->id }}"
                        value="{{ Crypt::decryptString($pass->password) }}" class="hidden">

                    {{-- Visual Dots --}}
                    <div class="text-slate-500 font-mono text-lg tracking-widest pl-2 select-none">••••••••</div>

                    {{-- Copy Button (Primary Action) --}}
                    <button onclick="copyToClipboard('pass-val-{{ $pass->id }}')"
                        class="p-2 text-indigo-500 hover:text-white hover:bg-indigo-600 transition bg-indigo-50 rounded-lg shadow-sm border border-indigo-100 active:scale-95 flex items-center gap-2"
                        title="Salin Password">
                        <span class="text-[10px] font-bold hidden sm:inline-block">SALIN</span>
                        <i data-feather="copy" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- Footer Actions (Salin button removed) --}}
            <div class="flex justify-between items-center pt-4 border-t border-slate-100 mt-auto">
                {{-- Visit Link --}}
                @if ($pass->site_url)
                    <a href="{{ $pass->site_url }}" target="_blank"
                        class="text-xs font-semibold text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 transition py-1 px-2 rounded hover:bg-indigo-50">
                        <i data-feather="external-link" class="w-3.5 h-3.5"></i> Buka Website
                    </a>
                @else
                    <div></div> {{-- Spacer if no link --}}
                @endif

                {{-- Delete --}}
                <button onclick="openDeleteModal('{{ route('passwords.destroy', $pass->id) }}')"
                    class="text-xs font-semibold text-slate-400 hover:text-red-600 transition flex items-center gap-1.5 py-1 px-2 rounded hover:bg-red-50">
                    <i data-feather="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </div>
        </div>
    @endforeach
</div>
@endif
