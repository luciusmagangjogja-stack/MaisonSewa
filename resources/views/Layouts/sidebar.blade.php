{{--
    layouts/sidebar.blade.php
    ─────────────────────────────────────────────────────────────
    Variabel Alpine dari app.blade.php:
      • sidebarOpen       → desktop collapse/expand  (boolean)
      • sidebarMobileOpen → mobile drawer open/close (boolean)

    CSS class dari app.blade.php:
      • .sidebar           → aside utama
      • .sidebar.collapsed → desktop collapsed (w-64 → w-16)
      • .sidebar.mobile-open → mobile drawer terbuka
      • .sidebar-item / .sidebar-item.active

    Label & tooltip tampil saat:
      • Desktop lebar  : sidebarOpen == true
      • Mobile drawer  : sidebarMobileOpen == true
      → Diringkas sebagai :  showLabel = sidebarOpen || sidebarMobileOpen
        tapi karena ini partial di dalam scope Alpine parent,
        kita cukup pakai x-show="sidebarOpen || sidebarMobileOpen"
--}}

{{-- ─── LOGO & TOGGLE ── --}}
<div class="flex items-center px-4 py-5 border-b border-white/10 flex-shrink-0" style="min-height:72px">
    <div class="flex items-center gap-3 overflow-hidden min-w-0">

        {{-- Icon Crown --}}
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background: linear-gradient(135deg, #D6B98C, #C4A478);">
            <i data-lucide="crown" class="w-5 h-5" style="color:#1E1A16"></i>
        </div>

        {{-- Brand text — tampil saat expanded --}}
        <div x-show="sidebarOpen || sidebarMobileOpen"
             x-transition:enter="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="overflow-hidden">
            <p class="font-playfair font-bold text-white text-base leading-tight whitespace-nowrap">{{ \App\Services\SettingsService::get('app_name', 'SewaJas') }}</p>
            <p class="text-xs whitespace-nowrap" style="color:#D6B98C">{{ \App\Services\SettingsService::get('app_tagline', 'RENTAL JAS') }}</p>
        </div>
    </div>

    {{-- Desktop: tombol collapse --}}
    <button @click="sidebarOpen = !sidebarOpen"
            x-show="sidebarOpen"
            class="ml-auto p-1.5 rounded-lg hover:bg-white/10 transition flex-shrink-0 hidden lg:flex"
            title="Tutup sidebar">
        <i data-lucide="panel-left-close" class="w-4 h-4 text-white/50"></i>
    </button>

    {{-- Mobile: tombol tutup drawer --}}
    <button @click="sidebarMobileOpen = false"
            class="ml-auto p-1.5 rounded-lg hover:bg-white/10 transition flex-shrink-0 lg:hidden">
        <i data-lucide="x" class="w-4 h-4 text-white/50"></i>
    </button>
</div>

{{-- ─── ROLE BADGE ── --}}
@auth
<div x-show="sidebarOpen || sidebarMobileOpen"
     class="px-4 py-3 border-b border-white/10 flex-shrink-0">
    @if(auth()->user()->isSuperAdmin())
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
            <div>
                <p class="text-[11px] font-bold leading-tight" style="color:#D6B98C">SUPER ADMIN</p>
                <p class="text-[10px]" style="color:rgba(255,255,255,0.35)">Akses penuh semua cabang</p>
            </div>
        </div>
    @elseif(auth()->user()->isAdminToko())
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
            <div>
                <p class="text-[11px] font-bold leading-tight" style="color:#60A5FA">ADMIN CABANG</p>
                <p class="text-[10px] truncate" style="color:rgba(255,255,255,0.4)">{{ auth()->user()->branch?->name }}</p>
            </div>
        </div>
    @else
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
            <div>
                <p class="text-[11px] font-bold leading-tight" style="color:#10B981">SALES</p>
                <p class="text-[10px] truncate" style="color:rgba(255,255,255,0.4)">{{ auth()->user()->branch?->name }}</p>
            </div>
        </div>
    @endif
</div>
@endauth

{{-- ════════════════════════════════════════════════
     NAVIGASI UTAMA
═══════════════════════════════════════════════════ --}}
<nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden sidebar-nav">

    {{-- ─── DASHBOARD ─── --}}
    <div class="mb-1">
        <a href="{{ route('dashboard') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('dashboard') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Dashboard</span>
        </a>
    </div>

    {{-- ─── TRANSAKSI (semua role) ─── --}}
    <div x-show="sidebarOpen || sidebarMobileOpen" class="px-3 pt-3 pb-1">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:rgba(214,185,140,0.4)">Transaksi</p>
    </div>
    {{-- Divider tipis saat collapsed desktop --}}
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="mx-3 my-1.5 border-t border-white/10 hidden lg:block"></div>

    <div class="space-y-0.5">
        {{-- Daftar Penyewaan --}}
        <a href="{{ route('rentals.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('rentals.index') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="shirt" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="flex-1 whitespace-nowrap">Penyewaan</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Penyewaan</span>
        </a>
        {{-- Scan QR --}}
        <a href="{{ route('rentals.scan') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('rentals.scan') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="scan" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="flex-1 whitespace-nowrap">Scan QR</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Scan QR</span>
        </a>
    </div>

    {{-- ─── MASTER DATA (semua role) ─── --}}
    <div x-show="sidebarOpen || sidebarMobileOpen" class="px-3 pt-4 pb-1">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:rgba(214,185,140,0.4)">Master Data</p>
    </div>
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="mx-3 my-1.5 border-t border-white/10 hidden lg:block"></div>

    <div class="space-y-0.5">
        <a href="{{ route('customers.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('customers.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Customer</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Customer</span>
        </a>

        <a href="{{ route('products.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('products.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="package" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Produk</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Produk</span>
        </a>

        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('categories.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('categories.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="tag" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Kategori</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Kategori</span>
        </a>
        @endif
    </div>

    {{-- ─── LAPORAN (super admin & admin cabang) ─── --}}
    @unless(auth()->user()->isSales())
    <div x-show="sidebarOpen || sidebarMobileOpen" class="px-3 pt-4 pb-1">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:rgba(214,185,140,0.4)">Laporan</p>
    </div>
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="mx-3 my-1.5 border-t border-white/10 hidden lg:block"></div>

    <div class="space-y-0.5">
        <a href="{{ route('reports.revenue') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('reports.revenue') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="trending-up" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Laporan Pendapatan</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Laporan Pendapatan</span>
        </a>

        <a href="{{ route('reports.transactions') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('reports.transactions') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="receipt" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Laporan Transaksi</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Laporan Transaksi</span>
        </a>

        <a href="{{ route('reports.returns') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('reports.returns') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="package-check" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Laporan Pengembalian</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Laporan Pengembalian</span>
        </a>

        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('reports.stock') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('reports.stock') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="shirt" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Laporan Stok</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Laporan Stok</span>
        </a>
        @endif

        @if(!auth()->user()->isSales())
        <a href="{{ route('points.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('points.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="wallet" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Laporan Poin</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Laporan Poin</span>
        </a>
        @endif
    </div>
    @endunless

    {{-- ─── KOMISI (sales) ─── --}}
    @if(auth()->user()->isSales())
    <div x-show="sidebarOpen || sidebarMobileOpen" class="px-3 pt-4 pb-1">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:rgba(214,185,140,0.4)">Insentif</p>
    </div>
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="mx-3 my-1.5 border-t border-white/10 hidden lg:block"></div>

    <div class="space-y-0.5">
        <a href="{{ route('points.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('points.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="wallet" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Poin Saya</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Poin Saya</span>
        </a>
    </div>
    @endif

    {{-- ─── MANAJEMEN (super admin) ─── --}}
    @if(auth()->user()->isSuperAdmin())
    <div x-show="sidebarOpen || sidebarMobileOpen" class="px-3 pt-4 pb-1">
        <p class="text-[10px] font-bold uppercase tracking-widest" style="color:rgba(214,185,140,0.4)">Manajemen</p>
    </div>
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="mx-3 my-1.5 border-t border-white/10 hidden lg:block"></div>

    <div class="space-y-0.5">
        <a href="{{ route('branches.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('branches.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="building-2" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Kelola Cabang</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Kelola Cabang</span>
        </a>

        <a href="{{ route('users.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('users.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="user-cog" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Kelola Pengguna</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Kelola Pengguna</span>
        </a>

        <a href="{{ route('settings.index') }}"
           class="sidebar-item group relative flex items-center gap-3 px-3 py-2.5 text-sm
                  {{ request()->routeIs('settings.*') ? 'active' : 'text-white/70' }}"
           @click="sidebarMobileOpen = false">
            <i data-lucide="settings" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="sidebarOpen || sidebarMobileOpen" class="whitespace-nowrap">Pengaturan</span>
            <span x-show="!sidebarOpen && !sidebarMobileOpen" class="sidebar-tooltip">Pengaturan</span>
        </a>
    </div>
    @endif

</nav>

{{-- ════════════════════════════════════════════════
     USER FOOTER
═══════════════════════════════════════════════════ --}}
<div class="border-t border-white/10 flex-shrink-0">

    {{-- Expanded — desktop lebar + mobile drawer --}}
    <div x-show="sidebarOpen || sidebarMobileOpen" class="p-3">
        <div class="flex items-center gap-3">
            {{-- Avatar inisial --}}
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                        font-bold text-xs text-white"
                 style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] truncate" style="color:rgba(214,185,140,0.6)">{{ auth()->user()->role }}</p>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="p-1.5 rounded-lg hover:bg-white/10 transition"
                        title="Logout">
                    <i data-lucide="log-out" class="w-3.5 h-3.5 text-white/50"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Collapsed — desktop saja: tombol expand --}}
    <div x-show="!sidebarOpen && !sidebarMobileOpen" class="p-2 hidden lg:block">
        <button @click="sidebarOpen = true"
                class="w-full flex justify-center p-2 rounded-lg hover:bg-white/10 transition"
                title="Buka Sidebar">
            <i data-lucide="panel-left-open" class="w-4 h-4 text-white/50"></i>
        </button>
    </div>
</div>
