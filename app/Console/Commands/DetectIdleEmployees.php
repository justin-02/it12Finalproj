<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SessionTrack;
use Carbon\Carbon;

class DetectIdleEmployees extends Command
{
    protected $signature = 'employees:detect-idle {--minutes=10}';
    protected $description = 'Detect idle employee sessions and flag them';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $threshold = Carbon::now()->subMinutes($minutes);

        $updated = SessionTrack::where('last_activity_at', '<', $threshold)
            ->where('is_idle', false)
            ->update(['is_idle' => true]);

        $this->info("Flagged {$updated} sessions as idle");
        return 0;
    }
}
