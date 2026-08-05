<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────
        // CABANG
        // ─────────────────────────────────────
        $branchPusat = Branch::create([
            'name'     => 'Toko Pusat - Yogyakarta',
            'code'     => 'YGY01',
            'address'  => 'Jl. Malioboro No. 88, Yogyakarta',
            'phone'    => '0274-123456',
            'email'    => 'pusat@jasrental.id',
            'city'     => 'Yogyakarta',
            'province' => 'DI Yogyakarta',
            'is_active'=> true,
        ]);

        $branchSolo = Branch::create([
            'name'     => 'Cabang Solo',
            'code'     => 'SLO01',
            'address'  => 'Jl. Slamet Riyadi No. 45, Solo',
            'phone'    => '0271-654321',
            'email'    => 'solo@jasrental.id',
            'city'     => 'Surakarta',
            'province' => 'Jawa Tengah',
            'is_active'=> true,
        ]);

        // ─────────────────────────────────────
        // USERS (ganti assignRole() → kolom role)
        // ─────────────────────────────────────

        User::create([
            'name'      => 'Super Administrator',
            'email'     => 'superadmin@jasrental.id',
            'password'  => Hash::make('password'),
            'phone'     => '081234567890',
            'role'      => 'super_admin',   // ← langsung isi kolom
            'branch_id' => null,
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Admin Pusat Yogya',
            'email'     => 'admin.pusat@jasrental.id',
            'password'  => Hash::make('password'),
            'phone'     => '081298765432',
            'role'      => 'admin_toko',
            'branch_id' => $branchPusat->id,
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Admin Cabang Solo',
            'email'     => 'admin.solo@jasrental.id',
            'password'  => Hash::make('password'),
            'phone'     => '082187654321',
            'role'      => 'admin_toko',
            'branch_id' => $branchSolo->id,
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'budi.santoso@jasrental.id',
            'password'  => Hash::make('password'),
            'phone'     => '085312345678',
            'role'      => 'sales',
            'branch_id' => $branchPusat->id,
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Andi Pratama',
            'email'     => 'andi.pratama@jasrental.id',
            'password'  => Hash::make('password'),
            'phone'     => '085398765432',
            'role'      => 'sales',
            'branch_id' => $branchSolo->id,
            'is_active' => true,
        ]);

        // ─────────────────────────────────────
        // KATEGORI
        // ─────────────────────────────────────
        $categories = [
            ['name' => 'Jas Formal',    'slug' => 'jas-formal',    'icon' => 'briefcase',      'sort_order' => 1],
            ['name' => 'Tuxedo',        'slug' => 'tuxedo',        'icon' => 'award',          'sort_order' => 2],
            ['name' => 'Jas Wisuda',    'slug' => 'jas-wisuda',    'icon' => 'graduation-cap', 'sort_order' => 3],
            ['name' => 'Kebaya',        'slug' => 'kebaya',        'icon' => 'sparkles',       'sort_order' => 4],
            ['name' => 'Rompi',         'slug' => 'rompi',         'icon' => 'layers',         'sort_order' => 5],
            ['name' => 'Sepatu',        'slug' => 'sepatu',        'icon' => 'footprints',     'sort_order' => 6],
            ['name' => 'Aksesoris',     'slug' => 'aksesoris',     'icon' => 'watch',          'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        // ─────────────────────────────────────
        // Now call ERP Data Seeder
        // ─────────────────────────────────────
        $this->call(ErpDataSeeder::class);

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║      JasRental — Data Awal Berhasil     ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info('║  AKUN LOGIN:                             ║');
        $this->command->info('║  superadmin@jasrental.id  → super_admin  ║');
        $this->command->info('║  admin.pusat@jasrental.id → admin_toko   ║');
        $this->command->info('║  admin.solo@jasrental.id  → admin_toko   ║');
        $this->command->info('║  budi.santoso@jasrental.id → sales       ║');
        $this->command->info('║  andi.pratama@jasrental.id → sales       ║');
        $this->command->info('║  Password semua: password                ║');
        $this->command->info('╚══════════════════════════════════════════╝');
    }
}
