<?php

namespace App\Console\Commands;

use App\Models\Broadcast\BroadcastCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledBroadcasts extends Command
{
    protected $signature = 'broadcasts:process-scheduled';
    protected $description = 'Process scheduled broadcast campaigns that are due';

    public function handle(): int
    {
        $now = now();

        $campaigns = BroadcastCampaign::query()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', $now)
            ->limit(10)
            ->get();

        foreach ($campaigns as $campaign) {
            try {
                $campaign->queueMessages();
                $this->info("Processed campaign: {$campaign->name}");
            } catch (\Throwable $e) {
                Log::channel('broadcast')->error('Failed to process scheduled campaign', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to process campaign {$campaign->name}: {$e->getMessage()}");
            }
        }

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled campaigns to process.');
        }

        return self::SUCCESS;
    }
}
