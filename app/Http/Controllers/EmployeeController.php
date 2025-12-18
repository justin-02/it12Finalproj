<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->orderBy('name')->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'position' => 'nullable|string',
            'department' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'position' => $data['position'] ?? null,
            'department' => $data['department'] ?? null,
            'hire_date' => $data['hire_date'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string',
            'position' => 'nullable|string',
            'department' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ]);

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }

    public function toggleStatus(User $employee)
    {
        $employee->is_active = ! $employee->is_active;
        $employee->save();

        return redirect()->back()->with('success', 'Employee status updated');
    }

    public function resetPassword(Request $request, User $employee)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $employee->password = Hash::make($request->password);
        $employee->save();

        return redirect()->back()->with('success', 'Password reset successfully');
    }

    public function show(User $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    // Export employees as CSV
    public function export(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->orderBy('name')->get();

        $filename = 'employees-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['id', 'name', 'email', 'phone', 'role', 'position', 'department', 'hire_date', 'is_active', 'created_at'];

        $callback = function() use ($employees, $columns) {
            $file = fopen('php://output', 'w');+
            fputcsv($file, $columns);

            foreach ($employees as $e) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = data_get($e, $col);
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
