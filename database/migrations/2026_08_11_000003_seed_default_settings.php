<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'company_name', 'value' => 'SewaJas'],
            ['key' => 'company_tagline', 'value' => 'Premium Suit Rental'],
            ['key' => 'company_address', 'value' => ''],
            ['key' => 'company_phone', 'value' => ''],
            ['key' => 'company_email', 'value' => 'support@sewajas.id'],
            ['key' => 'company_website', 'value' => 'www.sewajas.id'],
            ['key' => 'company_logo', 'value' => null],
            ['key' => 'app_logo', 'value' => null],
            ['key' => 'app_name', 'value' => 'SewaJas'],
            ['key' => 'app_tagline', 'value' => 'RENTAL JAS'],
            ['key' => 'qris_image', 'value' => null],
            ['key' => 'bank_name', 'value' => ''],
            ['key' => 'bank_account', 'value' => ''],
            ['key' => 'bank_holder', 'value' => ''],
            ['key' => 'fine_per_day', 'value' => '10000'],
            ['key' => 'rental_duration_days', 'value' => '3'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $keys = [
            'company_name', 'company_tagline', 'company_address', 'company_phone',
            'company_email', 'company_website', 'company_logo', 'app_logo',
            'app_name', 'app_tagline', 'qris_image', 'bank_name',
            'bank_account', 'bank_holder',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
