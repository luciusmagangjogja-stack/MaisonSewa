<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        // URL gambar dummy dengan warna yang sesuai
        $photoMap = [
            // Jas Hitam - warna hitam
            'Jas Formal Hitam Classic' => 'https://placehold.co/400x500/000000/FFFFFF?text=Jas+Hitam+Classic',
            
            // Jas Navy - warna biru tua
            'Jas Formal Navy Premium' => 'https://placehold.co/400x500/001f3f/FFFFFF?text=Jas+Navy+Premium',
            
            // Tuxedo Putih - warna putih
            'Tuxedo Putih Elegant' => 'https://placehold.co/400x500/ffffff/000000?text=Tuxedo+Putih',
            
            // Tuxedo Hitam - warna hitam
            'Tuxedo Hitam Modern' => 'https://placehold.co/400x500/111111/FFFFFF?text=Tuxedo+Hitam',
            
            // Wisuda Biru - warna biru dongker
            'Jas Wisuda Biru Dongker' => 'https://placehold.co/400x500/003366/FFFFFF?text=Jas+Wisuda+Biru',
            
            // Wisuda Abu - warna abu
            'Jas Wisuda Abu-Abu' => 'https://placehold.co/400x500/888888/FFFFFF?text=Jas+Wisuda+Abu',
        ];

        foreach ($products as $product) {
            if (isset($photoMap[$product->name])) {
                $product->photo = $photoMap[$product->name];
                $product->save();
                $this->command->info("✅ Updated: {$product->name}");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Gambar produk stabil dan sesuai warna!');
    }
}
