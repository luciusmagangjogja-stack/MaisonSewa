@extends('Layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori')

@section('content')
<div class="space-y-6">

    {{-- ── HEADER ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Kategori</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Kelola kategori produk jas</p>
        </div>
@auth
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </a>
        @endif
        @endauth
    </div>

    {{-- ── TABEL ────────────────────────────────────────────── --}}
    <div class="ds-card overflow-hidden">
        @if($categories->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background: var(--secondary)">
                <i data-lucide="tag" class="w-8 h-8" style="color:var(--primary)"></i>
            </div>
            <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada kategori</p>
            <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan kategori produk pertama</p>
            <a href="{{ route('categories.create') }}" class="btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
            </a>
        </div>
        @else
        {{-- Desktop table --}}
        <div class="hidden md:block">
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <colgroup>
                    <col style="width: 20%">
                    <col style="width: 15%">
                    <col style="width: 8%">
                    <col style="width: 10%">
                    <col style="width: 15%">
                    <col style="width: 12%">
                    <col style="width: 20%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-left">Kategori</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-left">Slug</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-center">Icon</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-center">Urutan</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-center">Jumlah Produk</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-center">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 bg-slate-50 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-sand/30">
                    @foreach($categories as $category)
                    <tr class="transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle">
                            <p class="font-semibold text-sm" style="color:var(--text-dark)">{{ $category->name }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle">
                            <span class="text-xs font-mono px-2 py-1 rounded-lg" style="background:var(--secondary); color:var(--text-soft)">
                                {{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle text-center">
                            @if($category->icon)
                            <div class="flex justify-center w-full">
                                <i data-lucide="{{ $category->icon }}" class="w-5 h-5" style="color:var(--primary)"></i>
                            </div>
                            @else
                            <span class="text-xs" style="color:var(--text-soft)">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle text-center">
                            <span class="text-sm font-medium" style="color:var(--text-soft)">{{ $category->sort_order }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle text-center">
                            <div class="flex justify-center">
                            <span class="badge badge-gold">{{ $category->products_count }} produk</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle text-center">
                            <div class="flex justify-center">
                            @if($category->is_active)
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-gray">Nonaktif</span>
                            @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-700 align-middle text-center">
                            <div class="flex items-center justify-center gap-2">
                                @auth
                                    @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color:var(--primary)"></i>
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" id="deleteCategory_{{ $category->id }}" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" onclick="confirmDelete('deleteCategory_{{ $category->id }}', 'kategori {{ $category->name }}')"
                                            class="p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                                    </button>
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden space-y-3">
            @foreach($categories as $category)
            <div class="ds-card border rounded-xl p-4 space-y-3">
                {{-- Top: icon + name + status --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:var(--secondary)">
                        @if($category->icon)
                            <i data-lucide="{{ $category->icon }}" class="w-5 h-5" style="color:var(--primary)"></i>
                        @else
                            <i data-lucide="tag" class="w-5 h-5" style="color:var(--text-soft)"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color:var(--text-dark)">{{ $category->name }}</p>
                    </div>
                    @if($category->is_active)
                        <span class="badge badge-green text-xs">Aktif</span>
                    @else
                        <span class="badge badge-gray text-xs">Nonaktif</span>
                    @endif
                </div>

                {{-- Info row --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--text-soft)">
                    <span class="font-mono px-2 py-0.5 rounded-md" style="background:var(--secondary)">
                        {{ $category->slug }}
                    </span>
                    <span>Urutan: <strong class="font-medium" style="color:var(--text-dark)">{{ $category->sort_order }}</strong></span>
                    <span>{{ $category->products_count }} produk</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-2 border-t" style="border-color:var(--border)">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('categories.edit', $category) }}"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" id="deleteCategoryMobile_{{ $category->id }}" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button"
                                onclick="confirmDelete('deleteCategoryMobile_{{ $category->id }}', 'kategori {{ $category->name }}')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] text-red-600 hover:bg-red-50">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                        </button>
                        @else
                        <a href="{{ route('categories.show', $category) }}"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="eye" class="w-4 h-4"></i> Lihat Detail
                        </a>
                        @endif
                    @else
                        <a href="{{ route('categories.show', $category) }}"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="eye" class="w-4 h-4"></i> Lihat Detail
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
@endsection
