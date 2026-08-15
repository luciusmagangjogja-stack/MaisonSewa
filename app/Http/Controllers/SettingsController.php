<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->orderBy('key')->get()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });

        $response = response()->view('settings.index', compact('settings'));
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fine_per_day'         => 'required|numeric|min:0',
            'rental_duration_days' => 'required|numeric|min:0',
            'company_tagline'      => 'nullable|string|max:150',
            'company_address'      => 'nullable|string|max:255',
            'company_phone'        => 'nullable|string|max:30',
            'company_email'        => 'nullable|email|max:100',
            'company_website'      => 'nullable|string|max:150',
            'app_name'             => 'nullable|string|max:50',
            'app_tagline'          => 'nullable|string|max:100',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account'         => 'nullable|string|max:50',
            'bank_holder'          => 'nullable|string|max:100',
        ]);

        $textFields = [
            'fine_per_day', 'rental_duration_days', 'company_tagline',
            'company_address', 'company_phone', 'company_email', 'company_website',
            'app_name', 'app_tagline', 'bank_name', 'bank_account', 'bank_holder',
        ];

        foreach ($textFields as $field) {
            if (!$request->has($field)) {
                continue;
            }

            $value = $validated[$field] ?? null;

            if ($value === null && DB::table('settings')->where('key', $field)->exists()) {
                continue;
            }

            DB::table('settings')->updateOrInsert(['key' => $field], ['value' => $value, 'updated_at' => now(), 'created_at' => now()]);
        }

        if ($request->hasFile('company_logo') || $request->hasFile('app_logo')) {
            $sourceField = $request->hasFile('company_logo') ? 'company_logo' : 'app_logo';

            $request->validate([
                $sourceField => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            $sourceFile = $request->file($sourceField);
            $request->files->set('app_logo', $sourceFile);

            $this->handleFileUpload($request, 'app_logo', 'settings/app-logos');

            $request->files->remove('app_logo');
        }

        if ($request->hasFile('qris_image')) {
            $request->validate([
                'qris_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=200,min_height=200',
            ]);
            $this->handleFileUpload($request, 'qris_image', 'settings/qris');
        }

        \App\Services\SettingsService::forget();

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    private function handleFileUpload(Request $request, string $field, string $diskPath): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $oldPath = DB::table('settings')->where('key', $field)->value('value');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $newPath = $request->file($field)->store($diskPath, 'public');
        DB::table('settings')->updateOrInsert(['key' => $field], ['value' => $newPath, 'updated_at' => now(), 'created_at' => now()]);
    }
}


