<?php

namespace App\Services;

use App\Models\ProductivityMetric;
use Carbon\Carbon;

class EmployeeMetricsService
{
    public static function recordTransaction(int $userId, float $amount): void
    {
        $date = Carbon::today()->toDateString();
        $metric = ProductivityMetric::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $metrics = $metric->metrics ?? [];
        $metrics['transactions'] = ($metrics['transactions'] ?? 0) + 1;
        $metrics['sales_total'] = round(($metrics['sales_total'] ?? 0) + $amount, 2);
        $metric->metrics = $metrics;
        $metric->save();
    }

    public static function recordStockIn(int $userId, float $quantitySacks): void
    {
        $date = Carbon::today()->toDateString();
        $metric = ProductivityMetric::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $metrics = $metric->metrics ?? [];
        $metrics['stock_ins'] = ($metrics['stock_ins'] ?? 0) + $quantitySacks;
        $metric->metrics = $metrics;
        $metric->save();
    }

    public static function recordStockOut(int $userId, float $quantitySacks): void
    {
        $date = Carbon::today()->toDateString();
        $metric = ProductivityMetric::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $metrics = $metric->metrics ?? [];
        $metrics['stock_outs'] = ($metrics['stock_outs'] ?? 0) + $quantitySacks;
        $metric->metrics = $metrics;
        $metric->save();
    }

    public static function recordTaskCompleted(int $userId, string $taskType, ?int $durationSeconds = null): void
    {
        $date = Carbon::today()->toDateString();
        $metric = ProductivityMetric::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $metrics = $metric->metrics ?? [];
        $metrics['tasks_completed'] = ($metrics['tasks_completed'] ?? 0) + 1;
        $metrics['by_task'] = $metrics['by_task'] ?? [];
        $metrics['by_task'][$taskType] = ($metrics['by_task'][$taskType] ?? 0) + 1;
        if ($durationSeconds) {
            $metrics['avg_task_duration'] = ($metrics['avg_task_duration'] ?? 0) + $durationSeconds;
            // We'll store sum; consumer can divide by tasks_completed to get average
        }
        $metric->metrics = $metrics;
        $metric->save();
    }
}
