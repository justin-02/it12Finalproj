Employee Management & Monitoring — Summary

Overview

This document summarizes the Employee Management and Employee Monitoring enhancements added to the Agrivet Supply System.

Files added/modified

- Migration: `database/migrations/2025_12_11_000003_add_employee_details_and_activity_tables.php` — adds employee fields and activity tables (`session_tracks`, `tasks`, `productivity_metrics`, `employee_performances`).
- Migration: `database/migrations/2025_12_12_000004_update_employee_logs_event_type.php` — converts `employee_logs.event` to `VARCHAR(255)` to allow full request strings.
- Models added: `SessionTrack`, `Task`, `ProductivityMetric`, `EmployeePerformance`, `EmployeeMetricsService` (service).
- Middleware: `LogActivity` — logs requests, updates `last_seen`, and session tracks.
- Controllers: `EmployeeController` — CRUD for employees; AdminController extended with `sessions`, `activityLogs`, `productivityReport`.
- Views: admin employee list and forms, `sessions`, `activity-logs`, `productivity-report`.
- Routes: Admin routes added for employee management, sessions, activity logs, and reports.

How it works

- Activity logging
  - `LogActivity` middleware runs for authenticated routes and writes `EmployeeLog` entries (login/logout are always stored; request logs are stored only after the `employee_logs.event` column is migrated to allow longer values).
  - `SessionTrack` records per-session details (IP, user agent, last activity).

- Task tracking
  - Core operations (helper order creation/submission, cashier order processing, inventory stock-in/stock-out) create `Task` records with `started_at` and `completed_at` timestamps. On completion we update `duration_seconds`.

- Productivity metrics
  - `ProductivityMetric` stores per-user, per-day JSON metrics (transactions count, sales_total, stock_ins, stock_outs, tasks_completed, etc.).
  - `EmployeeMetricsService` provides helpers to record transactions, stock in/out, and completed tasks.

Admin UI

- `Admin -> Employee Monitoring` shows summary and quick links.
- `Admin -> Sessions` shows active sessions and history.
- `Admin -> Activity Logs` shows employee logs with filtering.
- `Admin -> Productivity Report` shows per-user metrics over a date range.

Running migrations

From project root:

```powershell
php artisan migrate
```

Or to apply only the employee migration(s):

```powershell
php artisan migrate --path=database/migrations/2025_12_11_000003_add_employee_details_and_activity_tables.php
php artisan migrate --path=database/migrations/2025_12_12_000004_update_employee_logs_event_type.php
```

Notes and next steps

- Data privacy: request content is trimmed in logs (password fields omitted). Review retention policy for `employee_logs` and `session_tracks`.
- Performance: The middleware writes a log on every request. Consider writing a sampled log, batching, or using a queue for high-traffic deployments.
- Enhancements: Add alerts for anomalous behavior, dashboards with charts, and exportable CSV/PDF reports.
