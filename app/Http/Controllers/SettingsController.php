<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->orderBy('key')->get();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fine_per_day'            => 'required|numeric|min:0',
            'rental_duration_days'   => 'required|numeric|min:0',
        ]);

        $finePerDay = (int) $validated['fine_per_day'];
        $durationDays = (int) $validated['rental_duration_days'];

        DB::table('settings')->updateOrInsert(['key' => 'fine_per_day'], ['value' => $finePerDay]);
        DB::table('settings')->updateOrInsert(['key' => 'rental_duration_days'], ['value' => $durationDays]);

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

