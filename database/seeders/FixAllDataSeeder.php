<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Rental;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FixAllDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Perbaiki gambar produk
        $products = Product::all();
        $photoMap = [
            'Jas Formal Hitam Classic' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=500&fit=crop',
            'Jas Formal Navy Premium' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=400&h=500&fit=crop',
            'Tuxedo Putih Elegant' => 'https://images.unsplash.com/photo-1593030660092-5586280c423e?w=400&h=500&fit=crop',
            'Tuxedo Hitam Modern' => 'https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?w=400&h=500&fit=crop',
            'Jas Wisuda Biru Dongker' => 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=400&h=500&fit=crop',
            'Jas Wisuda Abu-Abu' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop',
        ];
        foreach ($products as $product) {
            if (isset($photoMap[$product->name])) {
                $product->photo = $photoMap[$product->name];
                $product->save();
                $this->command->info("✅ Updated Product: {$product->name}");
            }
        }

        // 2. Generate QR untuk semua rental
        $rentals = Rental::all();
        foreach ($rentals as $rental) {
            if (!$rental->qr_code) {
                $this->generateQrCode($rental);
                $this->command->info("✅ Generated QR: {$rental->invoice_number}");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 SEMUA DATA BERHASIL DIPERBAIKI!');
    }

    private function generateQrCode(Rental $rental): void
    {
        $qrData = route('rentals.scan', $rental->invoice_number);
        $path = 'qrcodes/rentals/' . $rental->invoice_number . '.svg';
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        $svg = QrCode::format('svg')->size(300)->margin(2)->generate($qrData);
        file_put_contents($fullPath, $svg);
        
        $rental->update(['qr_code' => $path]);
    }
}
