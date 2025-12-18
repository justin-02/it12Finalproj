<?php
// app/Http/Controllers/EmployeeMonitoringController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeAttendance;
use App\Models\WorkActivity;
use App\Models\PerformanceEvaluation;
use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeMonitoringController extends Controller
{
    public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        // Get all employees with their current status
        $employees = User::where('role', '!=', 'admin')
            ->with(['todayAttendance', 'schedules' => function($query) use ($today) {
                $query->where('date', $today);
            }])
            ->get()
            ->map(function($user) {
                $user->current_session = $user->currentSession;
                $user->today_schedule = $user->schedules->first();
                return $user;
            });

        // Today's attendance summary
        $todayAttendance = EmployeeAttendance::with('user')
            ->whereDate('date', $today)
            ->get();

        $attendanceSummary = [
            'total_employees' => User::where('role', '!=', 'admin')->count(),
            'clocked_in' => $todayAttendance->whereNotNull('clock_in')->count(),
            'currently_online' => $employees->where('is_online', true)->count(),
            'on_time' => $todayAttendance->where('status', 'present')->count(),
            'late' => $todayAttendance->where('status', 'late')->count(),
            'absent' => $todayAttendance->where('status', 'absent')->count(),
        ];

        // Recent activities (login/logout)
        $recentActivities = WorkActivity::with('user')
            ->whereIn('activity_type', ['system_login', 'system_logout'])
            ->orderBy('start_time', 'desc')
            ->take(15)
            ->get();

        // Performance alerts
        $performanceAlerts = $this->getPerformanceAlerts();

        return view('admin.employee-monitoring', compact(
            'employees',
            'todayAttendance', 
            'attendanceSummary', 
            'recentActivities',
            'performanceAlerts'
        ));
    }

    // Add this method to get real-time user status
    public function getUserStatus($id)
    {
        $user = User::with(['todayAttendance', 'workActivities' => function($query) {
            $query->whereDate('start_time', today())
                  ->orderBy('start_time', 'desc')
                  ->take(5);
        }])->findOrFail($id);

        $status = [
            'user' => $user,
            'is_online' => $user->is_online,
            'current_session' => $user->currentSession,
            'today_attendance' => $user->todayAttendance,
            'recent_activities' => $user->workActivities,
            'login_count' => $user->login_count,
            'last_login' => $user->last_login_at ? $user->last_login_at->format('M d, Y H:i:s') : 'Never',
            'last_logout' => $user->last_logout_at ? $user->last_logout_at->format('M d, Y H:i:s') : 'Never',
        ];

        return response()->json($status);
    }


}