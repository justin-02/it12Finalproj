<?php

namespace App\Jobs;

use App\Models\EmployeeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteEmployeeLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        try {
            EmployeeLog::safeCreate($this->data);
        } catch (\Throwable $e) {
            // Fallback: write to laravel log if DB fails
            \Log::error('WriteEmployeeLog failed: '.$e->getMessage(), ['data' => $this->data]);
        }
    }
}
