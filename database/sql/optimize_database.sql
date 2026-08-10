-- Nandini Membership database maintenance
-- Take a full database backup before running this script.
-- Business records (members, reservations, affiliates, commissions, and payouts)
-- are intentionally preserved.

SET @booking_sync_retention_days = 90;
SET @webhook_log_retention_days = 90;
SET @session_retention_days = 30;
SET @failed_job_retention_days = 90;
SET @finished_batch_retention_days = 30;

-- Preview the records that qualify for deletion.
SELECT 'booking_sync_logs' AS table_name, COUNT(*) AS rows_to_delete
FROM booking_sync_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL @booking_sync_retention_days DAY)
UNION ALL
SELECT 'webhotelier_webhook_logs', COUNT(*)
FROM webhotelier_webhook_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL @webhook_log_retention_days DAY)
UNION ALL
SELECT 'sessions', COUNT(*)
FROM sessions
WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL @session_retention_days DAY))
UNION ALL
SELECT 'failed_jobs', COUNT(*)
FROM failed_jobs
WHERE failed_at < DATE_SUB(NOW(), INTERVAL @failed_job_retention_days DAY)
UNION ALL
SELECT 'job_batches', COUNT(*)
FROM job_batches
WHERE finished_at IS NOT NULL
  AND finished_at < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL @finished_batch_retention_days DAY));

START TRANSACTION;

-- Cache data is disposable and will be rebuilt automatically.
DELETE FROM cache;
DELETE FROM cache_locks;

-- Remove expired browser sessions only.
DELETE FROM sessions
WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL @session_retention_days DAY));

-- Password-reset tokens are short-lived and safe to remove after 24 hours.
DELETE FROM password_reset_tokens
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);

DELETE FROM affiliate_password_reset_tokens
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);

-- Retain recent synchronization and webhook diagnostics.
DELETE FROM booking_sync_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL @booking_sync_retention_days DAY);

DELETE FROM webhotelier_webhook_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL @webhook_log_retention_days DAY);

-- Preserve pending jobs. Remove only old failures and completed batches.
DELETE FROM failed_jobs
WHERE failed_at < DATE_SUB(NOW(), INTERVAL @failed_job_retention_days DAY);

DELETE FROM job_batches
WHERE finished_at IS NOT NULL
  AND finished_at < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL @finished_batch_retention_days DAY));

COMMIT;

-- Reclaim space and refresh index statistics. These statements can briefly lock
-- the listed tables, so run them during a low-traffic period.
OPTIMIZE TABLE
    cache,
    cache_locks,
    sessions,
    password_reset_tokens,
    affiliate_password_reset_tokens,
    booking_sync_logs,
    webhotelier_webhook_logs,
    failed_jobs,
    job_batches;

-- Confirm the remaining row counts.
SELECT 'booking_sync_logs' AS table_name, COUNT(*) AS remaining_rows FROM booking_sync_logs
UNION ALL SELECT 'webhotelier_webhook_logs', COUNT(*) FROM webhotelier_webhook_logs
UNION ALL SELECT 'sessions', COUNT(*) FROM sessions
UNION ALL SELECT 'failed_jobs', COUNT(*) FROM failed_jobs
UNION ALL SELECT 'job_batches', COUNT(*) FROM job_batches;
