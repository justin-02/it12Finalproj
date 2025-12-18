Deployment Checklist for Agrivet Employee Subsystems

1. Environment
- Ensure `.env` has correct DB connection and APP_KEY set.
- Configure mail settings (for password reset notifications if used).
- Configure queue (Redis or database) and run queue worker:
  - `php artisan queue:work --daemon`

2. Migrations
- Run all migrations:
  - `php artisan migrate`
- Verify new tables: `session_tracks`, `tasks`, `productivity_metrics`, `employee_performances`.

3. Scheduler
- Add cron entry to run Laravel scheduler every minute:
  - `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

4. Storage & Permissions
- Ensure `storage/` and `bootstrap/cache` writable by web user.

5. Caching
- Clear and warm caches:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

6. Queue
- Ensure queue worker is running.

7. Monitoring
- Monitor DB size and number of logs; consider retention policy.

8. Backup
- Run DB backup before large migrations.

9. Post-deploy
- Run `php artisan employees:detect-idle --minutes=10` to initialize idle flags.
- Run `php artisan employees:generate-daily-report` to generate first report.

10. Rollback plan
- Keep DB backup; to rollback code use git and re-run any required rollbacks for migrations.
