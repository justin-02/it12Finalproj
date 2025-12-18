<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeLog;
use App\Models\SessionTrack;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();

            $sessionId = $request->session() ? $request->session()->getId() : null;

            // Update last seen
            $user->last_seen = now();
            $user->saveQuietly();

            // Update or create session track (only if migration table exists)
            if (Schema::hasTable('session_tracks')) {
                $sessionId = $request->session()->getId();
                $track = SessionTrack::firstOrNew([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                ]);
                $track->ip_address = $request->ip();
                $track->user_agent = $request->userAgent();
                $track->last_activity_at = now();
                if (! $track->started_at) {
                    $track->started_at = now();
                }
                $track->save();
            }

            // Create a lightweight employee log for the request only if the
            // `employee_logs.event` column accepts arbitrary text (not enum).
            if (Schema::hasTable('employee_logs')) {
                try {
                    $column = DB::selectOne("SHOW COLUMNS FROM `employee_logs` WHERE Field='event'");
                } catch (\Throwable $e) {
                    $column = null;
                }

                $isEnum = false;
                if ($column && isset($column->Type)) {
                    $isEnum = str_starts_with(strtolower($column->Type), 'enum(');
                }

                // If event column is not enum (or couldn't detect), store full request string.
                if (! $isEnum) {
                        $logData = [
                            'user_id' => $user->id,
                            'event' => $request->method() . ' ' . $request->path(),
                            'logged_at' => now(),
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'session_id' => $sessionId,
                            'context' => [
                                'input' => method_exists($request, 'except') ? $request->except(['password', 'password_confirmation']) : []
                            ]
                        ];

                        // Dispatch to queue if available, otherwise write sync
                        if (class_exists(\App\Jobs\WriteEmployeeLog::class)) {
                            dispatch(new \App\Jobs\WriteEmployeeLog($logData));
                        } else {
                            try {
                                EmployeeLog::safeCreate($logData);
                            } catch (\Throwable $e) {
                                \Log::error('Failed to write EmployeeLog', ['error' => $e->getMessage()]);
                            }
                        }
                }
                // If event is enum (login/logout) we skip logging generic requests to avoid truncation warnings.
            }
        }

        return $response;
    }
}
