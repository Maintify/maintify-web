<x-app-layout>
    @slot('pageTitle', 'User Management')
    @slot('breadcrumb', 'Admin / Users')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">User Management</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Kelola status aktif/nonaktif dan detail informasi pengguna sistem Maintify.</p>
            </div>
        </div>

        {{-- Success / Error Flash Messages --}}
        @if(session('success'))
            <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px;">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search & Filters --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-3">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, email, atau no. telepon..."
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Role Filter --}}
                <div class="w-full md:w-56">
                    <select name="role"
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                        <option value="">Semua Peran</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ $role === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all flex-1 md:flex-initial">
                        Cari
                    </button>
                    @if($search || $role)
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Pengguna</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Peran</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kontak</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($users as $usr)
                            <tr class="hover:bg-zinc-900/30 transition-colors align-middle">
                                {{-- Name / Email --}}
                                <td class="px-6 py-4 font-bold text-zinc-200">
                                    <a href="{{ route('admin.users.show', $usr->id) }}" class="hover:text-red-400 transition-colors">
                                        {{ $usr->name }}
                                    </a>
                                    @if($usr->id === auth()->id())
                                        <span class="ml-1 text-[10px] font-normal px-2 py-0.5 bg-zinc-850 border border-zinc-700 text-zinc-400 rounded">Anda</span>
                                    @endif
                                </td>
                                {{-- Role Badge --}}
                                <td class="px-6 py-4">
                                    @if($usr->isSuperAdmin())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 border border-red-900/50 text-red-400">
                                            Super Admin
                                        </span>
                                    @elseif($usr->isWorkshop())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-950/40 border border-blue-900/50 text-blue-400">
                                            Bengkel Mitra
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                                            Pemilik Kendaraan
                                        </span>
                                    @endif
                                </td>
                                {{-- Contact --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-300">{{ $usr->email }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $usr->phone_number ?? '-' }}</div>
                                </td>
                                {{-- Active status indicator --}}
                                <td class="px-6 py-4">
                                    @if($usr->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500">
                                            <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                {{-- Action buttons --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        {{-- Activate/Deactivate Toggle Button --}}
                                        @if($usr->id !== auth()->id())
                                            <form action="{{ route('admin.users.update', $usr->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin {{ $usr->is_active ? 'nonaktifkan' : 'aktifkan' }} akun ini?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_active" value="{{ $usr->is_active ? 0 : 1 }}">
                                                @if($usr->is_active)
                                                    <button type="submit" class="px-3 py-1.5 bg-red-950/20 hover:bg-red-950/40 border border-red-900/40 text-red-400 hover:text-red-300 text-xs font-semibold rounded-lg transition-all">
                                                        Nonaktifkan
                                                    </button>
                                                @else
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-950/20 hover:bg-emerald-950/40 border border-emerald-900/40 text-emerald-400 hover:text-emerald-300 text-xs font-semibold rounded-lg transition-all">
                                                        Aktifkan
                                                    </button>
                                                @endif
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.users.show', $usr->id) }}" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 text-xs font-semibold rounded-lg transition-all">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Pengguna</p>
                                    <p class="text-zinc-650 text-xs mt-1">Tidak ada akun pengguna yang ditemukan dalam pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
