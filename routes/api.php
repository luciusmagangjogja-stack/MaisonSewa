<?php

use App\Http\Controllers\BroadcastCampaignController;
use Illuminate\Support\Facades\Route;

Route::prefix('worker')->name('worker.')->group(function () {
    Route::post('/opt-out', [BroadcastCampaignController::class, 'handleOptOut'])->name('opt-out');
    Route::get('/daily-limit', [BroadcastCampaignController::class, 'checkDailyLimit'])->name('daily-limit');
    Route::post('/update-status', [BroadcastCampaignController::class, 'updateMessageStatus'])->name('update-status');
});
