<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateAllQrSeeder extends Seeder
{
    public function run(): void
    {
        $rentals = Rental::all();
        $count = 0;
        
        foreach ($rentals as $rental) {
            if (!$rental->qr_code) {
                $qrData = route('rentals.scan', $rental->invoice_number);
                $path = 'qrcodes/rentals/' . $rental->invoice_number . '.svg';
                $fullPath = storage_path('app/public/' . $path);
                
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
                
                $svg = QrCode::format('svg')->size(300)->margin(2)->generate($qrData);
                file_put_contents($fullPath, $svg);
                
                $rental->update(['qr_code' => $path]);
                $count++;
                $this->command->info("✅ Generated QR: {$rental->invoice_number}");
            }
        }
        
        $this->command->newLine();
        $this->command->info("🎉 BERHASIL: {$count} QR CODE DI GENERATE!");
    }
}
