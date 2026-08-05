@extends('Layouts.app')
@section('title','Kelola Pengguna')
@section('page-title','Kelola Pengguna')
@section('subtitle','Manajemen akun & hak akses — Super Admin Only')

@section('content')
<div class="space-y-5">

    {{-- Info Role --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([
            ['role'=>'super_admin','label'=>'Super Admin','color'=>'#D6B98C','bg'=>'#D6B98C15','icon'=>'shield-check','desc'=>'Akses penuh semua cabang'],
            ['role'=>'admin_toko', 'label'=>'Admin Toko', 'color'=>'#3B82F6','bg'=>'#3B82F615','icon'=>'store',       'desc'=>'Kelola satu cabang'],
            ['role'=>'sales',      'label'=>'Sales',       'color'=>'#10B981','bg'=>'#10B98115','icon'=>'user-check',  'desc'=>'Transaksi harian saja'],
        ] as $r)
        <div class="card p-4 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:{{ $r['bg'] }}">
                <i data-lucide="{{ $r['icon'] }}" class="w-5 h-5" style="color:{{ $r['color'] }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm" style="color:var(--text-dark)">
                    {{ $roleCounts[$r['role']] ?? 0 }}
                    <span style="color:{{ $r['color'] }}">{{ $r['label'] }}</span>
                </p>
                <p class="text-xs" style="color:var(--text-soft)">{{ $r['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter & Add --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, email..."
                       class="form-input">
            </div>
            <div>
                <select name="role" class="form-input">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="branch" class="form-input">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
            @if(request()->hasAny(['search','role','branch']))
            <a href="{{ route('users.index') }}" class="btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i> Reset
            </a>
            @endif
        </form>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
        @if($users->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--secondary)">
                <i data-lucide="users" class="w-8 h-8" style="color:var(--primary)"></i>
            </div>
            <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada pengguna</p>
            <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan pengguna pertama</p>
            <a href="{{ route('users.create') }}" class="btn-primary">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </a>
        </div>
        @else
        {{-- Desktop table --}}
        <div class="hidden md:block">
            <table class="w-full elegant-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Pengguna</th>
                        <th class="text-left">Role</th>
                        <th class="text-left">Cabang</th>
                        <th class="text-left">Kontak</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="{{ !$user->is_active ? 'opacity-60' : '' }}">
                        <td class="text-xs" style="color:var(--text-soft)">{{ $users->firstItem() + $loop->index }}</td>

                        {{-- Pengguna --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-xl object-cover"
                                     style="outline: 2px solid #ffffff; outline-offset: 0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm" style="color:var(--text-dark)">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                        <span class="badge badge-gold text-[9px] ml-1">Anda</span>
                                        @endif
                                    </p>
                                    <p class="text-xs truncate" style="color:var(--text-soft)">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge text-[11px] px-3 py-1
                                {{ match($user->role) {
                                    'super_admin' => 'badge-gold',
                                    'admin_toko'  => 'badge-blue',
                                    'sales'       => 'badge-green',
                                    default       => 'badge-gray'
                                } }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role ?? '-')) }}
                            </span>
                        </td>

                        <td>
                            @if($user->branch)
                                <p class="text-sm" style="color:var(--text-dark)">{{ $user->branch->name }}</p>
                                <p class="text-xs" style="color:var(--text-soft)">{{ $user->branch->code }}</p>
                            @elseif($user->role === 'super_admin')
                                <span class="text-xs" style="color:var(--primary)">Semua Cabang</span>
                            @else
                                <span class="text-xs text-red-400">Belum dikaitkan</span>
                            @endif
                        </td>

                        <td class="text-sm" style="color:var(--text-soft)">{{ $user->phone ?? '-' }}</td>

                        <td class="text-center">
                            <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit"
                                   style="color:var(--text-soft)">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle', $user) }}" id="toggleUserIndex_{{ $user->id }}" class="hidden">
                                    @csrf @method('PATCH')
                                </form>
                                <button type="button" onclick="confirmAction('toggleUserIndex_{{ $user->id }}', '{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Pengguna', '{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengguna {{ $user->name }}?', '{{ $user->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}', '{{ $user->is_active ? '#ef4444' : '#10b981' }}')"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        style="color:{{ $user->is_active ? '#C0392B' : '#10B981' }}">
                                    <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                </button>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" id="deleteUserIndex_{{ $user->id }}" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" onclick="confirmDelete('deleteUserIndex_{{ $user->id }}', 'pengguna {{ $user->name }}')"
                                        class="p-1.5 rounded-lg hover:bg-red-50 transition-colors"
                                        title="Nonaktifkan/Hapus"
                                        style="color:#C0392B">
                                    <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden space-y-3">
            @foreach($users as $user)
            <div class="card border rounded-xl p-4 space-y-3">
                {{-- Top: avatar + name/email + role badge --}}
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0"
                         style="outline: 2px solid #ffffff; outline-offset: 0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color:var(--text-dark)">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                            <span class="badge badge-gold text-[9px] ml-1">Anda</span>
                            @endif
                        </p>
                        <p class="text-xs truncate" style="color:var(--text-soft)">{{ $user->email }}</p>
                    </div>
                    <span class="badge text-[11px] px-3 py-1 flex-shrink-0
                        {{ match($user->role) {
                            'super_admin' => 'badge-gold',
                            'admin_toko'  => 'badge-blue',
                            'sales'       => 'badge-green',
                            default       => 'badge-gray'
                        } }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role ?? '-')) }}
                    </span>
                </div>

                {{-- Info row --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--text-soft)">
                    <span>
                        @if($user->branch)
                            <strong class="font-medium" style="color:var(--text-dark)">{{ $user->branch->name }}</strong>
                            <span> - {{ $user->branch->code }}</span>
                        @elseif($user->role === 'super_admin')
                            <span style="color:var(--primary)">Semua Cabang</span>
                        @else
                            <span class="text-red-400">Belum dikaitkan</span>
                        @endif
                    </span>
                    <span>{{ $user->phone ?? '-' }}</span>
                    <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }} text-[11px]">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-2 border-t" style="border-color:var(--border)">
                    <a href="{{ route('users.edit', $user) }}"
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                       style="background:var(--secondary); color:var(--text-dark)">
                        <i data-lucide="edit-2" class="w-4 h-4"></i> Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.toggle', $user) }}" id="toggleUserMobile_{{ $user->id }}" class="hidden">
                        @csrf @method('PATCH')
                    </form>
                    <button type="button"
                            onclick="confirmAction('toggleUserMobile_{{ $user->id }}', '{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Pengguna', '{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengguna {{ $user->name }}?', '{{ $user->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}', '{{ $user->is_active ? '#ef4444' : '#10b981' }}')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                            style="background:var(--secondary); color:var(--text-dark)">
                        <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" id="deleteUserMobile_{{ $user->id }}" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button"
                            onclick="confirmDelete('deleteUserMobile_{{ $user->id }}', 'pengguna {{ $user->name }}')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] text-red-600 hover:bg-red-50"
                            title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @if($users->hasPages())
        <div class="px-6 py-4 border-t" style="border-color:var(--border)">
            {{ $users->links('components.pagination') }}
        </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>lucide.createIcons();</script>
@endpush
