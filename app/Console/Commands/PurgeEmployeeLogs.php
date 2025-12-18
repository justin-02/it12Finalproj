<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeLog;
use App\Models\SessionTrack;
use Carbon\Carbon;

class PurgeEmployeeLogs extends Command
{
    protected $signature = 'employees:purge-logs {--days=90}';
    protected $description = 'Purge old employee logs and session tracks according to retention policy';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $threshold = Carbon::now()->subDays($days);

        $logsDeleted = EmployeeLog::where('logged_at', '<', $threshold)->delete();
        $sessionsDeleted = SessionTrack::where('created_at', '<', $threshold)->delete();

        $this->info("Deleted {$logsDeleted} employee logs and {$sessionsDeleted} old sessions");
        return 0;
    }
}
