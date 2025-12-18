<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductivityMetric;
use Illuminate\Support\Facades\Storage;

class GenerateDailyProductivityReport extends Command
{
    protected $signature = 'employees:generate-daily-report';
    protected $description = 'Generate daily productivity report (CSV) and store in storage/reports';

    public function handle(): int
    {
        $today = now()->toDateString();
        $metrics = ProductivityMetric::where('date', $today)->with('user')->get();

        $rows = [];
        foreach ($metrics as $m) {
            $rows[] = [
                'date' => $m->date,
                'user_id' => $m->user_id,
                'user' => $m->user?->name ?? '',
                'metrics' => json_encode($m->metrics),
            ];
        }

        $csv = "date,user_id,user,metrics\n";
        foreach ($rows as $r) {
            $csv .= "{$r['date']},{$r['user_id']},\"{$r['user']}\",\"{$r['metrics']}\"\n";
        }

        $path = "reports/productivity-{$today}.csv";
        Storage::put($path, $csv);

        $this->info("Saved report to storage/{$path}");
        return 0;
    }
}
